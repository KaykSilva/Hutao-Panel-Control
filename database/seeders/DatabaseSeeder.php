<?php

namespace Database\Seeders;

use App\Models\BotSetting;
use App\Models\User;
use App\Models\WhatsappGroup;
use App\Models\WhatsappGroupMember;
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
        $admin = User::query()->updateOrCreate(['email' => 'admin@hutao.local'], [
            'name' => 'Administrador',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $user = User::query()->updateOrCreate(['email' => 'usuario@hutao.local'], [
            'name' => 'Usuario Demo',
            'phone' => '5585999999999',
            'password' => 'password',
            'role' => 'user',
        ]);

        BotSetting::query()->firstOrCreate(['key' => 'api_token'], ['value' => Str::random(48)]);
        BotSetting::query()->updateOrCreate(['key' => 'bot_name'], ['value' => 'Hutao Bot']);
        BotSetting::query()->updateOrCreate(['key' => 'welcome_message'], ['value' => 'Ola! Como posso ajudar?']);

        $group = WhatsappGroup::query()->firstOrCreate(
            ['whatsapp_id' => 'demo-grupo'],
            ['name' => 'Grupo Demo'],
        );

        WhatsappGroupMember::query()->updateOrCreate(
            ['whatsapp_group_id' => $group->id, 'whatsapp_id' => '30417226834026'],
            ['user_id' => $admin->id, 'name' => 'Administrador', 'phone' => '98981895794'],
        );

        WhatsappGroupMember::query()->updateOrCreate(
            ['whatsapp_group_id' => $group->id, 'whatsapp_id' => 'demo-usuario'],
            ['user_id' => $user->id, 'name' => 'Usuario Demo', 'phone' => '5585999999999'],
        );
    }
}
