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
        Schema::create('classements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('poule_id')
                ->constrained('poules')
                ->cascadeOnDelete();

            $table->foreignId('equipe_id')
                ->constrained('equipes')
                ->cascadeOnDelete();

            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('matchs_joues')->default(0);
            $table->unsignedInteger('victoires')->default(0);
            $table->unsignedInteger('nuls')->default(0);
            $table->unsignedInteger('defaites')->default(0);
            $table->unsignedInteger('buts_marques')->default(0);
            $table->unsignedInteger('buts_encaissees')->default(0);

            $table->timestamps();

            $table->unique(['poule_id', 'equipe_id']);
            $table->index('equipe_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classements');
    }
};

