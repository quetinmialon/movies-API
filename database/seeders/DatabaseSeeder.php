<?php

namespace Database\Seeders;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Movie;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $directors = Director::factory(20)->create();
        $actors = Actor::factory(150)->create();

        Movie::factory(50)
            ->sequence(fn ($sequence) => [
                'director_id' => $directors->random(),
            ])
            ->create()
            ->each(fn ($movie) => $movie->actors()->attach($actors->random(5)));
    }
}
