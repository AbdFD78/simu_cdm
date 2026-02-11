<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matchs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('phase_id')
                ->constrained('phases')
                ->cascadeOnDelete();

            $table->foreignId('poule_id')
                ->nullable()
                ->constrained('poules')
                ->cascadeOnDelete();

            $table->foreignId('equipe_a_id')
                ->constrained('equipes')
                ->restrictOnDelete();

            $table->foreignId('equipe_b_id')
                ->constrained('equipes')
                ->restrictOnDelete();

            $table->dateTime('date_heure')->nullable();

            // scheduled | live | finished
            $table->string('statut', 20)->default('scheduled');

            $table->unsignedInteger('score_equipe_a')->default(0);
            $table->unsignedInteger('score_equipe_b')->default(0);

            $table->string('stade')->nullable();
            $table->string('ville')->nullable();

            $table->timestamps();

            $table->index('phase_id');
            $table->index('poule_id');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matchs');
    }
};

