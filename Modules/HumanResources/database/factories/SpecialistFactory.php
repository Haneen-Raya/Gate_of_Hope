<?php

namespace Modules\HumanResources\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Assessments\Models\IssueCategory;
use Modules\Core\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\HumanResources\Models\Specialist>
 */
class SpecialistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->date(),
            'user_id' => User::factory(),
            'issue_category_id' => IssueCategory::factory(),
        ];
    }
}
