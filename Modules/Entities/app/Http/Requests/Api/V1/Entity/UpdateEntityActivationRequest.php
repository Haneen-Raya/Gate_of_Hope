<?php

namespace Modules\Entities\Http\Requests\Api\V1\Entity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class UpdateEntityActivationRequest
 * * A specialized request for administrative status toggling.
 * Separating activation from general updates prevents accidental status changes
 * and allows for more granular permission control.
 */
class UpdateEntityActivationRequest extends FormRequest
{
    /**
     * Ensure only users with specific activation permissions can perform this action.
     * * @return bool
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user && $user->can('entities.activation.update');
    }

    /**
     * Validation rules for activation status.
     * * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'is_active'  => ['required', 'boolean'],
        ];
    }
}
