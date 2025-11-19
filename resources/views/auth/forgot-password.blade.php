@extends('layouts.app')

@section('content')
<div class="card p-4">
    <h3>Forgot Password</h3>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-primary">Send Reset Link</button>
    </form>
</div>
@endsection
