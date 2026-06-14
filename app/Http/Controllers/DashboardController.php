<?php

namespace App\Http\Controllers;

use App\Models\BotMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $messages = BotMessage::query()
            ->whereHas('contact', fn ($query) => $query
                ->where('phone', $request->user()->phone)
                ->orWhere('whatsapp_id', $request->user()->whatsapp_id))
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', [
            'user' => $request->user(),
            'messages' => $messages,
        ]);
    }
}
