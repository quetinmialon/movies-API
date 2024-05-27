<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieRequest;
use App\Http\Resources\MovieResource;
use App\Models\Movie;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return MovieResource::collection(
            Movie::list()
                ->without('director', 'actors')
                ->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieRequest $request)
    {
        $movie = Movie::create($request->validated());

        $movie->actors()->attach($request->validated('actors') ?? null);

        $movie->load('director', 'actors');

        return new MovieResource($movie);
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        return new MovieResource($movie);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MovieRequest $request, Movie $movie)
    {
        $movie->update($request->validated());

        if ($request->validated('actors')) {
            $movie->actors()->sync($request->validated('actors'));
        }

        // Permet de rafraîchir le modèle et ses relations (pratique pour actors sinon on aurait l'ancienne liste d'acteurs)
        $movie->refresh();

        return new MovieResource($movie);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        $movie->delete();

        return response()->noContent();
    }
}
