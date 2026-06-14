<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('user')->after('password')->index();
            $table->string('phone')->nullable()->after('email')->index();
            $table->string('whatsapp_id')->nullable()->after('phone')->unique();
            $table->timestamp('whatsapp_linked_at')->nullable()->after('whatsapp_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['role', 'phone', 'whatsapp_id', 'whatsapp_linked_at']);
        });
    }
};
