<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Setting;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(): View
    {
        $candidates = Candidate::withCount('votes')
            ->orderByDesc('votes_count')
            ->get();

        return view('result.index', [
            'candidates' => $candidates,
            'nama_acara' => Setting::get('nama_acara', 'Hasil Pemilihan'),
            'total'      => $candidates->sum('votes_count'),
        ]);
    }
}
