<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BotContact;
use App\Models\BotMessage;
use App\Models\BotSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BotController extends Controller
{
    public function settings(): JsonResponse
    {
        return response()->json([
            'bot_name' => BotSetting::valueFor('bot_name', 'Hutao Bot'),
            'welcome_message' => BotSetting::valueFor('welcome_message', 'Ola! Como posso ajudar?'),
            'support_phone' => BotSetting::valueFor('support_phone'),
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required_without:whatsapp_id', 'nullable', 'string'],
            'whatsapp_id' => ['required_without:phone', 'nullable', 'string'],
        ]);

        $user = User::query()
            ->when($data['phone'] ?? null, fn ($query, $phone) => $query->orWhere('phone', $phone))
            ->when($data['whatsapp_id'] ?? null, fn ($query, $id) => $query->orWhere('whatsapp_id', $id))
            ->first();

        abort_if(! $user, 404, 'User not found.');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'whatsapp_id' => $user->whatsapp_id,
            'role' => $user->role,
        ]);
    }

    public function linkUser(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required_without:phone', 'nullable', 'email'],
            'phone' => ['required_without:email', 'nullable', 'string', 'max:30'],
            'whatsapp_id' => ['required', 'string', 'max:255'],
        ]);

        $user = User::query()
            ->when($data['email'] ?? null, fn ($query, $email) => $query->orWhere('email', $email))
            ->when($data['phone'] ?? null, fn ($query, $phone) => $query->orWhere('phone', $phone))
            ->firstOrFail();

        $user->update([
            'phone' => $data['phone'] ?? $user->phone,
            'whatsapp_id' => $data['whatsapp_id'],
            'whatsapp_linked_at' => now(),
        ]);

        return response()->json(['linked' => true, 'user_id' => $user->id]);
    }

    public function storeMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp_id' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'direction' => ['nullable', 'in:inbound,outbound'],
            'message_type' => ['nullable', 'string', 'max:50'],
            'content' => ['nullable', 'string'],
            'external_id' => ['nullable', 'string', 'max:255'],
            'payload' => ['nullable', 'array'],
        ]);

        $contact = BotContact::query()->updateOrCreate(
            ['phone' => $data['phone']],
            [
                'name' => $data['name'] ?? null,
                'whatsapp_id' => $data['whatsapp_id'] ?? null,
                'last_seen_at' => now(),
            ],
        );

        $message = BotMessage::query()->create([
            'bot_contact_id' => $contact->id,
            'direction' => $data['direction'] ?? 'inbound',
            'message_type' => $data['message_type'] ?? 'text',
            'content' => $data['content'] ?? null,
            'payload' => $data['payload'] ?? null,
            'external_id' => $data['external_id'] ?? null,
        ]);

        return response()->json(['stored' => true, 'message_id' => $message->id], 201);
    }
}
