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
        Schema::create('mails', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mail_type_id')
                ->constrained()->restrictOnDelete();

            $table->string('title');
            $table->json('content_json');
            $table->string('content_html');
            $table->json('to');
            $table->boolean('sended');
            $table->boolean('sheet');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};
