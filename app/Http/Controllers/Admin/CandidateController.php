<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCandidateRequest;
use App\Http\Requests\UpdateCandidateRequest;
use App\Models\Candidate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CandidateController extends Controller
{
    public function index(): View
    {
        $candidates = Candidate::orderBy('urutan')->get();

        return view('admin.candidates.index', compact('candidates'));
    }

    public function create(): View
    {
        return view('admin.candidates.create');
    }

    public function store(StoreCandidateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['foto'] = $request->file('foto')->store('kandidat', 'public');

        Candidate::create($data);

        return redirect()->route('admin.kandidat.index')->with('success', 'Kandidat ditambahkan.');
    }

    public function edit(Candidate $kandidat): View
    {
        return view('admin.candidates.edit', ['candidate' => $kandidat]);
    }

    public function update(UpdateCandidateRequest $request, Candidate $kandidat): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kandidat', 'public');
        } else {
            unset($data['foto']);
        }

        $kandidat->update($data);

        return redirect()->route('admin.kandidat.index')->with('success', 'Kandidat diperbarui.');
    }

    public function toggle(Candidate $kandidat): RedirectResponse
    {
        $kandidat->update(['is_active' => ! $kandidat->is_active]);

        return redirect()->route('admin.kandidat.index')->with('success', 'Status kandidat diubah.');
    }
}
