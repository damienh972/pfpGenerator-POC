<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Layer>
 */
class LayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $image_cids = [];
        for ($i = 0; $i < rand(1, 3); $i++) {
            $image_cids[] = [
                'cid' => $this->faker->unique()->lexify('CID????????'),
                'name' => $this->faker->word(),
            ];
        }

        return [
            'name' => $this->faker->word(),
            'image_cids' => json_encode($image_cids),
        ];
    }
}
