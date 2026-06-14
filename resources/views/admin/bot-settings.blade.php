@extends('layouts.app', ['title' => 'Configurar bot'])

@section('content')
<div class="topbar">
    <div>
        <h1>Bot</h1>
        <p class="muted">Configuracoes lidas pelos endpoints da API.</p>
    </div>
</div>

@if (session('status'))
    <div class="notice">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="errors">{{ $errors->first() }}</div>
@endif

<section class="panel">
    <form method="post" action="{{ route('admin.bot.update') }}" class="form">
        @csrf
        @method('put')
        <label>Token da API
            <input name="api_token" value="{{ old('api_token', $settings['api_token']) }}" autocomplete="off">
        </label>
        <label class="row">
            <input name="regenerate_token" value="1" type="checkbox" style="width:auto"> Gerar novo token ao salvar
        </label>
        <label>Nome do bot
            <input name="bot_name" value="{{ old('bot_name', $settings['bot_name']) }}" required>
        </label>
        <label>Mensagem inicial
            <textarea name="welcome_message" required>{{ old('welcome_message', $settings['welcome_message']) }}</textarea>
        </label>
        <label>Telefone de suporte
            <input name="support_phone" value="{{ old('support_phone', $settings['support_phone']) }}">
        </label>
        <button class="button" type="submit">Salvar configuracoes</button>
    </form>
</section>

<section class="panel" style="margin-top:16px">
    <h2>Endpoints do bot</h2>
    <table class="table">
        <tbody>
            <tr><th>GET</th><td>/api/bot/settings</td></tr>
            <tr><th>GET</th><td>/api/bot/user?phone=5585999999999</td></tr>
            <tr><th>POST</th><td>/api/bot/users/link</td></tr>
            <tr><th>POST</th><td>/api/bot/messages</td></tr>
            <tr><th>POST</th><td>/api/webhook/whatsapp</td></tr>
        </tbody>
    </table>
</section>
@endsection
