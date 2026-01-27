<?php

namespace Modules\CaseManagement\Observers;

use Modules\CaseManagement\Enums\CaseReferralStatus;
use Modules\CaseManagement\Models\CaseReferral;

class CaseReferralObserver
{
    /**
     * Handle the CaseReferral "created" event.
     *
     * @param CaseReferral $caseReferral
     *
     * @return void
     */
    public function creating(CaseReferral $caseReferral): void
    {
        $caseReferral->created_by =auth()->id();
        $caseReferral->status = CaseReferralStatus::REFERRED->value;
        $caseReferral->followup_date =now();
    }

    /**
     * Handle the "saving" event for the CaseReferral model.
     *
     * This method is responsible for automatically managing
     * status-related timestamps in a centralized and consistent way.
     *
     * Business rule:
     * - When the referral status changes, the corresponding timestamp
     *   field is automatically populated with the current time.
     * - Existing timestamps are never overridden.
     *
     * This ensures:
     * - Accurate lifecycle tracking of referrals.
     * - Clean separation of business logic from controllers.
     * - A single source of truth for status-to-timestamp mapping.
     *
     * @param  CaseReferral  $caseReferral
     * @return void
     */
    public function saving(CaseReferral $caseReferral): void
    {
        if($caseReferral->exists && auth()->check()){
            $caseReferral->updated_by =auth()->id();
        }
        // Skip processing if the status has not changed
        if (! $caseReferral->isDirty('status')) {
            return;
        }

        // Current referral status (enum instance)
        $status = $caseReferral->status;

        // Resolve the timestamp field associated with this status
        $timestampField = $status->timestampField();

         // Set the timestamp only if defined and not already populated
        if ($timestampField && empty($caseReferral->{$timestampField})) {
            $caseReferral->{$timestampField} = now();
        }
    }
}
