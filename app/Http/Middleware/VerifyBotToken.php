<?php

namespace App\Http\Middleware;

use App\Models\BotSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyBotToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = config('bot.api_token') ?: BotSetting::valueFor('api_token');
        $requestToken = $request->bearerToken() ?: $request->header('X-Bot-Token');

        abort_if(blank($configuredToken) || ! hash_equals($configuredToken, (string) $requestToken), 401, 'Invalid bot token.');

        return $next($request);
    }
}
