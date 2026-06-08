<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_a_id')->constrained('teams')->restrictOnDelete();
            $table->foreignId('team_b_id')->constrained('teams')->restrictOnDelete();
            $table->dateTime('date_time')->nullable();
            $table->unsignedBigInteger('tournament_id')->index();
            $table->unsignedTinyInteger('format');
            $table->string('html_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
