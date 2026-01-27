<?php

namespace Modules\Entities\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Entities\Models\ProgramFunding;

class ProgramFundingService
{
    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_PROGRAM_FUNDINGS_GLOBAL = 'program_fundings';     // Tag for lists of programFundings
    private const TAG_PROGRAM_FUNDING_PREFIX = 'program_funding_';      // Tag for specific programFunding details

    /**
     * Get all program fundings from database
     *
     * @return array $arraydata
     */
    public function getAllProgramFundings(array $filters = [])
    {
        ksort($filters);
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'program_fundings_list_' . md5($cacheBase);

        $query = ProgramFunding::with(['donorEntity','program']);

        return Cache::tags([self::TAG_PROGRAM_FUNDINGS_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage,$query) {
                return $query
                    ->filter($filters)      // Executes the specialized ProgramFundingBuilder orchestration.
                    ->paginate($perPage);   // Returns a paginated instance with metadata.
            }
        );
    }

    /**
     * Add new program funding to the database.
     *
     * @param array $arraydata
     *
     * @return ProgramFunding $programFunding
     */
    public function createProgramFunding(array $data)
    {
        return DB::transaction(function () use ($data) {
            $programFunding = ProgramFunding::create($data);
            return $programFunding;
        });
    }

    /**
     * Get a single program funding with its relationships.
     *
     * @param  ProgramFunding $programFunding
     *
     * @return ProgramFunding $programFunding
     */
    public function showProgramFunding(ProgramFunding $programFunding)
    {
        $cacheKey=self::TAG_PROGRAM_FUNDING_PREFIX."details_{$programFunding->id}".'_'.app()->getLocale();
        $programFundingTag=self::TAG_PROGRAM_FUNDING_PREFIX.$programFunding->id;
        return Cache::tags([self::TAG_PROGRAM_FUNDINGS_GLOBAL, $programFundingTag])->remember($cacheKey, self::CACHE_TTL, function () use ($programFunding) {
            return $programFunding->load([['donorEntity','program']])->toArray();
        });
    }

    /**
     * Update the specified program funding in the database.
     *
     * @param array $arraydata
     * @param  ProgramFunding $programFunding
     *
     * @return ProgramFunding $programFunding
     */
    public function updateProgramFunding(array $data, ProgramFunding $programFunding)
    {
        return DB::transaction(function () use ($data,$programFunding) {
            $programFunding->update($data);
            return $programFunding->refresh();
        });
    }

    /**
     * Delete the specified program funding from the database.
     *
     * @param ProgramFunding $programFunding
     *
     */
    public function deleteProgramFunding(ProgramFunding $programFunding)
    {
        $programFunding->delete();
    }

}


