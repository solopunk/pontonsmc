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
        Schema::create('coowners', function (Blueprint $table) {
            $table->id();

            $table->foreignId('boat_id')->constrained()->cascadeOnDelete();

            $table->string('first');
            $table->string('last');
            $table->string('nationality');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coowners');
    }
};
