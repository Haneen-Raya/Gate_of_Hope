<?php

namespace Modules\Programs\Services;

use App\Services\LoggerService;
use Illuminate\Support\Facades\DB;
use Modules\Programs\Models\Program;
use Illuminate\Support\Facades\Cache;
use Modules\Programs\Enums\Program\ProgramStatus;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class ProgramService
 * * Orchestrates the business logic for the Programs module.
 * This service acts as an intermediary between the Controller and the Database/Cache layers,
 * ensuring that all operations adhere to domain rules and system integrity.
 * * Key Features:
 * - Transactional Integrity: Uses DB transactions for critical persistence.
 * - Tagged Caching: Implements granular cache management for high performance.
 * - State Management: Validates Enum-based status transitions.
 * - Audit Trail: Integrates with LoggerService for comprehensive event tracking.
 * * @package Modules\Programs\Services
 * @author Your Name/Team
 * @version 1.0.0
 */
class ProgramService
{
    /** * @var int Cache Time-To-Live in seconds (1 Hour).
     */
    const CACHE_TTL = 3600;

    /**
     * ProgramService constructor.
     * * @param LoggerService $logger Service handled via Dependency Injection for auditing events.
     */
    public function __construct(protected LoggerService $logger)
    {
    }

    /**
     * Retrieve a paginated list of programs with search and status filters.
     * * Utilizes MD5-based cache keys and tags for efficient list invalidation.
     * * @param array $filters Criteria including 'search', 'status', and 'per_page'.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listPrograms(array $filters)
    {
        $cacheKey = 'programs_list_' . md5(serialize($filters));

        return Cache::tags(['programs_list'])->remember($cacheKey, self::CACHE_TTL, function () use ($filters) {
            return Program::query()
                ->with(['issueCategory', 'creator'])
                ->when($filters['search'] ?? null, fn($q, $search) => $q->whereNameLike($search))
                ->when($filters['status'] ?? null, fn($q, $status) => $q->whereStatus($status))
                ->latest()
                ->paginate($filters['per_page'] ?? 15);
        });
    }

    /**
     * Persist a new Program instance within a database transaction.
     * * Automatically logs the creation event for auditing purposes.
     * * @param array $data Validated attributes for the new program.
     * @return Program
     * @throws \Throwable If database operation fails.
     */
    public function createProgram(array $data): Program
    {
        return DB::transaction(function () use ($data) {
            $program = Program::create($data);

            $this->logger->log(
                event: 'program.created',
                message: "New program created: {$program->name}",
                properties: ['id' => $program->id],
                logName: 'programs',
                subject: $program
            );

            return $program;
        });
    }

    /**
     * Retrieve a specific program by ID with its relationships.
     * * Features a dual-cache strategy (list tag + individual ID tag).
     * * @param int $id Program unique identifier.
     * @return Program
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getProgramById(int $id): Program
    {
        $cacheKey = "program_detail_{$id}";

        $program = Cache::tags(['programs_list', $cacheKey])->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn() => Program::with(['issueCategory', 'creator', 'activities'])->findOrFail($id)
        );

        $this->logger->log(
            event: 'program.viewed',
            message: "Program '{$program->name}' accessed.",
            logName: 'programs',
            subject: $program
        );

        return $program;
    }

    /**
     * Update an existing program and validate its lifecycle transition.
     * * @param int $id Program ID to update.
     * @param array $data Updated attributes.
     * @return Program
     * @throws HttpResponseException If the requested status transition is invalid.
     */
    public function updateProgram(int $id, array $data): Program
    {
        $program = Program::findOrFail($id);

        if (isset($data['status'])) {
            $newStatus = ProgramStatus::from($data['status']);

            if (!$program->status->canTransitionTo($newStatus)) {
                throw new HttpResponseException(
                    response()->json([
                        'message' => "Transition from {$program->status->value} to {$newStatus->value} is not allowed."
                    ], 422)
                );
            }
        }

        $program->update($data);

        $this->logger->log(
            event: 'program.updated',
            message: "Program updated: {$program->name}",
            logName: 'programs',
            subject: $program
        );

        return $program;
    }

    /**
     * Delete a program from the database.
     * * @param int $id Program ID to remove.
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteProgram(int $id): bool
    {
        $program = Program::findOrFail($id);

        $this->logger->log(
            event: 'program.deleted',
            message: "Program deleted: {$program->name}",
            logName: 'programs',
            subject: $program
        );

        return $program->delete();
    }
}
