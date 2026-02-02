<?php

namespace Modules\CaseManagement\Enums\V1;

/**
 * Enum CaseEventTag
 *
 * A robust dictionary of standardized event identifiers across the 
 * Case Management module. This Enum acts as the "Single Source of Truth" 
 * for classification, filtering, and UI iconography.
 *
 * @package Modules\CaseManagement\Enums
 */
enum CaseEventTag: string
{
    /* --- Case Lifecycle --- */
    case CASE_OPENED = 'case.opened';
    case CASE_CLOSED = 'case.closed';
    case CASE_STATUS_CHANGED = 'case.status_changed';
    case CASE_PRIORITY_CHANGED = 'case.priority_changed';
    case CASE_UPDATED = 'case.updated';

        /* --- Referrals --- */
    case REFERRAL_CREATED = 'referral.created';
    case REFERRAL_ACCEPTED = 'referral.accepted';
    case REFERRAL_REJECTED = 'referral.rejected';
    case REFERRAL_COMPLETED = 'referral.completed';
    case REFERRAL_UPDATED = 'referral.updated';

        /* --- Reviews --- */
    case REVIEW_RECORDED = 'review.recorded';
    case REVIEW_PROGRESS_UPDATED = 'review.progress_updated';
    case REVIEW_NOTES_UPDATED = 'review.notes_updated';
    case REVIEW_UPDATED = 'review.updated';

        /* --- Sessions --- */
    case SESSION_HELD = 'session.held';
    case SESSION_RESCHEDULED = 'session.rescheduled';
    case SESSION_DURATION_CHANGED = 'session.duration_changed';
    case SESSION_UPDATED = 'session.updated';

        /* --- Support Plans --- */
    case PLAN_CREATED = 'plan.created';
    case PLAN_SCHEDULE_CHANGED = 'plan.schedule_changed';
    case PLAN_UPDATED = 'plan.updated';

    /**
     * Retrieve a human-readable label for the event.
     * * This method transforms the technical slug into a professional 
     * title suitable for UI headers, activity logs, and notifications.
     * * @return string
     */
    public function label(): string
    {
        return match ($this) {
            /* --- Case Lifecycle --- */
            self::CASE_OPENED           => 'Case Opened',
            self::CASE_CLOSED           => 'Case Closed',
            self::CASE_STATUS_CHANGED   => 'Case Status Updated',
            self::CASE_PRIORITY_CHANGED => 'Case Priority Adjusted',
            self::CASE_UPDATED          => 'Case Information Updated',

            /* --- Referrals --- */
            self::REFERRAL_CREATED      => 'New Referral Initiated',
            self::REFERRAL_ACCEPTED     => 'Referral Accepted',
            self::REFERRAL_REJECTED     => 'Referral Rejected',
            self::REFERRAL_COMPLETED    => 'Referral Service Completed',
            self::REFERRAL_UPDATED      => 'Referral Details Updated',

            /* --- Reviews & Evaluations --- */
            self::REVIEW_RECORDED         => 'Case Review Recorded',
            self::REVIEW_PROGRESS_UPDATED => 'Progress Status Updated',
            self::REVIEW_NOTES_UPDATED    => 'Clinical Notes Updated',
            self::REVIEW_UPDATED          => 'Review Details Updated',

            /* --- Sessions --- */
            self::SESSION_HELD             => 'Session Conducted',
            self::SESSION_RESCHEDULED      => 'Session Rescheduled',
            self::SESSION_DURATION_CHANGED => 'Session Duration Adjusted',
            self::SESSION_UPDATED          => 'Session Details Updated',

            /* --- Support Plans --- */
            self::PLAN_CREATED          => 'Support Plan Established',
            self::PLAN_SCHEDULE_CHANGED => 'Plan Schedule Modified',
            self::PLAN_UPDATED          => 'Support Plan Updated',

            /* --- Fallback Strategy --- */
            default => str($this->value)->replace('.', ' ')->title(),
        };
    }
}
