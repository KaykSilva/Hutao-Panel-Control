<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp_id' => ['nullable', 'string', 'max:255', 'unique:users,whatsapp_id,'.$request->user()->id],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Perfil atualizado.');
    }
}
