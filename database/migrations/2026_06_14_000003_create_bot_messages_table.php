<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bot_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bot_contact_id')->constrained()->cascadeOnDelete();
            $table->string('direction')->default('inbound')->index();
            $table->string('message_type')->default('text');
            $table->text('content')->nullable();
            $table->json('payload')->nullable();
            $table->string('external_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bot_messages');
    }
};
