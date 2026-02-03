<?php

namespace App\Traits;

/**
 * Trait InteractsWithEnums
 * * Standardizes the transformation of Enum attributes within Eloquent Models 
 * for optimized API output.
 */
trait InteractsWithEnums
{
    /**
     * Transform specified Enum fields into structured data objects.
     * * @param array $array The serialized model attributes (usually from parent::toArray()).
     * @param array $enumFields List of attributes to be treated as Enums.
     * @return array
     */
    protected function transformEnums(array $array, array $enumFields): array
    {
        foreach ($enumFields as $field) {
            
            // Verify the field is set and is a valid PHP Enum
            if (isset($this->$field) && $this->$field instanceof \UnitEnum) {
                $array[$field] = [
                    'value' => $this->$field->value ?? $this->$field->name,
                    'label' => method_exists($this->$field, 'label')
                        ? $this->$field->label()
                        : $this->$field->name,
                ];
            }
        }
        return $array;
    }
}
