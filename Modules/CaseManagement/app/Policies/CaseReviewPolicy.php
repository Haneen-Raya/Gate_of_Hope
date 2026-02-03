<?php

namespace Modules\CaseManagement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CaseManagement\Models\CaseReview;
use Modules\Core\Models\User;

/**
 * Class CaseReviewPolicy
 *
 * Governs the authorization logic for case reviews.
 * Allows multi-stakeholder access based on specialized professional roles
 * and direct beneficiary relationships.
 *
 * @package Modules\CaseManagement\Policies
 */
class CaseReviewPolicy
{
    use HandlesAuthorization;

    public function __construct() {}

    /**
     * Determine whether the user can view the index of case reviews.
     * Permission: 'case_reviews.read'
     */
    public function viewAny(User $user): bool
    {
        return $user->can('case_reviews.read');
    }

    /**
     * Determine whether the user can view a specific case review.
     * ACCESS GRANTED IF:
     * 1. User is an Administrator/Auditor (case_reviews.read).
     * 2. User is the Beneficiary associated with the case.
     * 3. User is the Specialist who conducted the review.
     */
    public function view(User $user, CaseReview $caseReview): bool
    {
        return $user->can('case_reviews.read') ||
               $caseReview->beneficiary_case_id == $user->beneficiary->id ||
               $caseReview->specialist_id == $user->specialist->id;
    }

    /**
     * Determine whether the user can initiate a new case review.
     */
    public function create(User $user): bool
    {
        return $user->can('case_reviews.create');
    }

    /**
     * Determine whether the user can modify a review.
     * Follows the same logic as 'view' but requires 'update' permission for staff.
     */
    public function update(User $user, CaseReview $caseReview): bool
    {
        return $user->can('case_reviews.update') ||
               $caseReview->beneficiary_case_id == $user->beneficiary->id ||
               $caseReview->specialist_id == $user->specialist->id;
    }

    /**
     * Determine whether the user can permanently delete a review.
     * Strict requirement: Administrative permission 'case_reviews.delete'.
     */
    public function delete(User $user, CaseReview $caseReview): bool
    {
        return $user->can('case_reviews.delete');
    }
}
