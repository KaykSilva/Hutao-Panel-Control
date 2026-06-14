<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BotContact;
use App\Models\BotMessage;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'usersCount' => User::query()->count(),
            'contactsCount' => BotContact::query()->count(),
            'messagesCount' => BotMessage::query()->count(),
            'latestMessages' => BotMessage::query()->with('contact')->latest()->limit(8)->get(),
        ]);
    }
}
