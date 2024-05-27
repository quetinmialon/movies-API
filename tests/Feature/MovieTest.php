<?php

namespace Tests\Feature;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MovieTest extends TestCase
{
    use RefreshDatabase;

    protected const array aloneStructure = [
        'id',
        'title',
        'synopsis',
        'duration',
        'release',
        'created_at',
        'updated_at',
        'director' => [
            'id',
            'name',
        ],
        'actors' => [
            '*' => [
                'id',
                'name',
            ],
        ],
    ];

    protected const array listStructure = [
        'id',
        'title',
        'synopsis',
        'duration',
        'release',
    ];

    public function test_can_get_movies_list_with_pagination(): void
    {
        Movie::factory(30)->create([
            'director_id' => Director::factory()->create(),
        ]);

        $response = $this->getJson('/api/movies');

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(15, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => static::listStructure,
                ],
            ]);
    }

    public function test_can_get_movie_by_id(): void
    {
        $movie = Movie::factory()->create([
            'director_id' => Director::factory()->create(),
        ]);

        $movie->actors()->attach(Actor::factory(3)->create());

        $response = $this->getJson('/api/movies/'.$movie->id);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => static::aloneStructure,
            ])
            ->assertJsonCount(3, 'data.actors');
    }

    public function test_can_create_movie(): void
    {
        $attributes = [
            'title' => 'Test Movie',
            'synopsis' => 'A wonderful movie',
            'duration' => 5,
            'release' => '2024-05-12',
        ];

        $relationships = [
            'director_id' => Director::factory()->create()->id,
            'actors' => Actor::factory(3)->create()->pluck('id'),
        ];

        $response = $this->postJson('/api/movies', $attributes + $relationships);

        $this->assertDatabaseHas('movies', $attributes);

        $response
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => static::aloneStructure,
            ])
            ->assertJson([
                'data' => $attributes + [
                    'director' => [
                        'id' => $relationships['director_id'],
                    ],
                    'actors' => array_map(
                        fn (int $actorId) => ['id' => $actorId],
                        $relationships['actors']->toArray()
                    ),
                ],
            ]);
    }

    public function test_can_update_movie(): void
    {
        $movie = Movie::factory()->create([
            'director_id' => Director::factory()->create(),
        ]);

        $movie->actors()->attach(Actor::factory(2)->create());

        $newAttributes = [
            'synopsis' => 'A wonderful movie',
        ];

        $newRelationships = [
            'director_id' => Director::factory()->create()->id,
            'actors' => Actor::factory(3)->create()->pluck('id'),
        ];

        $response = $this->patchJson('/api/movies/'.$movie->id, $newAttributes + $newRelationships);

        $this->assertDatabaseHas('movies', $newAttributes);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => static::aloneStructure,
            ])
            ->assertJson([
                'data' => $newAttributes + [
                    'director' => [
                        'id' => $newRelationships['director_id'],
                    ],
                    'actors' => array_map(
                        fn (int $actorId) => ['id' => $actorId],
                        $newRelationships['actors']->toArray()
                    ),
                ],
            ]);
    }

    public function test_can_delete_movie(): void
    {
        $movie = Movie::factory()->create([
            'director_id' => Director::factory()->create(),
        ]);

        $response = $this->deleteJson('/api/movies/'.$movie->id);

        $this->assertDatabaseMissing('movies', ['id' => $movie->id]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
