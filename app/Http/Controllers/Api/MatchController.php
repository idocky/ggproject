<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameMatch;
use App\Services\MatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MatchController extends Controller
{
    public function __construct(
        private readonly MatchService $matchService,
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->matchService->index());
    }

    public function show(GameMatch $match): JsonResponse
    {
        return response()->json($this->matchService->show($match));
    }

    public function recentMatches(GameMatch $match): JsonResponse
    {
        return response()->json($this->matchService->recentMatches($match));
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $this->validateMatch($request);

        return response()->json($this->matchService->create($validated), 201);
    }

    public function update(Request $request, GameMatch $match): JsonResponse
    {
        $validated = $this->validateMatch($request, $match);

        return response()->json($this->matchService->update($match, $validated));
    }

    /**
     * @return array<string, mixed>
     */
    private function validateMatch(Request $request, ?GameMatch $match = null): array
    {
        $rules = [
            'team_a_id' => [$match ? 'sometimes' : 'required', 'required', 'integer', 'exists:teams,id'],
            'team_b_id' => [$match ? 'sometimes' : 'required', 'required', 'integer', 'exists:teams,id'],
            'date_time' => ['nullable', 'date'],
            'tournament_id' => [$match ? 'sometimes' : 'required', 'required', 'integer', 'min:1'],
            'format' => [$match ? 'sometimes' : 'required', 'required', 'integer', 'min:1'],
            'html_url' => ['nullable', 'url', 'max:2048'],
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request, $match): void {
            $teamA = $request->input('team_a_id', $match?->team_a_id);
            $teamB = $request->input('team_b_id', $match?->team_b_id);

            if ($teamA !== null && $teamB !== null && (int) $teamA === (int) $teamB) {
                $validator->errors()->add('team_b_id', 'The team_b_id field must be different from team_a_id.');
            }
        });

        return $validator->validate();
    }
}
