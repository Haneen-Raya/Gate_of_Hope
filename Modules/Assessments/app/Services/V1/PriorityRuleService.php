<?php

namespace Modules\Assessments\Services\V1;

use Illuminate\Support\Facades\Cache;
use Modules\Assessments\Models\PriorityRules;
/**
 * Class PriorityRuleService
 * * Manages the business logic for assessment priority rules.
 * This service handles scoring thresholds (min/max) and matches them
 * with priority levels (Low, Medium, High, Critical) while utilizing
 * a tagged caching system for high-speed retrieval.
 * * @package Modules\Assessments\Services\V1
 */
class PriorityRuleService
{

    /**
     * Cache expiration time in seconds (1 hour).
     * @var int
     */
    private const CACHE_TTL = 3600;

    /**
     * Global tag used for all priority rule cache entries.
     * @var string
     */
    private const TAG_RULES_GLOBAL = 'priority_rules';

    /**
     * Prefix for individual rule cache tags.
     * @var string
     */
    private const TAG_RULE_PREFIX = 'priority_rule_';

    /**
     * Retrieve all active priority rules with their associated issue types.
     * * @return \Illuminate\Database\Eloquent\Collection|PriorityRules[]
     */
    public function getAll()
    {
        $cacheKey = "priority_rules_list_all";

        return Cache::tags([self::TAG_RULES_GLOBAL])->remember($cacheKey, self::CACHE_TTL, function () {
            return PriorityRules::with('issueType')->where('is_active', 1)->get();
        });
    }

    /**
     * Get a specific priority rule by ID.
     * * Uses double-tagging to ensure that updating a single rule only
     * invalidates its own specific cache and the global list.
     * * @param int $id The unique identifier of the rule.
     * @return PriorityRules
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id)
    {
        $cacheKey    = self::TAG_RULE_PREFIX . "details_{$id}";
        $specificTag = self::TAG_RULE_PREFIX . $id;

        return Cache::tags([self::TAG_RULES_GLOBAL, $specificTag])->remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            return PriorityRules::with('issueType')->findOrFail($id);
        });
    }

    /**
     * Create a new priority rule and invalidate the global cache.
     * * @param array $data Validated rule data (issue_type_id, min_score, max_score, priority).
     * @return PriorityRules
     */
    public function create(array $data)
    {
        $rule = PriorityRules::create($data);
        $this->clearGlobalCache();
        return $rule;
    }

    /**
     * Update an existing priority rule and refresh its specific cache.
     * * @param int $id The ID of the rule to update.
     * @param array $data The updated attributes.
     * @return PriorityRules
     */
    public function update(int $id, array $data)
    {
        $rule = PriorityRules::findOrFail($id);
        $rule->update($data);

        $this->clearSpecificCache($id);
        return $rule;
    }

    /**
     * Remove a priority rule and clean up associated cache tags.
     * * @param int $id The ID of the rule to delete.
     * @return bool|null
     */
    public function delete(int $id)
    {
        $rule = PriorityRules::findOrFail($id);
        $this->clearSpecificCache($id);
        return $rule->delete();
    }

    /**
     * Invalidate cache tags for a specific rule and the global rules list.
     * * @param int $id The ID of the specific rule.
     * @return void
     */
    public function clearSpecificCache(int $id)
    {
        $specificTag = self::TAG_RULE_PREFIX . $id;
        Cache::tags([$specificTag, self::TAG_RULES_GLOBAL])->flush();
    }

    /**
     * Invalidate all priority rule-related cache entries.
     * * @return void
     */
    public function clearGlobalCache()
    {
        Cache::tags([self::TAG_RULES_GLOBAL])->flush();
    }
}
