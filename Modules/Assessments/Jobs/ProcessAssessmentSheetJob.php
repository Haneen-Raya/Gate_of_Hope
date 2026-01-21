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

class ProcessAssessmentSheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected string $filePath, protected int $issueTypeId) {}

    public function handle()
    {
        $data = Excel::toCollection(new AssessmentResultsImport, Storage::path($this->filePath))->first();


        $rules = PriorityRules::where('issue_type_id', $this->issueTypeId)->get();

        foreach ($data as $row) {
            $nationalId = $row['enter_your_national_id_number'];
            $beneficiary = Beneficiary::where('national_id', $nationalId)->first();

            if (!$beneficiary) continue;

            $scoreData = $this->parseScoreColumn($row['النتيجة']);
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


    private function parseScoreColumn($rawScore)
    {
        $parts = explode('/', $rawScore);
        return [
            'score' => isset($parts[0]) ? (int) trim($parts[0]) : 0,
            'max'   => isset($parts[1]) ? (int) trim($parts[1]) : 0,
        ];
    }
}
