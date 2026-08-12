@extends('layouts.app')

@section('title', 'Terima Kasih')

@section('content')
<div class="voter-screen">
    <div class="voter-col">
        <div class="floating-panel text-center">
            <span class="success-mark" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
            <h1 class="page-title">Suara Anda tercatat</h1>
            <p class="help-text">Terima kasih sudah berpartisipasi.</p>
            <a href="{{ route('vote.index') }}" class="btn btn-primary btn-action">
                <i class="bi bi-person-plus"></i>
                Voter berikutnya
            </a>
        </div>
    </div>
</div>
@endsection

@push('head')
<style>
    .success-mark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: linear-gradient(180deg, #12B76A 0%, #039855 100%);
        color: #fff;
        font-size: 34px;
        line-height: 1;
        margin-bottom: 24px;
        /* Lingkaran halo, bukan animasi berulang — sekali muncul lalu diam. */
        box-shadow: 0 0 0 8px rgba(18, 183, 106, 0.12), 0 8px 20px rgba(3, 152, 85, 0.32);
        animation: mark-in .35s ease-out;
    }
    @keyframes mark-in {
        from { transform: scale(0.7); opacity: 0; }
        to   { transform: scale(1);   opacity: 1; }
    }
    .success-mark + .page-title { margin-bottom: 8px; }
    .help-text + .btn { margin-top: var(--space); }
</style>
@endpush
