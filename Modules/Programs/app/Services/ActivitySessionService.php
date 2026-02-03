<?php

namespace Modules\Programs\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Programs\Models\ActivitySession;
use Modules\Programs\Enums\ActivitySessionStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Support\Collection;
use Modules\Programs\Events\ActivitySessionScheduled;

/**
 * Class ActivitySessionService
 *
 * Contains all business logic related to Activity Sessions.
 * Handles creation, updates, deletion, status transitions,
 * caching strategies, and spatial queries.
 */
class ActivitySessionService
{
    /**
     * Cache key prefix for activity session related queries.
     *
     * @var string
     */
    protected string $cacheKeyPrefix = 'activity_sessions_';

    /**
     * Retrieve paginated activity sessions with caching.
     *
     * @param int $perPage Number of items per page
     * @param int $page Current page number
     * @return LengthAwarePaginator
     */
    public function getPaginatedSessions(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $cacheKey = $this->cacheKeyPrefix . "paginated_page_{$page}_per_{$perPage}";

        return Cache::remember($cacheKey, 3600, function () use ($perPage, $page) {
            return ActivitySession::query()
                ->orderByDate()
                ->paginate($perPage, ['*'], 'page', $page);
        });
    }

    /* -----------------------------------------------------------------
     |  CREATE
     |-----------------------------------------------------------------*/

    /**
     * Create a new activity session.
     *
     * - Validates location data
     * - Checks trainer schedule conflicts
     * - Wraps operation in a database transaction
     * - Flushes related cache entries
     *
     * @param array $data
     * @return ActivitySession
     *
     * @throws \Exception
     */
    public function create(array $data): ActivitySession
    {
        return DB::transaction(function () use ($data) {

            $this->prepareLocation($data);

            $this->checkTrainerConflict(
                $data['trainer_id'],
                $data['session_date'],
                $data['start_time'],
                $data['end_time']
            );

            $session = ActivitySession::create($data);

            $this->flushCache($session);

            event(new ActivitySessionScheduled($session));

            return $session;
        });
    }

    /* -----------------------------------------------------------------
     |  UPDATE
     |-----------------------------------------------------------------*/

    /**
     * Update an existing activity session.
     *
     * - Prevents modification of locked sessions
     * - Re-checks trainer time conflicts
     * - Updates spatial location if provided
     *
     * @param ActivitySession $session
     * @param array $data
     * @return ActivitySession
     *
     * @throws \Exception
     */
    public function update(ActivitySession $session, array $data): ActivitySession
    {
        return DB::transaction(function () use ($session, $data) {

            if ($this->isLocked($session)) {
                throw new \Exception('Cannot modify completed or cancelled sessions.');
            }

            $trainerId = $data['trainer_id'] ?? $session->trainer_id;
            $date      = $data['session_date'] ?? $session->session_date;
            $start     = $data['start_time'] ?? $session->start_time;
            $end       = $data['end_time'] ?? $session->end_time;

            $this->checkTrainerConflict($trainerId, $date, $start, $end, $session->id);

            $this->prepareLocation($data);

            $session->update($data);

            $this->flushCache($session);

            return $session->refresh();
        });
    }

    /* -----------------------------------------------------------------
     |  DELETE
     |-----------------------------------------------------------------*/

    /**
     * Delete an activity session.
     *
     * Past sessions cannot be deleted.
     *
     * @param ActivitySession $session
     * @return bool
     *
     * @throws \Exception
     */
    public function delete(ActivitySession $session): bool
    {
        if ($session->session_date < today()) {
            throw new \Exception('Cannot delete past sessions.');
        }

        return DB::transaction(function () use ($session) {
            $session->delete();
            $this->flushCache($session);
            return true;
        });
    }

    /* -----------------------------------------------------------------
     |  STATUS ACTIONS
     |-----------------------------------------------------------------*/

    /**
     * Mark an ongoing session as completed.
     *
     * @param ActivitySession $session
     * @return ActivitySession
     *
     * @throws \Exception
     */
    public function complete(ActivitySession $session): ActivitySession
    {
        if ($session->status !== ActivitySessionStatus::ONGOING) {
            throw new \Exception('Only ongoing sessions can be completed.');
        }

        $session->update(['status' => ActivitySessionStatus::COMPLETED]);
        $this->flushCache($session);

        return $session->refresh();
    }

    /**
     * Cancel an activity session.
     *
     * @param ActivitySession $session
     * @return ActivitySession
     *
     * @throws \Exception
     */
    public function cancel(ActivitySession $session): ActivitySession
    {
        if ($this->isLocked($session)) {
            throw new \Exception('Cannot cancel completed or cancelled sessions.');
        }

        $session->update(['status' => ActivitySessionStatus::CANCELLED]);
        $this->flushCache($session);

        return $session->refresh();
    }

