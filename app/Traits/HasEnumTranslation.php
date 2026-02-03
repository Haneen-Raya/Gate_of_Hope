<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait HasEnumTranslation
 * * Facilitates automatic localization for string-backed Enums.
 */
trait HasEnumTranslation
{
    /**
     * Retrieve the localized label for the current Enum case.
     * * @return string
     */
    public function label(): string
    {
        // Convert 'DisabilityType' to 'disability_type'
        $className = Str::snake(class_basename($this));

        // Construct key: enums.disability_type.visual
        $translationKey = "enums." . $className . "." . $this->value;

        return __($translationKey);
    }
}
