<?php

namespace Modules\Programs\Http\Requests\Api\V1\ActivityAttendance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Programs\Enums\Api\V1\Activity\AttendanceStatus;

class UpdateActivityAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'activity_session_id' => ['nullable', 'integer', 'exists:activity_sessions,id'],
            'beneficiary_id'      => ['nullable', 'integer', 'exists:beneficiaries,id'],
            'attendance_status'   => ['nullable', 'string', Rule::in(AttendanceStatus::all())],
            'description'         => ['nullable', 'string', 'max:1000'],
        ];
    }
}
