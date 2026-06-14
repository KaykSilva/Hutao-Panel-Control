<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Hutao Panel Control' }}</title>
    <style>
        :root { color-scheme: light; --ink:#1f2937; --muted:#6b7280; --line:#d9dee7; --bg:#f6f7f9; --panel:#ffffff; --accent:#0f766e; --accent-ink:#ffffff; --danger:#b91c1c; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; color: var(--ink); background: var(--bg); }
        a { color: inherit; text-decoration: none; }
        .shell { min-height: 100vh; display: grid; grid-template-columns: 240px minmax(0, 1fr); }
        .sidebar { background: #111827; color: #eef2f7; padding: 24px 18px; }
        .brand { font-size: 18px; font-weight: 800; margin-bottom: 28px; }
        .nav { display: grid; gap: 6px; }
        .nav a, .nav button { border: 0; border-radius: 6px; color: #d1d5db; background: transparent; padding: 10px 12px; font: inherit; text-align: left; cursor: pointer; width: 100%; }
        .nav a:hover, .nav button:hover, .nav .active { background: #1f2937; color: white; }
        main { padding: 28px; }
        .topbar { display: flex; justify-content: space-between; gap: 16px; align-items: center; margin-bottom: 22px; }
        h1 { margin: 0; font-size: 26px; line-height: 1.2; }
        h2 { margin: 0 0 14px; font-size: 18px; }
        .muted { color: var(--muted); }
        .grid { display: grid; gap: 16px; }
        .stats { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .panel, .card { background: var(--panel); border: 1px solid var(--line); border-radius: 8px; padding: 18px; }
        .stat-value { font-size: 30px; font-weight: 800; margin-top: 8px; }
        .form { display: grid; gap: 14px; max-width: 760px; }
        label { display: grid; gap: 7px; font-weight: 700; }
        input, textarea, select { width: 100%; border: 1px solid var(--line); border-radius: 6px; padding: 11px 12px; font: inherit; background: white; color: var(--ink); }
        textarea { min-height: 120px; resize: vertical; }
        .row { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
        .button { border: 0; border-radius: 6px; padding: 10px 14px; background: var(--accent); color: var(--accent-ink); font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; min-height: 42px; }
        .button.secondary { background: #e5e7eb; color: #111827; }
        .button.danger { background: var(--danger); color: white; }
        .table { width: 100%; border-collapse: collapse; background: white; border: 1px solid var(--line); border-radius: 8px; overflow: hidden; }
        .table th, .table td { padding: 12px 14px; border-bottom: 1px solid var(--line); text-align: left; vertical-align: middle; }
        .table th { font-size: 13px; color: var(--muted); background: #f9fafb; }
        .notice { border: 1px solid #99f6e4; background: #f0fdfa; color: #134e4a; border-radius: 6px; padding: 10px 12px; margin-bottom: 16px; }
        .errors { border: 1px solid #fecaca; background: #fef2f2; color: #7f1d1d; border-radius: 6px; padding: 10px 12px; margin-bottom: 16px; }
        .auth { min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #eef2f7; }
        .auth-card { width: min(440px, 100%); background: white; border: 1px solid var(--line); border-radius: 8px; padding: 26px; }
        @media (max-width: 800px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: static; }
            main { padding: 18px; }
            .stats { grid-template-columns: 1fr; }
            .topbar { align-items: flex-start; flex-direction: column; }
            .table { display: block; overflow-x: auto; }
        }
    </style>
</head>
<body>
@auth
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">Hutao Panel</div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>Dashboard</a>
                <a href="{{ route('profile.edit') }}" @class(['active' => request()->routeIs('profile.*')])>Perfil</a>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Admin</a>
                    <a href="{{ route('admin.bot.edit') }}" @class(['active' => request()->routeIs('admin.bot.*')])>Bot</a>
                    <a href="{{ route('admin.users.index') }}" @class(['active' => request()->routeIs('admin.users.*')])>Usuarios</a>
                @endif
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Sair</button>
                </form>
            </nav>
        </aside>
        <main>
            @yield('content')
        </main>
    </div>
@else
    <main class="auth">
        @yield('content')
    </main>
@endauth
</body>
</html>
