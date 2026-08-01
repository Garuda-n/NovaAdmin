<x-guest-layout>
    {{-- Card Header --}}
    <div class="login-card-header">
        <h1>Welcome back</h1>
        <p>Enter your credentials to access your account</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="login-session-status">
            {{ session('status') }}
        </div>
    @endif

    {{-- Toast --}}
    <x-toast />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="login-field">
            <label for="email">{{ __('Email Address') }}</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="you@company.com"
            />
        </div>

        {{-- Password --}}
        <div class="login-field">
            <label for="password">{{ __('Password') }}</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
            />
        </div>

        {{-- Remember / Forgot --}}
        <div class="login-meta-row">
            <label for="remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="login-forgot-link" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="login-submit-btn">
            {{ __('Sign In') }}
        </button>
    </form>

    {{-- Footer --}}
    @if (Route::has('register'))
        <div class="login-footer">
            Don't have an account? <a href="{{ route('register') }}">Create one</a>
        </div>
    @endif
</x-guest-layout>