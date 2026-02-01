<?php

namespace Modules\Programs\Services;

use App\Services\LoggerService;
use Illuminate\Support\Facades\DB;
use Modules\Programs\Models\Program;
use Illuminate\Support\Facades\Cache;
use Modules\Programs\Enums\Program\ProgramStatus;

/**
 * Class ProgramService
 * Fokus on pure business logic. Error handling is delegated to the Global Handler.
 */
class ProgramService
{
    /** Cache TTL: 1 Hour */
    const CACHE_TTL = 3600;

    public function __construct(protected LoggerService $logger)
    {
    }

    /**
     * List programs with caching.
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
     * Create a new program.
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
     * Get program details.
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
     * Update an existing program.
     */
    public function updateProgram(int $id, array $data): Program
    {
        $program = Program::findOrFail($id);
        if (isset($data['status'])) {
        $newStatus = ProgramStatus::from($data['status']);

        if (!$program->status->canTransitionTo($newStatus)) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json(['message' => 'Transition from ' . $program->status->value . ' to ' . $newStatus->value . ' is not allowed.'], 422)
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
     * Delete a program.
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
