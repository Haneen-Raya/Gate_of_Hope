<?php

namespace Modules\Assessments\Services\V1;

use Illuminate\Http\UploadedFile;
use Modules\Assessments\Jobs\ProcessAssessmentSheetJob;


class AssessmentImportService
{

    public function handleImport(UploadedFile $file, int $issueTypeId): void
    {
        $path = $file->store('temp_imports');
        ProcessAssessmentSheetJob::dispatch($path, $issueTypeId);
    }
}
