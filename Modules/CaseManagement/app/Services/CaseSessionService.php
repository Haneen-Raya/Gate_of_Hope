<?php

namespace Modules\CaseManagement\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Modules\CaseManagement\Models\CaseSession;
use Modules\CaseManagement\Enums\SessionType;
use Modules\CaseManagement\Models\BeneficiaryCase;
use Throwable;

/**
 * Class CaseSessionService
 *
 * Responsible for handling all business logic related to
 * case sessions including:
 * - querying sessions
 * - caching strategies
 * - create / update / delete lifecycle
 *
 * This service relies on:
 * - Custom Eloquent Builder (CaseSessionBuilder)
 * - Cache Tags (Redis recommended)
 * - Enum casting for session_type
 */
class CaseSessionService
{
    /**
     * Cache TTL in minutes
     */
    protected int $cacheMinutes = 10;

    /**
     * Get paginated sessions for a specific beneficiary case
     *
     * Uses cache tags:
     * - case_sessions
     * - case_{caseId}
     *
     * @param int $caseId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateForCase(
        int $caseId,
        int $perPage = 10
    ): LengthAwarePaginator {
        $page = request('page', 1);

        return Cache::tags(['case_sessions', "case_{$caseId}"])
            ->remember(
                "paginate.case.{$caseId}.page.{$page}",
                now()->addMinutes($this->cacheMinutes),
                fn () => CaseSession::query()
                    ->withRelations()
                    ->forCase($caseId)
                    ->latestSession()
                    ->paginate($perPage)
            );
    }

    /**
     * Get all sessions for a specific case (no pagination)
     *
     * @param int $caseId
     * @return Collection<CaseSession>
     */
    public function getAllForCase(int $caseId): Collection
    {
        return Cache::tags(['case_sessions', "case_{$caseId}"])
            ->remember(
                "all.case.{$caseId}",
                now()->addMinutes($this->cacheMinutes),
                fn () => CaseSession::query()
                    ->withRelations()
                    ->forCase($caseId)
                    ->latestSession()
                    ->get()
            );
    }

    /**
     * Retrieve a single case session by its ID
     *
     * @param int $id
     * @return CaseSession
     *
     * @throws ModelNotFoundException
     */
    public function findById(int $id): CaseSession
    {
        return Cache::tags(['case_sessions'])
            ->remember(
                "session.{$id}",
                now()->addMinutes($this->cacheMinutes),
                fn () => CaseSession::query()
                    ->withRelations()
                    ->findOrFail($id)
            );
    }

    /**
     * Create a new case session
     *
     * Expected that:
     * - session_type is already validated against SessionType enum
     * - Model casting will convert it to SessionType instance
     *
     * @param array $data
     * @param int $beneficiaryCaseId
     * @return CaseSession
     *
     * @throws Throwable
     */
    public function create(
        array $data,
        BeneficiaryCase $beneficiaryCase
    ): CaseSession {

        // Attach the beneficiary case ID to the session data
        $data['beneficiary_case_id'] = $beneficiaryCase->id;

        // Prevent creating duplicate sessions:
        // Same case + same session date + same specialist
        $exists = CaseSession::query()
            ->where('beneficiary_case_id', $beneficiaryCase->id)
            ->whereDate('session_date', $data['session_date'])
            ->where('conducted_by', $data['conducted_by'])
            ->exists();

        if ($exists) {
            abort(409, 'A session with the same case, date, and specialist already exists.');
        }

        // Create the session
        $session = CaseSession::create($data);

        // Clear related cache to avoid stale data
        $this->clearCaseCache($beneficiaryCase->id);

        return $session;
    }


    /**
     * Update an existing case session
     *
     * @param CaseSession $caseSession
     * @param array $data
     * @return CaseSession
     *
     * @throws Throwable
     */
    public function update(
        CaseSession $caseSession,
        array $data
    ): CaseSession {

        // Check for duplicate session:
        // Same case + same date + same specialist
        // Exclude the current session from the check
        $exists = CaseSession::query()
            ->where('beneficiary_case_id', $caseSession->beneficiary_case_id)
            ->whereDate('session_date', $data['session_date'] ?? $caseSession->session_date)
            ->where('conducted_by', $data['conducted_by'] ?? $caseSession->conducted_by)
            ->where('id', '!=', $caseSession->id)
            ->exists();

        if ($exists) {
            abort(409, 'A session with the same case, date, and specialist already exists.');
        }

        // Update the session
        $caseSession->update($data);

        // Clear case-level cache
        $this->clearCaseCache($caseSession->beneficiary_case_id);

        // Clear single-session cache
        $this->clearSessionCache($caseSession->id);

        // Return fresh model instance
        return $caseSession->refresh();
    }

    /**
     * Delete a case session
     *
     * @param CaseSession $caseSession
     * @return void
     *
     * @throws Throwable
     */
    public function delete(CaseSession $caseSession): void
    {
        $caseId = $caseSession->beneficiary_case_id;
        $sessionId = $caseSession->id;

        $caseSession->delete();

        $this->clearCaseCache($caseId);
        $this->clearSessionCache($sessionId);
    }

    /**
     * Get paginated sessions handled by a specific specialist
     *
     * @param int $specialistId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getBySpecialist(
        int $specialistId,
        int $perPage = 10
    ): LengthAwarePaginator {
        $page = request('page', 1);

        return Cache::tags(['case_sessions', "specialist_{$specialistId}"])
            ->remember(
                "paginate.specialist.{$specialistId}.page.{$page}",
                now()->addMinutes($this->cacheMinutes),
                fn () => CaseSession::query()
                    ->withRelations()
                    ->bySpecialist($specialistId)
                    ->latestSession()
                    ->Paginate($perPage)
            );  
    }

    /**
     * Get sessions for a case within a date range
     *
     * @param int $caseId
     * @param string $from
     * @param string $to
     * @return Collection<CaseSession>
     */
    public function getForCaseBetweenDates(
        int $caseId,
        string $from,
        string $to
    ): Collection {
        return Cache::tags(['case_sessions', "case_{$caseId}"])
            ->remember(
                "between.case.{$caseId}.{$from}.{$to}",
                now()->addMinutes($this->cacheMinutes),
                fn () => CaseSession::query()
                    ->withRelations()
                    ->forCase($caseId)
                    ->betweenDates($from, $to)
                    ->latestSession()
                    ->get()
            );
    }
    /**
     * Count all sessions for a given case
     *
     * @param int $caseId
     * @return int
     */
    public function countForCase(int $caseId): int
    {
        return Cache::tags(['case_sessions', "case_{$caseId}"])
            ->remember(
                "count.case.{$caseId}",
                now()->addMinutes($this->cacheMinutes),
                fn () => CaseSession::query()
                    ->forCase($caseId)
                    ->count()
            );
    }

    public function getByType(int $caseId, SessionType|string $type)
    {
        return CaseSession::query()
            ->forCase($caseId)
            ->sessionType($type)
            ->latestSession()
            ->get();
    }


    /* =======================================================
     | Cache invalidation helpers
     ======================================================= */

    /**
     * Clear all cached data related to a specific case
     */
    protected function clearCaseCache(int $caseId): void
    {
        Cache::tags(['case_sessions', "case_{$caseId}"])->flush();
    }

    /**
     * Clear cached data for a single session
     */
    protected function clearSessionCache(int $sessionId): void
    {
        Cache::forget("session.{$sessionId}");
    }
}
