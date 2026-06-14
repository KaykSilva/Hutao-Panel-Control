<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shared_account_participants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('shared_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('whatsapp_group_member_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount_cents');
            $table->unsignedInteger('paid_amount_cents')->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->unique(['shared_account_id', 'whatsapp_group_member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_account_participants');
    }
};
