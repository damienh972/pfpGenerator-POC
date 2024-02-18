<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'deleted_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
