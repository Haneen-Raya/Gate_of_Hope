<?php

namespace Modules\CaseManagement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\CaseManagement\Models\CaseReview;
use Modules\Core\Models\User;

class CaseReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('case_reviews.read');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CaseReview $caseReview): bool
    {
        return $user->can('case_reviews.read') || $caseReview->beneficiary_case_id == $user->beneficiary->id || $caseReview->specialist_id == $user->specialist->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('case_reviews.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CaseReview $caseReview): bool
    {
        return $user->can('case_reviews.update') || $caseReview->beneficiary_case_id == $user->beneficiary->id || $caseReview->specialist_id == $user->specialist->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CaseReview $caseReview): bool
    {
        return $user->can('case_reviews.delete');
    }
}
