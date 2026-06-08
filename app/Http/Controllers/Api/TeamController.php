<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct(
        private readonly TeamService $teamService,
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->teamService->index());
    }

    public function show(Team $team): JsonResponse
    {
        return response()->json($this->teamService->show($team));
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'logo' => ['required', 'string', 'max:2048'],
            'ranking' => ['nullable', 'integer', 'min:1'],
        ]);

        return response()->json($this->teamService->create($validated), 201);
    }

    public function update(Request $request, Team $team): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'country' => ['sometimes', 'required', 'string', 'max:255'],
            'logo' => ['sometimes', 'required', 'string', 'max:2048'],
            'ranking' => ['nullable', 'integer', 'min:1'],
        ]);

        return response()->json($this->teamService->update($team, $validated));
    }
}
