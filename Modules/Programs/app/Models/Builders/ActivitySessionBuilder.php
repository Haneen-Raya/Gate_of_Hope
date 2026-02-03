<?php

namespace Modules\Programs\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Modules\Programs\Enums\V1\ActivitySessionStatus;

/**
 * Class ActivitySessionBuilder
 *
 * Custom Eloquent query builder for ActivitySession model.
 * Provides convenient methods to filter and order sessions by date, status,
 * trainer, activity, and other common criteria.
 */
class ActivitySessionBuilder extends Builder
{
    /**
     * Filter upcoming sessions (today or later) with status SCHEDULED or ONGOING.
     *
     * @return self
     */
    public function upcomingSessions(): self
    {
        return $this->whereDate('session_date', '>=', today())
                    ->whereIn('status', [
                        ActivitySessionStatus::SCHEDULED,
                        ActivitySessionStatus::ONGOING,
                    ]);
    }

    /**
     * Filter past sessions (before today).
     *
     * @return self
     */
    public function pastSessions(): self
    {
        return $this->whereDate('session_date', '<', today());
    }

    /**
     * Filter sessions by trainer ID.
     *
     * @param int $trainerId
     * @return self
     */
    public function forTrainerSessions(int $trainerId): self
    {
        return $this->where('trainer_id', $trainerId);
    }

    /**
     * Filter sessions by activity ID.
     *
     * @param int $activityId
     * @return self
     */
    public function forActivity(int $activityId): self
    {
        return $this->where('activity_id', $activityId);
    }

    /**
     * Filter sessions by status.
     *
     * Accepts a single ActivitySessionStatus enum, a string, or an array of enums/strings.
     *
     * @param ActivitySessionStatus|array<ActivitySessionStatus|string>|string $statuses
     * @return self
     */
    public function status(ActivitySessionStatus|array|string $statuses): self
    {
        if (is_array($statuses)) {
            $values = array_map(fn($s) => $s instanceof ActivitySessionStatus ? $s->value : $s, $statuses);
            return $this->whereIn('status', $values);
        }

        return $this->where('status', $statuses instanceof ActivitySessionStatus ? $statuses->value : $statuses);
    }

    /**
     * Filter sessions occurring on a specific date.
     *
     * @param string $date Format: 'Y-m-d'
     * @return self
     */
    public function onDate(string $date): self
    {
        return $this->whereDate('session_date', $date);
    }

    /**
     * Order sessions by session_date and start_time.
     *
     * @param string $direction 'asc' or 'desc'
     * @return self
     */
    public function orderByDate(string $direction = 'asc'): self
    {
        return $this->orderBy('session_date', $direction)
                    ->orderBy('start_time', $direction);
    }
}
