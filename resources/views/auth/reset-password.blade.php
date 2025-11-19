@extends('layouts.app')

@section('content')
<div class="card p-4">
    <h3>Reset Password</h3>

    {{-- Show success status (e.g., "Password reset link sent") --}}
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    {{-- Show validation errors (generic) --}}
    @if ($errors->any() && ! $errors->has('error'))
        <div class="alert alert-danger">
            @foreach($errors->all() as $e)
                <div>{{ $e }}</div>
            @endforeach
        </div>
    @endif

    {{-- Special handling for token / expired message --}}
    @if ($errors->has('error'))
        @php
            $errorMsg = $errors->first('error');
            $isExpired = stripos($errorMsg, 'expire') !== false || stripos($errorMsg, 'expired') !== false;
        @endphp

        @if ($isExpired)
            <div class="alert alert-warning">
                <strong>Link expired</strong><br>
                {{ $errorMsg }} <br><br>
                <a href="{{ route('password.request') }}" class="btn btn-primary">Request a new reset link</a>
                <small class="d-block mt-2 text-muted">You will be redirected automatically in <span id="countdown">8</span> seconds.</small>
            </div>

            <script>
                // Auto-redirect after 8 seconds (dev-friendly). Remove if you don't want auto-redirect.
                (function(){
                    var t = 8;
                    var el = document.getElementById('countdown');
                    var iv = setInterval(function(){
                        t--;
                        if (el) el.innerText = t;
                        if (t <= 0) {
                            clearInterval(iv);
                            window.location.href = "{{ route('password.request') }}";
                        }
                    }, 1000);
                })();
            </script>
        @else
            {{-- Show other non-expiry errors --}}
            <div class="alert alert-danger">
                {{ $errorMsg }}
            </div>
        @endif
    @endif

    {{-- Only show the reset form if there's no token-expired error --}}
    @if (! $errors->has('error') || (isset($isExpired) && ! $isExpired))
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" class="form-control" required readonly>
            </div>

            <div class="mb-3">
                <label>New password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Confirm password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button class="btn btn-primary">Reset Password</button>
        </form>
    @endif
</div>
@endsection
