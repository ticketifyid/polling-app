@extends('layouts.auth')

@section('title', 'Login Panitia')

@section('content')
<div class="adm-auth-mark"><i class="bi bi-shield-lock"></i></div>

<h1 class="adm-auth-title">Login panitia</h1>
<p class="adm-auth-sub">Masukkan password bersama untuk membuka panel.</p>

{{-- AuthController@login mengembalikan back()->with('error', 'Password salah.') --}}
@if(session('error'))
    <div class="alert alert-danger mb-3" role="alert">
        <i class="bi bi-exclamation-circle-fill"></i>
        <div>{{ session('error') }}</div>
    </div>
@endif

<form method="POST" action="{{ route('admin.login') }}">
    @csrf

    <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-key"></i></span>
            <input type="password" class="form-control @error('password') is-invalid @enderror"
                   id="password" name="password" required autofocus autocomplete="current-password">
        </div>
        @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100" style="min-height:48px;">
        <i class="bi bi-box-arrow-in-right"></i> Masuk
    </button>
</form>
@endsection
