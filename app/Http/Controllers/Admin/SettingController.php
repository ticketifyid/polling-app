<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.setting', [
            'nama_acara'     => Setting::get('nama_acara', ''),
            'polling_dibuka' => filter_var(Setting::get('polling_dibuka'), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_acara' => ['required', 'string', 'max:255'],
        ]);

        Setting::set('nama_acara', $validated['nama_acara']);
        Setting::set('polling_dibuka', $request->boolean('polling_dibuka') ? '1' : '0');

        return redirect()->route('admin.setting.edit')->with('success', 'Pengaturan disimpan.');
    }
}
