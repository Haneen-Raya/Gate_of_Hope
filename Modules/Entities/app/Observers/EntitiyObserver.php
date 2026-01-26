<?php

namespace Modules\Entities\Observers;

use Modules\Entities\Models\Entitiy;

/**
 * Class EntityObserver
 *
 * Observer for the Entity model to handle intelligent code generation.
 *
 * Generates a code from the entity name based on the following rules:
 *  - Split the name into words (by space)
 *  - Take first 2 letters from each word (if possible)
 *  - If the result is less than 3 chars, pad with 'X'
 *  - Ensure uniqueness by adding numeric suffix if needed
 *
 * @package Modules\Core\Observers
 */
class EntitiyObserver
{
    /**
     * Handle the "creating" event of the Entity model.
     *
     *  This method is automatically triggered by Laravel when a new Entity instance
     * is being created. If the 'code' attribute is empty, it generates a unique code
     * using the intelligent algorithm based on the entity name.
     *
     * @param Entitiy $entitiy
     *
     * @return void
     */
    public function creating(Entitiy $entitiy): void
    {
        // Only generate a code if it's not manually provided
        if (empty($entitiy->code)) {
            $entitiy->code = $this->generateIntelligentCode($entitiy->name);
        }
    }

    /**
     * Generate a unique and intelligent code based on the entity name.
     *
     * Algorithm:
     * 1. Split the name into words (space-separated)
     * 2. Take the first 2 letters from each word
     * 3. Convert to uppercase
     * 4. Pad with 'X' if the resulting code is less than 3 characters
     * 5. Check for uniqueness in the database
     * 6. Append numeric suffix if a duplicate exists
     *
     * Examples:
     *  - "Red Cross" → "REDC"
     *  - "United Nations" → "UNNA"
     *  - "Li" → "LIX"
     *  - "Red Cross" when "REDC" exists → "REDC1"
     *
     * @param string $name The entity name to generate the code from
     *
     * @return string A unique, uppercase code ready for database insertion
     */
    protected function generateIntelligentCode(string $name): string
    {
         // Split the name by space
        $words = preg_split('/\s+/', $name);

        $code = '';

        foreach ($words as $word) {
            $code .= strtoupper(substr($word, 0, 2)); // First 2 letters of each word
        }

        // Ensure minimum length of 3
        $code = str_pad($code, 3, 'X');

        $baseCode = $code;
        $counter = 1;

        // Ensure uniqueness
        while (Entitiy::where('code', $code)->exists()) {
            $code = $baseCode . $counter;
            $counter++;
        }

        return $code;
    }
}
