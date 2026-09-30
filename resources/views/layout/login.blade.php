<html>
@include("layout.header")
<style>
    .login-brand-image { width: min(280px, 75%); max-height: 100px; object-fit: contain; object-position: left center; }
    .login-brand-wordmark { color: var(--brand-primary, #0866ff); font-size: clamp(1.75rem, 4vw, 2.5rem); font-weight: 800; letter-spacing: -.04em; line-height: 1.15; }
    @media (max-width: 767.98px) { .login-brand-image { width: min(240px, 85%); } }
</style>
<body class="bg-light d-flex flex-column min-vh-100" @if(!empty($siteSettings['login_background_path'])) style="background-image:linear-gradient(#ffffffbb,#ffffffbb),url('{{ asset('storage/'.$siteSettings['login_background_path']) }}');background-size:cover;background-position:center;background-attachment:fixed" @endif>
<main class="flex-grow-1">
    <div class="container-xxl  bg-light p-0 m-0 ">
        <div class="row my-5 py-5 g-0" >
            <div class="col-md-6 col-12 text-start p-4 mx-4" >
                <div class="row ">
                    <div class="col-md-9 mx-4" >
                        @if(!empty($siteSettings['logo_path']))<img class="login-brand-image" src="{{ asset('storage/'.$siteSettings['logo_path']) }}" alt="{{ config('app.name') }}">@else<div class="login-brand-wordmark" role="img" aria-label="{{ config('app.name') }}">{{ $siteSettings['site_name'] }}</div>@endif
                    </div>
                </div>
                <div class="text-dark h2">{{ app()->getLocale() === 'ar' ? $siteSettings['login_title'] : __('ui.login_title') }}</div>
                @if(!empty($siteSettings['login_subtitle']))<p class="text-muted h5 mt-3">{{ app()->getLocale() === 'ar' ? $siteSettings['login_subtitle'] : __('ui.login_subtitle') }}</p>@endif
            </div>
            <div class="col-md-4 col-12 text-start bg-white p-2  mx-5 rounded shadow" >
                @if(session('registration_closed'))
                    <div class="alert alert-info" role="status">{{ __('ui.registration_closed') }}</div>
                @endif
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="row p-2" >
                        <div class="col-md-12  " >
                            <input type="email" class="form-control shadow-sm py-3" placeholder="{{ __('ui.email') }}" name="email" autocomplete="email" required/>
                            @error('email')  
                        <strong>{{ $message }}</strong>   
                        @enderror
                        </div> 
                    </div>
                    <div class="row p-2" >
                        <div class="col-md-12 " >
                        <input type="password" autocomplete="current-password" class="form-control shadow-sm py-3" placeholder="{{ __('ui.password') }}" name="password" required>
                                @error('password')
                            <strong>{{ $message }}</strong>
                                @enderror
                        </div>
                    </div>
                    <div class="row p-2" >
                        <div class="col-md-12 " >
                            <button class="btn btn-primary form-control py-3" >{{ __('ui.sign_in') }}</button>
                    <div class="text-center py-2" >
                        <strong class="h3 ">
                            <a href="{{ route('password.request') }}">{{ __('ui.forgot_password') }}</a>
                        </strong>
                    </div>
                    <hr>
                    @if($siteSettings['registration_enabled'])<div class="row justify-content-center" >
                        <div class="col-md-8 p-2 d-flex justify-content-center text-center" >
                            <a href="{{ url('/reg') }}" class="btn btn-success form-control py-3" >{{ __('ui.create_account') }}</a>
                        </div>
                    </div>@endif
                    </div>
                </form>
            </div>
        </div>
    
    </div>
</main>
    
</body>
<footer>
<div class=" bg-white " >
        @include( 'layout.footer')
    </div>  
</footer>

</html>
