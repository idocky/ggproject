<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameMatch;
use App\Models\Map;
use App\Models\Team;
use App\Rules\MongoExists;
use App\Services\MapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function __construct(
        private readonly MapService $mapService,
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->mapService->index());
    }

    public function show(Map $map): JsonResponse
    {
        return response()->json($this->mapService->show($map));
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'map' => ['required', 'string', 'max:255'],
            'score' => ['required', 'array'],
            'match_id' => ['required', 'string', new MongoExists(GameMatch::class)],
            'pick' => ['required', 'integer', 'min:1'],
            'winner_team_id' => ['nullable', 'string', new MongoExists(Team::class)],
        ]);

        return response()->json($this->mapService->create($validated), 201);
    }

    public function update(Request $request, Map $map): JsonResponse
    {
        $validated = $request->validate([
            'map' => ['sometimes', 'required', 'string', 'max:255'],
            'score' => ['sometimes', 'required', 'array'],
            'match_id' => ['sometimes', 'required', 'string', new MongoExists(GameMatch::class)],
            'pick' => ['sometimes', 'required', 'integer', 'min:1'],
            'winner_team_id' => ['nullable', 'string', new MongoExists(Team::class)],
        ]);

        return response()->json($this->mapService->update($map, $validated));
    }
}
