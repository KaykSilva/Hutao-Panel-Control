<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BotContact;
use App\Models\BotMessage;
use App\Models\BotSetting;
use App\Models\SharedAccountParticipant;
use App\Models\User;
use App\Models\WhatsappGroup;
use App\Models\WhatsappGroupMember;
use App\Support\Money;
use App\Support\PhoneNumber;
use App\Support\WhatsappId;
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

        $phoneVariants = PhoneNumber::variants($data['phone'] ?? null);
        $whatsappIdVariants = WhatsappId::variants($data['whatsapp_id'] ?? null);

        $user = User::query()
            ->where(function ($query) use ($phoneVariants, $whatsappIdVariants): void {
                $query
                    ->when($phoneVariants !== [], fn ($query) => $query->orWhereIn('phone', $phoneVariants))
                    ->when($whatsappIdVariants !== [], fn ($query) => $query->orWhereIn('whatsapp_id', $whatsappIdVariants));
            })
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

    public function identity(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['nullable', 'string'],
            'whatsapp_id' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
        ]);

        $phone = PhoneNumber::digits($data['phone'] ?? null);
        $whatsappId = WhatsappId::normalize($data['whatsapp_id'] ?? null);
        $phoneVariants = PhoneNumber::variants($phone);
        $whatsappIdVariants = WhatsappId::variants($whatsappId);

        $user = User::query()
            ->where(function ($query) use ($phoneVariants, $whatsappIdVariants): void {
                $query
                    ->when($phoneVariants !== [], fn ($query) => $query->orWhereIn('phone', $phoneVariants))
                    ->when($whatsappIdVariants !== [], fn ($query) => $query->orWhereIn('whatsapp_id', $whatsappIdVariants));
            })
            ->first();

        return response()->json([
            'registered' => $user !== null,
            'required_fields' => [
                'phone' => $phone,
                'whatsapp_id' => $whatsappId,
            ],
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'whatsapp_id' => $user->whatsapp_id,
            ] : null,
        ]);
    }

    public function syncGroup(Request $request): JsonResponse
    {
        $data = $request->validate([
            'whatsapp_id' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'members' => ['required', 'array'],
            'members.*.name' => ['nullable', 'string', 'max:255'],
            'members.*.phone' => ['nullable', 'string', 'max:30'],
            'members.*.whatsapp_id' => ['required', 'string', 'max:255'],
        ]);

        $group = WhatsappGroup::query()->updateOrCreate(
            ['whatsapp_id' => WhatsappId::normalize($data['whatsapp_id'])],
            ['name' => $data['name']],
        );

        foreach ($data['members'] as $member) {
            $phone = PhoneNumber::digits($member['phone'] ?? null) ?: null;
            $whatsappId = WhatsappId::normalize($member['whatsapp_id']);
            $user = $this->findUser($phone, $whatsappId);

            WhatsappGroupMember::query()->updateOrCreate(
                [
                    'whatsapp_group_id' => $group->id,
                    'whatsapp_id' => $whatsappId,
                ],
                [
                    'user_id' => $user?->id,
                    'name' => $member['name'] ?? null,
                    'phone' => $phone,
                    'last_seen_at' => now(),
                ],
            );
        }

        return response()->json([
            'synced' => true,
            'group_id' => $group->id,
            'members_count' => count($data['members']),
        ]);
    }

    public function debts(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['nullable', 'string'],
            'whatsapp_id' => ['nullable', 'string'],
        ]);

        $phoneVariants = PhoneNumber::variants($data['phone'] ?? null);
        $whatsappIdVariants = WhatsappId::variants($data['whatsapp_id'] ?? null);

        $participants = SharedAccountParticipant::query()
            ->with(['account.group', 'account.responsible', 'member'])
            ->whereNull('paid_at')
            ->whereHas('member', function ($query) use ($phoneVariants, $whatsappIdVariants): void {
                $query
                    ->when($phoneVariants !== [], fn ($query) => $query->orWhereIn('phone', $phoneVariants))
                    ->when($whatsappIdVariants !== [], fn ($query) => $query->orWhereIn('whatsapp_id', $whatsappIdVariants));
            })
            ->get()
            ->groupBy(fn (SharedAccountParticipant $participant) => $participant->account->group->name)
            ->map(fn ($items, $groupName) => [
                'group' => $groupName,
                'items' => $items->map(fn (SharedAccountParticipant $participant) => [
                    'account_id' => $participant->account->id,
                    'title' => $participant->account->title,
                    'amount_cents' => $participant->amount_cents,
                    'amount' => Money::toDecimal($participant->amount_cents),
                    'responsible' => $participant->account->responsible?->name,
                    'due_date' => $participant->account->due_date?->toDateString(),
                ])->values(),
            ])
            ->values();

        return response()->json([
            'debts' => $participants,
        ]);
    }

    public function linkUser(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required_without:phone', 'nullable', 'email'],
            'phone' => ['required_without:email', 'nullable', 'string', 'max:30'],
            'whatsapp_id' => ['required', 'string', 'max:255'],
        ]);

        $phoneVariants = PhoneNumber::variants($data['phone'] ?? null);

        $user = User::query()
            ->where(function ($query) use ($data, $phoneVariants): void {
                $query
                    ->when($data['email'] ?? null, fn ($query, $email) => $query->orWhere('email', $email))
                    ->when($phoneVariants !== [], fn ($query) => $query->orWhereIn('phone', $phoneVariants));
            })
            ->firstOrFail();

        $user->update([
            'phone' => $data['phone'] ?? $user->phone,
            'whatsapp_id' => WhatsappId::normalize($data['whatsapp_id']),
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

        $data['whatsapp_id'] = WhatsappId::normalize($data['whatsapp_id'] ?? null) ?: null;

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

    private function findUser(?string $phone, ?string $whatsappId): ?User
    {
        $phoneVariants = PhoneNumber::variants($phone);
        $whatsappIdVariants = WhatsappId::variants($whatsappId);

        return User::query()
            ->where(function ($query) use ($phoneVariants, $whatsappIdVariants): void {
                $query
                    ->when($phoneVariants !== [], fn ($query) => $query->orWhereIn('phone', $phoneVariants))
                    ->when($whatsappIdVariants !== [], fn ($query) => $query->orWhereIn('whatsapp_id', $whatsappIdVariants));
            })
            ->first();
    }
}
