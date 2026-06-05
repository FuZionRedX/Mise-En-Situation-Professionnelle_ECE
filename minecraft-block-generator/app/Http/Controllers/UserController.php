<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.user-edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'role'  => ['required', 'in:user,admin'],
        ]);

        $user->update($request->only('name', 'email', 'role'));

        return redirect()->route('admin.users')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            abort(403, 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Utilisateur supprimé.');
    }
}
