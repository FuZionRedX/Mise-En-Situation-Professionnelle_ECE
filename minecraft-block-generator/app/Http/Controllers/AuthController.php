<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function choice()
    {
        return view('auth.choice');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to login either by identifier or by email
        $credentials = ['password' => $data['password']];

        // Try identifier first
        $user = User::where('identifier', $data['identifier'])->first();
        if (!$user) {
            $user = User::where('email', $data['identifier'])->first();
        }

        if ($user && Hash::check($data['password'], $user->password)) {
            Auth::login($user);
            return redirect()->intended(route('block.new'));
        }

        return back()->withErrors(['identifier' => 'Identifiant ou mot de passe incorrect'])->withInput();
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'identifier' => 'required|string|alpha_num|min:3|max:20',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'identifier.required' => 'L\'identifiant est requis',
            'identifier.alpha_num' => 'L\'identifiant doit contenir uniquement des lettres et chiffres',
            'identifier.min' => 'L\'identifiant doit avoir au minimum 3 caractères',
            'identifier.max' => 'L\'identifiant doit avoir au maximum 20 caractères',
        ]);

        // Vérifier que l'identifiant n'existe pas
        if (User::where('identifier', $data['identifier'])->exists()) {
            return back()->withErrors(['identifier' => 'Cet identifiant est déjà pris'])->withInput();
        }

        $user = User::create([
            'name' => $data['identifier'],
            'identifier' => $data['identifier'],
            'email' => $data['identifier'] . '@local',
            'password' => $data['password'],
        ]);

        Auth::login($user);

        // Après inscription, montrer l'identifiant
        return view('auth.registered', ['identifier' => $data['identifier']]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('block.index');
    }
}
