<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Client::factory(1)->create()->each(
            function ($client) {
                $client->users()->saveMany(
                    \App\Models\User::factory(4)->make()
                )->each(function ($user) {
                    $user->projects()->saveMany(
                        \App\Models\Project::factory(rand(1, 3))->make()
                    )->each(function ($project) {

                        $layers = \App\Models\Layer::factory(rand(2, 5))->make()->each(function ($layer, $index) {
                            $layer->index = $index;
                        });

                        $project->layers()->saveMany($layers);
                    });
                });
            }
        );
    }
}
