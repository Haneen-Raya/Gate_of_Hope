<?php

namespace App\Support\MediaLibrary;

use Illuminate\Support\Pluralizer;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * @class CustomPathGenerator
 * 
 * Defines a structured and human-readable storage hierarchy for Spatie MediaLibrary.
 * Instead of the default numeric IDs, this generator organizes files by Model, ID, and Collection.
 * 
 * @Structure {plural_model_name}/{model_id}/{collection_name}/{media_id}/...
 * @Example beneficiaries/105/identities/42/original_file.jpg
 */
class CustomPathGenerator implements PathGenerator
{
    /**
     * Get the path for the original media file.
     *
     * @param Media $media
     * @return string
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    /**
     * Get the path for media conversions (e.g., thumbnails, optimized versions).
     *
     * @param Media $media
     * @return string
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    /**
     * Get the path for responsive image definitions.
     *
     * @param Media $media
     * @return string
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive/';
    }

    /**
     * Generate a deterministic and organized base path for the media.
     * * Logic:
     * 1. Resolves the plural name of the model (e.g., 'Beneficiary' becomes 'beneficiaries').
     * 2. Categorizes by the specific Model ID and the Media Collection name.
     * 3. Appends the Media ID as a leaf directory to prevent filename collisions.
     * * @param Media $media
     * @return string
     */
    protected function getBasePath(Media $media): string
    {
        // Resulting structure: beneficiaries/105/identities/42
        $modelFolder = Pluralizer::plural(strtolower(class_basename($media->model_type)));
        $collectionName = $media->collection_name;

        return "{$modelFolder}/{$media->model_id}/{$collectionName}/{$media->id}";
    }
}
