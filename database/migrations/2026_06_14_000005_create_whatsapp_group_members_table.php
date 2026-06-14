<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_group_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('whatsapp_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('phone')->nullable()->index();
            $table->string('whatsapp_id')->index();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->unique(['whatsapp_group_id', 'whatsapp_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_group_members');
    }
};
