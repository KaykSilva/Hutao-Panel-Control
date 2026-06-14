<?php

namespace App\Http\Controllers;

use App\Models\SharedAccount;
use App\Models\SharedAccountParticipant;
use App\Models\User;
use App\Models\WhatsappGroup;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SharedAccountController extends Controller
{
    public function index(Request $request): Response
    {
        $groups = WhatsappGroup::query()
            ->with(['members' => fn ($query) => $query->orderBy('name')->orderBy('phone')])
            ->orderBy('name')
            ->get();

        $accounts = SharedAccount::query()
            ->with(['group', 'responsible', 'participants.member'])
            ->latest()
            ->get()
            ->map(fn (SharedAccount $account) => [
                'id' => $account->id,
                'title' => $account->title,
                'description' => $account->description,
                'total_amount' => Money::toDecimal($account->total_amount_cents),
                'share_amount' => Money::toDecimal($account->share_amount_cents),
                'due_date' => $account->due_date?->toDateString(),
                'status' => $account->status,
                'group' => $account->group,
                'responsible' => $account->responsible,
                'participants' => $account->participants->map(fn (SharedAccountParticipant $participant) => [
                    'id' => $participant->id,
                    'amount' => Money::toDecimal($participant->amount_cents),
                    'paid_amount' => Money::toDecimal($participant->paid_amount_cents),
                    'paid_at' => $participant->paid_at?->toDateTimeString(),
                    'member' => $participant->member,
                ]),
            ]);

        return Inertia::render('Accounts/Index', [
            'accounts' => $accounts,
            'groups' => $groups,
            'users' => User::query()->orderBy('name')->get(['id', 'name', 'email', 'phone', 'whatsapp_id']),
            'status' => session('status'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'whatsapp_group_id' => ['required', 'exists:whatsapp_groups,id'],
            'responsible_user_id' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'total_amount' => ['required', 'regex:/^\d+([,.]\d{1,2})?$/'],
            'due_date' => ['nullable', 'date'],
            'participant_ids' => ['required', 'array', 'min:1'],
            'participant_ids.*' => [
                'integer',
                Rule::exists('whatsapp_group_members', 'id')
                    ->where('whatsapp_group_id', $request->integer('whatsapp_group_id')),
            ],
        ]);

        $totalCents = Money::toCents($data['total_amount']);

        if ($totalCents < 1) {
            throw ValidationException::withMessages([
                'total_amount' => 'Informe um valor maior que zero.',
            ]);
        }

        DB::transaction(function () use ($data, $request, $totalCents): void {
            $participantCount = count($data['participant_ids']);
            $shareCents = intdiv($totalCents, $participantCount);
            $remainder = $totalCents % $participantCount;

            $account = SharedAccount::query()->create([
                'whatsapp_group_id' => $data['whatsapp_group_id'],
                'created_by_user_id' => $request->user()->id,
                'responsible_user_id' => $data['responsible_user_id'] ?? $request->user()->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'total_amount_cents' => $totalCents,
                'share_amount_cents' => $shareCents,
                'due_date' => $data['due_date'] ?? null,
                'status' => 'open',
            ]);

            foreach (array_values($data['participant_ids']) as $index => $memberId) {
                SharedAccountParticipant::query()->create([
                    'shared_account_id' => $account->id,
                    'whatsapp_group_member_id' => $memberId,
                    'amount_cents' => $shareCents + ($index < $remainder ? 1 : 0),
                ]);
            }
        });

        return back()->with('status', 'Conta criada e dividida entre os participantes.');
    }

    public function markPaid(SharedAccountParticipant $participant): RedirectResponse
    {
        $participant->update([
            'paid_amount_cents' => $participant->amount_cents,
            'paid_at' => now(),
        ]);

        $account = $participant->account()->with('participants')->firstOrFail();

        if ($account->participants->every(fn (SharedAccountParticipant $item) => $item->paid_at !== null)) {
            $account->update(['status' => 'paid']);
        }

        return back()->with('status', 'Pagamento baixado.');
    }

    public function markOpen(SharedAccountParticipant $participant): RedirectResponse
    {
        $participant->update([
            'paid_amount_cents' => 0,
            'paid_at' => null,
        ]);

        $participant->account()->update(['status' => 'open']);

        return back()->with('status', 'Baixa removida.');
    }
}
