<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Map;
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
            'match_id' => ['required', 'integer', 'exists:matches,id'],
            'pick' => ['required', 'integer', 'min:1'],
            'winner_team_id' => ['nullable', 'integer', 'exists:teams,id'],
        ]);

        return response()->json($this->mapService->create($validated), 201);
    }

    public function update(Request $request, Map $map): JsonResponse
    {
        $validated = $request->validate([
            'map' => ['sometimes', 'required', 'string', 'max:255'],
            'score' => ['sometimes', 'required', 'array'],
            'match_id' => ['sometimes', 'required', 'integer', 'exists:matches,id'],
            'pick' => ['sometimes', 'required', 'integer', 'min:1'],
            'winner_team_id' => ['nullable', 'integer', 'exists:teams,id'],
        ]);

        return response()->json($this->mapService->update($map, $validated));
    }
}
