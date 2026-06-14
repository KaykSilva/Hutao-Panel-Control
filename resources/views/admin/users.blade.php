@extends('layouts.app', ['title' => 'Usuarios'])

@section('content')
<div class="topbar">
    <div>
        <h1>Usuarios</h1>
        <p class="muted">Contas da plataforma.</p>
    </div>
</div>

@if (session('status'))
    <div class="notice">{{ session('status') }}</div>
@endif

<table class="table">
    <thead>
        <tr><th>Nome</th><th>Email</th><th>Telefone</th><th>WhatsApp</th><th>Papel</th><th></th></tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->phone }}</td>
            <td>{{ $user->whatsapp_id }}</td>
            <td>
                <form method="post" action="{{ route('admin.users.role', $user) }}" class="row">
                    @csrf
                    @method('put')
                    <select name="role">
                        <option value="user" @selected($user->role === 'user')>user</option>
                        <option value="admin" @selected($user->role === 'admin')>admin</option>
                    </select>
                    <button class="button secondary" type="submit">Salvar</button>
                </form>
            </td>
            <td class="muted">{{ $user->created_at->format('d/m/Y') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div style="margin-top:16px">{{ $users->links() }}</div>
@endsection
