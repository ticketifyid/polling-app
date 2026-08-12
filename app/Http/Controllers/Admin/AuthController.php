<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (session('admin')) {
            return redirect()->route('admin.kandidat.index');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Hash::check($request->input('password'), Setting::get('admin_password', ''))) {
            return back()->with('error', 'Password salah.');
        }

        $request->session()->put('admin', true);
        $request->session()->regenerate();

        return redirect()->route('admin.kandidat.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin');
        $request->session()->regenerate();

        return redirect()->route('admin.login');
    }
}
