<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Services\TournamentService;
use Illuminate\Http\JsonResponse;

class TournamentController extends Controller
{
    public function __construct(
        private readonly TournamentService $tournamentService,
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->tournamentService->index());
    }

    public function show(Tournament $tournament): JsonResponse
    {
        return response()->json($this->tournamentService->show($tournament));
    }
}
