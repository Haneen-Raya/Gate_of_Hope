<?php

namespace Modules\Assessments\Services\V1;

use Illuminate\Support\Facades\Cache;
use Modules\Assessments\Models\GoogleForm;

/**
 * Class FormGoogleServices
 *
 * Provides administrative operations for managing Google Form links.
 * Implements a multi-layered caching strategy using tags to optimize
 * data retrieval and ensure efficient cache invalidation.
 *
 * @package Modules\Assessments\Services\V1
 */
class FormGoogleServices
{
    /**
     * Cache expiration time in seconds (1 hour).
     * @var int
     */
    private const CACHE_TTL = 3600;

    /**
     * Global cache tag for all google form entries.
     * @var string
     */
    private const TAG_FORMS_GLOBAL = 'google_forms';

    /**
     * Prefix used for individual form cache tags.
     * @var string
     */
    private const TAG_FORM_PREFIX  = 'google_form_';

    /**
     * Retrieve a paginated list of Google Forms.
     *
     * Results are cached based on the current page and limit to improve performance.
     *
     * @param int $perpage Number of items to display per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(int $perpage = 10)
    {
        $page = (int) request('page', 1);
        $cachekey = "form_list_P{$page}_limit_{$perpage}";

        return Cache::tags([self::TAG_FORMS_GLOBAL])->remember($cachekey, self::CACHE_TTL,
            fn() => GoogleForm::with('issueType')->paginate($perpage)
        );
    }

    /**
     * Retrieve a specific Google Form by its ID.
     *
     * Uses double-tagging (global and specific) to allow granular cache invalidation.
     *
     * @param int $id The unique identifier of the form.
     * @return GoogleForm
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id)
    {
        $cachekey    = self::TAG_FORM_PREFIX . "details_{$id}";
        $specificTag = self::TAG_FORM_PREFIX . $id;

        return Cache::tags([self::TAG_FORMS_GLOBAL, $specificTag])
            ->remember($cachekey, self::CACHE_TTL,
                fn() => GoogleForm::with('issueType')->findOrFail($id)
            );
    }

    /**
     * Create a new Google Form entry.
     * Note: Global cache tags are automatically flushed by the model's AutoFlushCache trait.
     *
     * @param array $data Validated data containing 'url' and 'issue_type_id'.
     * @return GoogleForm
     */
    public function create(array $data)
    {
        return GoogleForm::create($data);
    }

    /**
     * Update an existing Google Form entry.
     * Note: Invalidation is handled automatically via AutoFlushCache in the GoogleForm model.
     *
     * @param int $id The ID of the form to update.
     * @param array $data The updated data.
     * @return GoogleForm
     */
    public function update(int $id, array $data)
    {
        $form = GoogleForm::findOrFail($id);
        $form->update($data);

        return $form;
    }

    /**
     * Delete a Google Form entry.
     * Note: AutoFlushCache trait handles the removal of associated cache tags.
     *
     * @param int $id The ID of the form to delete.
     * @return bool|null
     */
    public function delete(int $id)
    {
        $form = GoogleForm::findOrFail($id);
        return $form->delete();
    }

    /**
     * Get a specific Google Form by the Issue Type ID.
     *
     * @param int $issueTypeId
     * @return GoogleForm
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getByIssueType(int $issueTypeId)
    {
        $cacheKey = self::TAG_FORM_PREFIX . "issue_type_{$issueTypeId}";

        return Cache::tags([self::TAG_FORMS_GLOBAL])->remember($cacheKey, self::CACHE_TTL, function () use ($issueTypeId) {
            return GoogleForm::with('issueType')
                ->where('issue_type_id', $issueTypeId)
                ->firstOrFail();
        });
    }
}
