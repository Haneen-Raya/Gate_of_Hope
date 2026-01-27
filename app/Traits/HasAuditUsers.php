<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\User;

/**
 * Trait HasAuditUsers
 *
 * This trait automates the recording of user IDs for model creation and updates.
 *
 * ---
 * ### USAGE INSTRUCTIONS:
 * 1. Use this trait in your Eloquent model: `use HasAuditUsers;`
 * 2. By default, it looks for `created_by` and `updated_by` columns.
 * 3. To override column names, define these protected properties in your model:
 * - `protected string $createdByField = 'your_custom_column';`
 * - `protected string $updatedByField = 'your_custom_column';`
 * ---
 *
 * @package App\Traits
 */
trait HasAuditUsers
{

    /**
     * Boot the trait.
     * 
     * Hooks into the Eloquent lifecycle to automatically assign Auth::id()
     * to the designated audit columns during 'creating' and 'updating' events.
     *
     * @return void
     */
    public static function bootHasAuditUsers()
    {
        // Handle the creation event
        static::creating(function ($model) {
            $creatorField = $model->getCreatedByField();
            $updaterField = $model->getUpdatedByField();

            // Set the ID only if the field exists and hasn't been manually set
            if (($creatorField && empty($model->{$creatorField})) && ($updaterField && empty($model->{$updaterField}))) {
                $model->{$creatorField} = $model->{$updaterField} = Auth::id();
            }
        });

        // Handle the updating event
        static::updating(function ($model) {
            $updaterField = $model->getUpdatedByField();

            if ($updaterField) {
                $model->{$updaterField} = Auth::id();
            }
        });
    }

    /**
     * Retrieve the column name for the creator.
     * 
     * Checks if a custom 'createdByField' property is defined in the model,
     * otherwise defaults to 'created_by'.
     *
     * @return string|null
     */
    protected function getCreatedByField(): ?string
    {
        return property_exists($this, 'createdByField')
            ? $this->createdByField
            : 'created_by';
    }

    /**
     * Retrieve the column name for the updater.
     * 
     * Checks if a custom 'updatedByField' property is defined in the model,
     * otherwise defaults to 'updated_by'.
     *
     * @return string|null
     */
    protected function getUpdatedByField(): ?string
    {
        return property_exists($this, 'updatedByField')
            ? $this->updatedByField
            : 'updated_by';
    }

    /**
     * Get the user who created the record.
     * 
     * @uses getCreatedByField() To resolve the foreign key dynamically.
     * 
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, $this->getCreatedByField());
    }

    /**
     * Get the user who last updated the record.
     * 
     * @uses getUpdatedByField() To resolve the foreign key dynamically.
     * 
     * @return BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, $this->getUpdatedByField());
    }
}
