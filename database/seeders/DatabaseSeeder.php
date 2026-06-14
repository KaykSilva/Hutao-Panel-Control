<?php

namespace Database\Seeders;

use App\Models\BotSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(['email' => 'admin@hutao.local'], [
            'name' => 'Administrador',
            'password' => 'password',
            'role' => 'admin',
        ]);

        User::query()->updateOrCreate(['email' => 'usuario@hutao.local'], [
            'name' => 'Usuario Demo',
            'phone' => '5585999999999',
            'password' => 'password',
            'role' => 'user',
        ]);

        BotSetting::query()->firstOrCreate(['key' => 'api_token'], ['value' => Str::random(48)]);
        BotSetting::query()->updateOrCreate(['key' => 'bot_name'], ['value' => 'Hutao Bot']);
        BotSetting::query()->updateOrCreate(['key' => 'welcome_message'], ['value' => 'Ola! Como posso ajudar?']);
    }
}
