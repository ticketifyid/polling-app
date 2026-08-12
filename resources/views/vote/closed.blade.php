@extends('layouts.app')

@section('title', 'Voting Ditutup')

@section('content')
<div class="voter-screen">
    <div class="voter-col">
        <div class="floating-panel text-center">
            <span class="closed-mark" aria-hidden="true"><i class="bi bi-lock"></i></span>
            {{-- String judul ini dikunci oleh VoteFlowTest::test_closed_message_when_polling_closed --}}
            <h1 class="page-title mb-2">Voting Telah Ditutup</h1>
            <p class="help-text">Terima kasih atas perhatian Anda.</p>
        </div>
    </div>
</div>
@endsection

@push('head')
<style>
    .closed-mark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #F2F4F7;
        color: var(--muted);
        font-size: 28px;
        line-height: 1;
        margin-bottom: 20px;
    }
</style>
@endpush
