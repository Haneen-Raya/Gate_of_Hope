<?php

namespace Modules\Assessments\Observers;

use Illuminate\Support\Str;
use Modules\Assessments\Models\IssueType;

/**
 * Observer for IssueType model
 *
 * Automatically generates a unique `code` for the issue type
 * when a new type is being created, based on its name.
 *
 * Example:
 *  - Name: "Login Issue"
 *  - Generated code: "LOGIN_ISSUE"
 *  - If "LOGIN_ISSUE" exists, it will generate "LOGIN_ISSUE_1", "LOGIN_ISSUE_2", etc.
 */
class IssueTypeObserver
{
    /**
     * Handle the IssueType "creating" event.
     *
     * This method is triggered automatically before a new
     * IssueType is saved to the database. It ensures that
     * the `code` attribute is set to a unique value if it is empty.
     *
     * @param IssueType $type
     * @return void
     */
    public function creating(IssueType $type)
    {
        if (empty($type->code)) {
            $type->code = $this->generateUniqueCode($type->name);
        }
    }

    /**
     * Generate a unique code for the issue type based on its name.
     *
     * Converts the name to uppercase, replaces spaces with underscores,
     * and appends a counter if the generated code already exists.
     *
     * @param string $name The name of the issue type
     * @return string Unique code for the issue type
     */
    private function generateUniqueCode($name)
    {
        $baseCode = Str::upper(Str::slug($name, '_'));
        $code = $baseCode;
        $counter = 1;

        while (IssueType::where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter++;
        }

        return $code;
    }
}
