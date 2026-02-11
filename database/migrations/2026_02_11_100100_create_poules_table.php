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
        Schema::create('poules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phase_id')
                ->constrained('phases')
                ->cascadeOnDelete();
            $table->string('nom', 10);
            $table->timestamps();

            $table->index(['phase_id', 'nom']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poules');
    }
};

