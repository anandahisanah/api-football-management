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
        Schema::create('teams', function (Blueprint $table) {
            // pk
            $table->uuid('id')->primary();
            // column
            $table->string('name');
            $table->string('logo_url')->nullable();
            $table->string('founding_year');
            $table->string('address');
            $table->string('city');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('players', function (Blueprint $table) {
            // pk
            $table->uuid('id')->primary();
            // fk
            $table->foreignUuid('team_id')->constrained('teams')->onDelete('cascade');
            // column
            $table->string('name');
            $table->integer('body_height');
            $table->integer('body_weight');
            $table->string('position');
            $table->integer('back_number');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('games', function (Blueprint $table) {
            // pk
            $table->uuid('id')->primary();
            // fk
            $table->foreignUuid('home_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignUuid('away_team_id')->constrained('teams')->onDelete('cascade');
            // column
            $table->string('location');
            $table->dateTime('datetime');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('scores', function (Blueprint $table) {
            // pk
            $table->uuid('id')->primary();
            // fk
            $table->foreignUuid('game_id')->constrained('games')->onDelete('cascade');
            $table->foreignUuid('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignUuid('player_id')->constrained('players')->onDelete('cascade');
            // column
            $table->dateTime('datetime');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
        Schema::dropIfExists('games');
        Schema::dropIfExists('players');
        Schema::dropIfExists('teams');
    }
};
