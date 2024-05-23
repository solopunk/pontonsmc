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
        Schema::create('boats', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('homeport_id')->constrained()->restrictOnDelete();
            $table->foreignId('boat_type')->constrained()->restrictOnDelete();

            $table->string('name');
            $table->string('brand');
            $table->string('model');
            $table->date('year');
            $table->float('length');
            $table->float('width');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boats');
    }
};
