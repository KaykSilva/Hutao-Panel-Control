<?php

namespace Tests\Feature;

use App\Models\BotSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Admin');
    }

    public function test_regular_user_cannot_open_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_bot_can_store_message_with_valid_token(): void
    {
        BotSetting::query()->create([
            'key' => 'api_token',
            'value' => 'secret-token',
        ]);

        $this->withHeader('X-Bot-Token', 'secret-token')
            ->postJson('/api/bot/messages', [
                'phone' => '5585999999999',
                'name' => 'Usuario Demo',
                'content' => 'Oi',
            ])
            ->assertCreated()
            ->assertJson(['stored' => true]);

        $this->assertDatabaseHas('bot_contacts', ['phone' => '5585999999999']);
        $this->assertDatabaseHas('bot_messages', ['content' => 'Oi', 'direction' => 'inbound']);
    }

    public function test_bot_can_find_user_using_phone_variants(): void
    {
        BotSetting::query()->create([
            'key' => 'api_token',
            'value' => 'secret-token',
        ]);

        $user = User::factory()->create([
            'name' => 'Kayk',
            'phone' => '98981895794',
        ]);

        $this->withHeader('X-Bot-Token', 'secret-token')
            ->getJson('/api/bot/user?phone=5598981895794')
            ->assertOk()
            ->assertJson([
                'id' => $user->id,
                'phone' => '98981895794',
            ]);
    }

    public function test_bot_can_find_user_using_normalized_whatsapp_id(): void
    {
        BotSetting::query()->create([
            'key' => 'api_token',
            'value' => 'secret-token',
        ]);

        $user = User::factory()->create([
            'name' => 'Administrador',
            'phone' => '98981895794',
            'whatsapp_id' => '30417226834026',
        ]);

        $this->withHeader('X-Bot-Token', 'secret-token')
            ->getJson('/api/bot/user?phone=30417226834026&whatsapp_id=30417226834026%40lid')
            ->assertOk()
            ->assertJson([
                'id' => $user->id,
                'whatsapp_id' => '30417226834026',
            ]);
    }

    public function test_bot_api_rejects_invalid_token(): void
    {
        BotSetting::query()->create([
            'key' => 'api_token',
            'value' => 'secret-token',
        ]);

        $this->withHeader('X-Bot-Token', 'wrong')
            ->getJson('/api/bot/settings')
            ->assertUnauthorized();
    }
}
