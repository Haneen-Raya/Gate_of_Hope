<?php

namespace Modules\Assessments\Services\V1;

use Illuminate\Support\Facades\Cache;
use Modules\Assessments\Models\PriorityRules;
class PriorityRuleService
{

    private const CACHE_TTL = 3600;

    private const TAG_RULES_GLOBAL = 'priority_rules';

    private const TAG_RULE_PREFIX = 'priority_rule_';

    public function getAll()
    {
        $cacheKey = "priority_rules_list_all";

        return Cache::tags([self::TAG_RULES_GLOBAL])->remember($cacheKey, self::CACHE_TTL, function () {
            return PriorityRules::with('issueType')->where('is_active', 1)->get();
        });
    }

    public function getById(int $id)
{
    $cacheKey    = self::TAG_RULE_PREFIX . "details_{$id}";
    $specificTag = self::TAG_RULE_PREFIX . $id;

    return Cache::tags([self::TAG_RULES_GLOBAL, $specificTag])->remember($cacheKey, self::CACHE_TTL, function () use ($id) {
        // أضفنا with('issueType') لضمان جلب بيانات نوع المشكلة
        return PriorityRules::with('issueType')->findOrFail($id);
    });
}

    public function create(array $data)
    {
        $rule = PriorityRules::create($data);
        $this->clearGlobalCache();
        return $rule;
    }


    public function update(int $id, array $data)
    {
        $rule = PriorityRules::findOrFail($id);
        $rule->update($data);

        $this->clearSpecificCache($id);
        return $rule;
    }

    public function delete(int $id)
    {
        $rule = PriorityRules::findOrFail($id);
        $this->clearSpecificCache($id);
        return $rule->delete();
    }


    public function clearSpecificCache(int $id)
    {
        $specificTag = self::TAG_RULE_PREFIX . $id;
        Cache::tags([$specificTag, self::TAG_RULES_GLOBAL])->flush();
    }


    public function clearGlobalCache()
    {
        Cache::tags([self::TAG_RULES_GLOBAL])->flush();
    }
}
