<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));

        $votes = Vote::with('candidate')
            ->when($q !== '', fn ($query) => $query->where('nama', 'like', "%{$q}%"))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.log', compact('votes', 'q'));
    }
}
