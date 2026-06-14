<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BotSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BotSettingsController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Admin/BotSettings', [
            'settings' => [
                'api_token' => BotSetting::valueFor('api_token'),
                'bot_name' => BotSetting::valueFor('bot_name', 'Hutao Bot'),
                'welcome_message' => BotSetting::valueFor('welcome_message', 'Ola! Como posso ajudar?'),
                'support_phone' => BotSetting::valueFor('support_phone'),
            ],
            'status' => session('status'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'api_token' => ['nullable', 'string', 'max:255'],
            'bot_name' => ['required', 'string', 'max:120'],
            'welcome_message' => ['required', 'string', 'max:1000'],
            'support_phone' => ['nullable', 'string', 'max:30'],
            'regenerate_token' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('regenerate_token') || blank($data['api_token'])) {
            $data['api_token'] = Str::random(48);
        }

        foreach (['api_token', 'bot_name', 'welcome_message', 'support_phone'] as $key) {
            BotSetting::query()->updateOrCreate(['key' => $key], ['value' => $data[$key] ?? null]);
        }

        return back()->with('status', 'Configuracoes do bot salvas.');
    }
}
