<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $collection) {
            $collection->sparse_and_unique('grid_id');
        });

        Schema::create('players', function (Blueprint $collection) {
            $collection->sparse_and_unique('grid_id');
            $collection->index('team_id');
        });

        Schema::create('tournaments', function (Blueprint $collection) {
            $collection->sparse_and_unique('grid_id');
        });

        Schema::create('matches', function (Blueprint $collection) {
            $collection->sparse_and_unique('grid_id');
            $collection->index('team_a.id');
            $collection->index('team_b.id');
            $collection->index('tournament.id');
        });

        Schema::create('maps', function (Blueprint $collection) {
            $collection->index('match_id');
            $collection->index('winner_team.id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('teams');
        Schema::drop('players');
        Schema::drop('tournaments');
        Schema::drop('matches');
        Schema::drop('maps');
    }
};
