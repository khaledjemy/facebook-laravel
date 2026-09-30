<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
@include('layout.header')
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <main class="card border-0 shadow-sm p-4" style="width:min(480px,92vw)">
        <h1 class="h3 mb-3">{{ __('ui.verify_email_title') }}</h1>
        <p class="text-muted">{{ __('ui.verify_email_instructions') }}</p>
        <p class="mb-3"><strong>{{ auth()->user()->getEmailForVerification() }}</strong></p>

        @if(session('resent'))
            <div class="alert alert-success" role="status">{{ __('ui.verify_email_sent') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn-primary w-100" type="submit">{{ __('ui.verify_email_resend') }}</button>
        </form>
        <form method="POST" action="{{ route('logout') }}" class="mt-3 text-center">
            @csrf
            <button class="btn btn-link" type="submit">{{ __('ui.sign_out') }}</button>
        </form>
    </main>
</body>
</html>
