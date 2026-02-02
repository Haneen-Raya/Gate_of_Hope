<?php

namespace Modules\Programs\Http\Requests\Api\V1\ActivityAttendance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Programs\Enums\Api\V1\Activity\AttendanceStatus;

class StoreActivityAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('activity.attendance.create');
    }

    /**
     * Get the validation rules that apply to the request.
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
