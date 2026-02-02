<?php

namespace App\Contracts;

/**
 * @interface HasCaseEvents
 *
 * Directs the architectural binding between an Eloquent Model and its 
 * corresponding Event Formatter. Implementing this contract signals that 
 * the model is an active participant in the Case Management Timeline ecosystem.
 *
 * @package App\Contracts
 */
interface HasCaseEvents
{
    /**
     * Define the specialized Formatter class for this model.
     * * @return string The fully qualified class name (FQCN) of the formatter.
     * @example \Modules\CaseManagement\Services\CaseEvent\Formatter\BeneficiaryCaseFormatter::class
     */
    public function caseEventFormatter(): string;
}
