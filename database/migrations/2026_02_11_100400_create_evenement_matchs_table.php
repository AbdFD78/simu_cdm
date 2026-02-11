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
        Schema::create('evenement_matchs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('match_id')
                ->constrained('matchs')
                ->cascadeOnDelete();

            $table->unsignedInteger('minute')->default(0);

            // goal | yellow_card | red_card | substitution
            $table->string('type', 30);

            $table->string('description')->nullable();

            $table->timestamps();

            $table->index(['match_id', 'minute']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evenement_matchs');
    }
};

