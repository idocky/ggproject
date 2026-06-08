<?php

namespace App\Services;

use App\Models\Player;
use Illuminate\Database\Eloquent\Collection;

class PlayerService
{
    /**
     * @return Collection<int, Player>
     */
    public function index(): Collection
    {
        return Player::query()
            ->with('team')
            ->orderBy('nickname')
            ->get();
    }

    public function show(Player $player): Player
    {
        return $player->load('team');
    }

    public function create(array $data): Player
    {
        $player = Player::query()->create($data);

        return $player->load('team');
    }

    public function update(Player $player, array $data): Player
    {
        $player->update($data);

        return $player->load('team');
    }
}
