<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\PlayerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function __construct(
        private readonly PlayerService $playerService,
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->playerService->index());
    }

    public function show(Player $player): JsonResponse
    {
        return response()->json($this->playerService->show($player));
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nickname' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'team_id' => ['required', 'integer', 'exists:teams,id'],
        ]);

        return response()->json($this->playerService->create($validated), 201);
    }

    public function update(Request $request, Player $player): JsonResponse
    {
        $validated = $request->validate([
            'nickname' => ['sometimes', 'required', 'string', 'max:255'],
            'full_name' => ['sometimes', 'required', 'string', 'max:255'],
            'country' => ['sometimes', 'required', 'string', 'max:255'],
            'team_id' => ['sometimes', 'required', 'integer', 'exists:teams,id'],
        ]);

        return response()->json($this->playerService->update($player, $validated));
    }
}
