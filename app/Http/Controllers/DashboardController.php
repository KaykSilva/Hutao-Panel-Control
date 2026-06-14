<?php

namespace App\Http\Controllers;

use App\Models\BotMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $messages = BotMessage::query()
            ->whereHas('contact', fn ($query) => $query
                ->where('phone', $request->user()->phone)
                ->orWhere('whatsapp_id', $request->user()->whatsapp_id))
            ->latest()
            ->limit(10)
            ->get();

        return Inertia::render('Dashboard', [
            'user' => $request->user(),
            'messages' => $messages,
        ]);
    }
}
