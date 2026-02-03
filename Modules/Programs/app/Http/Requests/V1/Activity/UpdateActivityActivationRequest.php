<?php

namespace Modules\Programs\Http\Requests\Api\V1\Activity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class UpdateActivityActivationRequest
 * * Specifically handles the activation/deactivation toggle for an activity.
 * This is used for quick administrative status changes.
 */
class UpdateActivityActivationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to update the status.
     * Requires the specific 'activities.activation.update' permission.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('activities.activation.update');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'is_active'  => ['required','boolean'],
        ];
    }
}
