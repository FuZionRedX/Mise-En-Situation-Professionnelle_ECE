<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Item;
use App\Models\Mob;
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
            'identifier' => ['required', 'string', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,identifier,' . $user->id],
            'name'       => ['required', 'string', 'max:50'],
            'email'      => ['required', 'email', 'unique:users,email,' . $user->id],
            'role'       => ['required', 'in:user,admin'],
        ]);

        $this->reassignCreations($user->identifier, $request->input('identifier'));
        $user->update($request->only('identifier', 'name', 'email', 'role'));

        return redirect()->route('admin.users')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            abort(403, 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Détacher ses créations : un futur compte du même nom ne doit pas en hériter
        $this->reassignCreations($user->identifier, null);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Utilisateur supprimé.');
    }

    private function reassignCreations(string $from, ?string $to): void
    {
        foreach ([Block::class, Item::class, Mob::class] as $model) {
            $model::where('creator_identifier', $from)->update(['creator_identifier' => $to]);
        }
    }
}
