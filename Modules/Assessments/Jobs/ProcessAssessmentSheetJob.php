<?php

namespace Modules\Assessments\Jobs;

use Illuminate\Bus\Queueable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Assessments\Models\PriorityRules;
use Modules\Beneficiaries\Models\Beneficiary;
use Modules\Assessments\Models\AssessmentResult;
use Modules\Assessments\Imports\AssessmentResultsImport;

/**
 * Class ProcessAssessmentSheetJob
 * * Background job responsible for processing uploaded assessment Excel sheets.
 * It performs the following operations:
 * 1. Parses the file and matches beneficiaries by national ID.
 * 2. Extracts raw scores and calculates normalized percentages.
 * 3. Matches scores against PriorityRules to suggest a priority level.
 * 4. Persists results and cleans up temporary files.
 * * @package Modules\Assessments\Jobs
 */
class ProcessAssessmentSheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * * @param string $filePath The relative path to the temporary stored Excel file.
     * @param int $issueTypeId The ID of the issue type being assessed.
     */
    public function __construct(protected string $filePath, protected int $issueTypeId) {}

    /**
     * Execute the job.
     * * Iterates through the Excel rows, performs lookups, and calculates
     * assessment metrics for each valid beneficiary.
     * * @return void
     */
    public function handle()
    {
        $data = Excel::toCollection(new AssessmentResultsImport, Storage::path($this->filePath))->first();


        $rules = PriorityRules::where('issue_type_id', $this->issueTypeId)->get();

        foreach ($data as $row) {

            $nationalId = $row['enter_your_national_id_number'];
            $nationalId = hash('sha256', $nationalId);
            $beneficiary = Beneficiary::where('identity_hash', $nationalId)->first();

            if (!$beneficiary) continue;
            
            $scoreData = $this->parseScoreColumn($row['alntyg']);
            $actualScore = $scoreData['score'];
            $maxScore = $scoreData['max'];

            $normalized = ($maxScore > 0) ? ($actualScore / $maxScore) * 100 : 0;

            $suggestedPriority = $rules->where('min_score', '<=', $actualScore)
                ->where('max_score', '>=', $actualScore)
                ->first()?->priority ?? 'Undefined';

            AssessmentResult::create([
                'beneficiary_id'     => $beneficiary->id,
                'issue_type_id'      => $this->issueTypeId,
                'score'              => $actualScore,
                'max_score'          => $maxScore,
                'normalized_score'   => $normalized,
                'priority_suggested' => $suggestedPriority,
                'is_latest'          => 1,
                'assessed_at'        => now(),
            ]);
        }

        Storage::delete($this->filePath);
    }

    /**
     * Parse the raw score string (e.g., "15/20") into an associative array.
     * * Splits the string by the '/' delimiter to extract the actual score
     * and the total possible maximum score.
     * * @param string|null $rawScore The raw score string from the Excel cell.
     * @return array{score: int, max: int}
     */
    private function parseScoreColumn($rawScore)
    {
        $parts = explode('/', $rawScore);
        return [
            'score' => isset($parts[0]) ? (int) trim($parts[0]) : 0,
            'max'   => isset($parts[1]) ? (int) trim($parts[1]) : 0,
        ];
    }
}
