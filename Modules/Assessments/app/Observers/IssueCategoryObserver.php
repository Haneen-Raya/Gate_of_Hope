<?php

namespace Modules\Assessments\Observers;

use Illuminate\Support\Str;
use Modules\Assessments\Models\IssueCategory;

/**
 * Observer for IssueCategory model
 *
 * Automatically generates a unique `code` for the category
 * when a new category is being created, based on its name.
 *
 * Example:
 *  - Name: "Network Issue"
 *  - Generated code: "NETWORK_ISSUE"
 *  - If "NETWORK_ISSUE" exists, it will generate "NETWORK_ISSUE_1", "NETWORK_ISSUE_2", etc.
 */
class IssueCategoryObserver
{
    /**
     * Handle the IssueCategory "creating" event.
     *
     * This method is triggered automatically before a new
     * IssueCategory is saved to the database. It ensures that
     * the `code` attribute is set to a unique value if it is empty.
     *
     * @param IssueCategory $category
     * @return void
     */
    public function creating(IssueCategory $category)
    {
        if (empty($category->code)) {
            $category->code = $this->generateUniqueCode($category->name);
        }
    }

    /**
     * Generate a unique code for the category based on its name.
     *
     * Converts the name to uppercase, replaces spaces with underscores,
     * and appends a counter if the generated code already exists.
     *
     * @param string $name The name of the category
     * @return string Unique code for the category
     */
    private function generateUniqueCode($name)
    {
        $baseCode = Str::upper(Str::slug($name, '_'));
        $code = $baseCode;
        $counter = 1;

        while (IssueCategory::where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter++;
        }

        return $code;
    }
}
