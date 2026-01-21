<?php

namespace Modules\Assessments\Services\V1;

use Illuminate\Http\UploadedFile;
use Modules\Assessments\Jobs\ProcessAssessmentSheetJob;

/**
 * Class AssessmentImportService
 * * This service is responsible for managing the importation of external assessment files.
 * It acts as an intermediary layer to receive uploaded files and prepare them
 * for asynchronous processing via the system queue.
 * * @package Modules\Assessments\Services\V1
 */
class AssessmentImportService
{

    /**
     * Handle the uploaded assessment import file.
     *
     * This method persists the uploaded file to temporary storage and dispatches
     * a background job to process the data asynchronously, ensuring minimal
     * latency for the API response.
     *
     * @param UploadedFile $file The uploaded file instance from the HTTP request (Excel/CSV).
     * @param int $issueTypeId The unique identifier of the issue type associated with this assessment.
     * * @return void
     * * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException If the file storage process fails.
     */
    public function handleImport(UploadedFile $file, int $issueTypeId): void
    {
        $path = $file->store('temp_imports');
        ProcessAssessmentSheetJob::dispatch($path, $issueTypeId);
    }
}