    /* -----------------------------------------------------------------
     |  CACHED QUERIES
     |-----------------------------------------------------------------*/

    /**
     * Retrieve upcoming sessions for a specific trainer.
     *
     * @param int $trainerId
     * @return Collection
     */
    public function getUpcomingForTrainer(int $trainerId): Collection
    {
        $cacheKey = $this->cacheKeyPrefix . 'trainer_' . $trainerId;

        return Cache::remember($cacheKey, 3600, function () use ($trainerId) {
            return ActivitySession::query()
                ->forTrainerSessions($trainerId)
                ->status([
                    ActivitySessionStatus::SCHEDULED,
                    ActivitySessionStatus::ONGOING,
                ])
                ->orderByDate()
                ->get();
        });
    }

    /**
     * Retrieve upcoming sessions for a specific activity.
     *
     * @param int $activityId
     * @return Collection
     */
    public function getUpcomingForActivity(int $activityId): Collection
    {
        $cacheKey = $this->cacheKeyPrefix . 'activity_' . $activityId;

        return Cache::remember($cacheKey, 3600, function () use ($activityId) {
            return ActivitySession::query()
                ->forActivity($activityId)
                ->status([
                    ActivitySessionStatus::SCHEDULED,
                    ActivitySessionStatus::ONGOING,
                ])
                ->orderByDate()
                ->get();
        });
    }

    /* -----------------------------------------------------------------
     |  SPATIAL / LOCATION
     |-----------------------------------------------------------------*/

    /**
     * Retrieve nearby activity sessions using spatial queries.
     *
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @param int $radiusMeters Search radius in meters
     * @param int|null $activityId Optional activity filter
     *
     * @return Collection
     */
    public function getNearbySessions(
        float $lat,
        float $lng,
        int $radiusMeters = 5000,
        int $activityId = null
    ): Collection {
        $point = new Point($lng, $lat);

        return ActivitySession::query()
            ->when($activityId, fn ($q) => $q->forActivity($activityId))
            ->status([
                ActivitySessionStatus::SCHEDULED,
                ActivitySessionStatus::ONGOING,
            ])
            ->whereDistanceSphere('location', $point, '<=', $radiusMeters)
            ->orderByDate()
            ->get();
    }

    /* -----------------------------------------------------------------
     |  INTERNAL HELPERS
     |-----------------------------------------------------------------*/

    /**
     * Prepare and normalize location data into a spatial Point.
     *
     * @param array $data
     * @return void
     *
     * @throws \Exception
     */
    protected function prepareLocation(array &$data): void
    {
        if (! isset($data['location'])) {
            return;
        }

        if (! isset($data['location']['lat'], $data['location']['lng'])) {
            throw new \Exception('Invalid location data.');
        }

        $data['location'] = new Point(
            $data['location']['lng'],
            $data['location']['lat']
        );
    }

    /**
     * Check if the trainer has a conflicting session.
     *
     * @param int $trainerId
     * @param string $date
     * @param string $start
     * @param string $end
     * @param int|null $ignoreSessionId
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function checkTrainerConflict(
        int $trainerId,
        string $date,
        string $start,
        string $end,
        int $ignoreSessionId = null
    ): void {
        $conflict = ActivitySession::query()
            ->forTrainerSessions($trainerId)
            ->whereDate('session_date', $date)
            ->status([
                ActivitySessionStatus::DRAFT,
                ActivitySessionStatus::SCHEDULED,
                ActivitySessionStatus::ONGOING,
            ])
            ->when($ignoreSessionId, fn ($q) => $q->where('id', '!=', $ignoreSessionId))
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                  ->orWhereBetween('end_time', [$start, $end])
                  ->orWhere(function ($q) use ($start, $end) {
                      $q->where('start_time', '<=', $start)
                        ->where('end_time', '>=', $end);
                  });
            })
            ->exists();

        if ($conflict) {
            throw new \Exception('Trainer already has another session during this time.');
        }
    }

    /**
     * Determine if a session is locked.
     *
     * @param ActivitySession $session
     * @return bool
     */
    protected function isLocked(ActivitySession $session): bool
    {
        return in_array($session->status, [
            ActivitySessionStatus::COMPLETED,
            ActivitySessionStatus::CANCELLED,
        ]);
    }

    /**
     * Flush all related cache entries for an activity session.
     *
     * @param ActivitySession $session
     * @return void
     */
    protected function flushCache(ActivitySession $session): void
    {
        Cache::forget($this->cacheKeyPrefix . 'trainer_' . $session->trainer_id);
        Cache::forget($this->cacheKeyPrefix . 'activity_' . $session->activity_id);

        if (method_exists(Cache::getStore(), 'tags')) {
            Cache::tags(['activity_sessions'])->flush();
        }
    }
}
