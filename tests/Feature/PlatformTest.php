<?php

namespace Tests\Feature;

use App\Models\BotSetting;
use App\Models\SharedAccountParticipant;
use App\Models\User;
use App\Models\WhatsappGroup;
use App\Models\WhatsappGroupMember;
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

    public function test_bot_can_sync_whatsapp_group_members(): void
    {
        BotSetting::query()->create([
            'key' => 'api_token',
            'value' => 'secret-token',
        ]);

        $user = User::factory()->create([
            'phone' => '98981895794',
            'whatsapp_id' => '30417226834026',
        ]);

        $this->withHeader('X-Bot-Token', 'secret-token')
            ->postJson('/api/bot/groups/sync', [
                'whatsapp_id' => '120363000000000000@g.us',
                'name' => 'Casa',
                'members' => [
                    [
                        'name' => 'Kayk',
                        'phone' => '5598981895794',
                        'whatsapp_id' => '30417226834026@lid',
                    ],
                ],
            ])
            ->assertOk()
            ->assertJson(['synced' => true, 'members_count' => 1]);

        $this->assertDatabaseHas('whatsapp_groups', [
            'whatsapp_id' => '120363000000000000',
            'name' => 'Casa',
        ]);

        $this->assertDatabaseHas('whatsapp_group_members', [
            'user_id' => $user->id,
            'phone' => '5598981895794',
            'whatsapp_id' => '30417226834026',
        ]);
    }

    public function test_user_can_create_shared_account_and_mark_payment_paid(): void
    {
        $creator = User::factory()->create();
        $group = WhatsappGroup::query()->create([
            'name' => 'Casa',
            'whatsapp_id' => 'grupo-casa',
        ]);
        $memberA = WhatsappGroupMember::query()->create([
            'whatsapp_group_id' => $group->id,
            'name' => 'Pessoa A',
            'phone' => '111',
            'whatsapp_id' => 'a',
        ]);
        $memberB = WhatsappGroupMember::query()->create([
            'whatsapp_group_id' => $group->id,
            'name' => 'Pessoa B',
            'phone' => '222',
            'whatsapp_id' => 'b',
        ]);

        $this->actingAs($creator)
            ->post('/accounts', [
                'whatsapp_group_id' => $group->id,
                'title' => 'Netflix',
                'total_amount' => '100,00',
                'participant_ids' => [$memberA->id, $memberB->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('shared_accounts', [
            'title' => 'Netflix',
            'total_amount_cents' => 10000,
            'share_amount_cents' => 5000,
        ]);

        $participant = SharedAccountParticipant::query()->firstOrFail();

        $this->actingAs($creator)
            ->put("/account-participants/{$participant->id}/paid")
            ->assertRedirect();

        $this->assertNotNull($participant->fresh()->paid_at);
        $this->assertSame(5000, $participant->fresh()->paid_amount_cents);
    }

    public function test_bot_can_list_debts_grouped_by_whatsapp_group(): void
    {
        BotSetting::query()->create([
            'key' => 'api_token',
            'value' => 'secret-token',
        ]);

        $creator = User::factory()->create();
        $group = WhatsappGroup::query()->create([
            'name' => 'Casa',
            'whatsapp_id' => 'grupo-casa',
        ]);
        $member = WhatsappGroupMember::query()->create([
            'whatsapp_group_id' => $group->id,
            'name' => 'Pessoa A',
            'phone' => '98981895794',
            'whatsapp_id' => '30417226834026',
        ]);

        $this->actingAs($creator)->post('/accounts', [
            'whatsapp_group_id' => $group->id,
            'title' => 'Internet',
            'total_amount' => '80.00',
            'participant_ids' => [$member->id],
        ]);

        $this->withHeader('X-Bot-Token', 'secret-token')
            ->getJson('/api/bot/debts?whatsapp_id=30417226834026@lid')
            ->assertOk()
            ->assertJsonPath('debts.0.group', 'Casa')
            ->assertJsonPath('debts.0.items.0.title', 'Internet')
            ->assertJsonPath('debts.0.items.0.amount', '80.00');
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
