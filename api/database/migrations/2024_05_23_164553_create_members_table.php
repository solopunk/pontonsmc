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
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            $table->string('email');
            $table->string('password')->nullable();
            $table->string('first');
            $table->string('last');
            $table->string('birthdate');
            $table->string('address');
            $table->string('postal_code');
            $table->string('city');
            $table->string('phone');
            $table->string('job');
            $table->boolean('pending')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
