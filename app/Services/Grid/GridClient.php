<?php

namespace App\Services\Grid;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GridClient
{
    public function query(string $query, array $variables = [], ?string $url = null): array
    {
        $response = Http::withHeaders([
            'x-api-key' => config('services.grid.key'),
            'Content-Type' => 'application/json',
        ])
            ->timeout(15)
            ->retry(2, 200)
            ->post($url ?? config('services.grid.url'), [
                'query' => $query,
                'variables' => $variables,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('GRID API HTTP error: ' . $response->status());
        }

        $json = $response->json();

        if (! empty($json['errors'])) {
            throw new RuntimeException('GRID API GraphQL error: ' . json_encode($json['errors']));
        }

        return $json['data'] ?? [];
    }
}
