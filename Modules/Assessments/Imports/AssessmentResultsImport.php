<?php

namespace Modules\Assessments\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

/**
 * Class AssessmentResultsImport
 * * Implements Excel data transformation into a Laravel Collection.
 * Utilizes a heading row to map columns to associative array keys.
 * * @package Modules\Assessments\Imports
 */
class AssessmentResultsImport implements ToCollection, WithHeadingRow
{
    /**
     * Return the raw collection of rows from the Excel sheet.
     * * @param Collection $rows The collection of rows parsed from the file.
     * @return Collection
     */
    public function collection(Collection $rows)
    {
        return $rows;
    }
}
