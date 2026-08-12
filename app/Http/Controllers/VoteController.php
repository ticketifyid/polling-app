<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVoteRequest;
use App\Models\Candidate;
use App\Models\Setting;
use App\Models\Vote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class VoteController extends Controller
{
    public function index(): Response
    {
        if (! filter_var(Setting::get('polling_dibuka'), FILTER_VALIDATE_BOOLEAN)) {
            return response()->view('vote.closed')
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        }

        $candidates = Candidate::where('is_active', true)->orderBy('urutan')->get();

        return response()->view('vote.index', compact('candidates'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function store(StoreVoteRequest $request): RedirectResponse
    {
        if (! filter_var(Setting::get('polling_dibuka'), FILTER_VALIDATE_BOOLEAN)) {
            return redirect()->route('vote.index')->with('error', 'Voting telah ditutup.');
        }

        Vote::create($request->validated());

        return redirect()->route('vote.success');
    }

    public function success(): Response
    {
        return response()->view('vote.success')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
