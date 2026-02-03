<?php

namespace Modules\Programs\Http\Requests\Api\V1\ActivityAttendance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Programs\Enums\V1\AttendanceStatus;

/**
 * Class StoreActivityAttendanceRequest
 * * Authorizes and validates the creation of an attendance record.
 * This request ensures that every attendance entry is mapped to a valid session
 * and follows the standardized AttendanceStatus Enum.
 */
class StoreActivityAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Verified against the 'activity.attendance.create' permission.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('activity.attendance.create');
    }

    /**
     * Get the validation rules that apply to the request.
     * * RULES:
     * - activity_session_id: Mandatory foreign key to the specific session.
     * - beneficiary_id: Mandatory foreign key to the recipient.
     * - attendance_status: Validated against predefined Enum values (Present, Absent, etc.).
     */
    public function rules(): array
    {
        return [
            'activity_session_id' => ['required', 'integer', 'exists:activity_sessions,id'],
            'beneficiary_id'      => ['required', 'integer', 'exists:beneficiaries,id'],
            'attendance_status'   => ['required', 'string', Rule::in(AttendanceStatus::all())],
            'description'         => ['nullable', 'string', 'max:1000'],
        ];
    }
}
