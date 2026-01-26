<?php

namespace Modules\CaseManagement\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\CaseManagement\Models\CaseReferral;

use function Symfony\Component\Clock\now;

class CaseReferralService
{
    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_CASE_REFERRALS_GLOBAL = 'case_referrals';     // Tag for lists of caseReferrals
    private const TAG_CASE_REFERRAL_PREFIX = 'case_referral_';      // Tag for specific cassReferral details

    /**
     * Get all case referrals from database
     *
     * @return array $arraydata
     */
    public function getAllCaseReferrals(array $filters = [])
    {
        ksort($filters);
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'case_referrals_list_' . md5($cacheBase);


        $query = CaseReferral::with(['beneficiaryCase','service','receiverEntity','creator','updater']);

        return Cache::tags([self::TAG_CASE_REFERRALS_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage,$query) {
                return $query
                    ->filter($filters)      // Executes the specialized CaseReferralBuilder orchestration.
                    ->paginate($perPage);   // Returns a paginated instance with metadata.
            }
        );
    }

    /**
     * Add new case referral to the database.
     *
     * @param array $arraydata
     *
     * @return CaseReferral $caseReferral
     */
    public function createCaseReferral(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] =auth()->id();
            $data['updated_by'] =auth()->id();
            $data['referral_date'] =now();
            $data['followup_date'] =now();
            $caseReferral = CaseReferral::create($data);
            return $caseReferral;
        });
    }

    /**
     * Get a single case referral with its relationships.
     *
     * @param  CaseReferral $case referral
     *
     * @return CaseReferral $case referral
     */
    public function showCaseReferral(CaseReferral $caseReferral)
    {
        $cacheKey=self::TAG_CASE_REFERRAL_PREFIX."details_{$caseReferral->id}".'_'.app()->getLocale();
        $caseReferralTag=self::TAG_CASE_REFERRAL_PREFIX.$caseReferral->id;
        return Cache::tags([self::TAG_CASE_REFERRALS_GLOBAL, $caseReferralTag])->remember($cacheKey, self::CACHE_TTL, function () use ($caseReferral) {
            return $caseReferral->load(['beneficiaryCase','service','receiverEntity','creator','updater'])->toArray();
        });
    }

    /**
     * Update the specified case referral in the database.
     *
     * @param array $arraydata
     * @param  CaseReferral $caseReferral
     *
     * @return CaseReferral $caseReferral
     */
    public function updateCaseReferral(array $data, CaseReferral $caseReferral)
    {
        return DB::transaction(function () use ($data,$caseReferral) {
            $data['updated_by'] =auth()->id();
            $caseReferral->update($data);
            return $caseReferral->refresh();
        });
    }

    /**
     * Delete the specified case referral from the database.
     *
     * @param CaseReferral $caseReferral
     *
     */
    public function deleteCaseReferral(CaseReferral $caseReferral)
    {
        $caseReferral->delete();
    }
}


