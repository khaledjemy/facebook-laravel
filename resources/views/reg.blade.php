<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" xmlns="http://www.w3.org/1999/xhtml">
@include('layout.header')
<body @if(!empty($siteSettings['login_background_path'])) style="background-image:linear-gradient(#ffffffbb,#ffffffbb),url('{{ asset('storage/'.$siteSettings['login_background_path']) }}');background-size:cover;background-position:center;background-attachment:fixed" @endif>
	<div class="container" >
@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

		<div class="card bg-transparent border-0">
			<div class="card-title " >
				<p>
					<h3>{{ app()->getLocale() === 'ar' ? $siteSettings['login_title'] : __('ui.login_title') }}</h3>
					<span>{{ app()->getLocale() === 'ar' ? $siteSettings['login_subtitle'] : __('ui.login_subtitle') }}</span>
				</p>
			</div>
			<div class="card-body" >
				<div class="row" >
				<div class="col-md-8 bg-white rounded shadow" >
					<form action="/regi" method="POST">
					@csrf
				<div class="row" >
					<div class="col-md-12" >
						@if(!empty($siteSettings['logo_path']))<img class="w-75 justify-content-left" src="{{ asset('storage/'.$siteSettings['logo_path']) }}" alt="{{ $siteSettings['site_name'] }}">@else<div class="h2 text-primary font-weight-bold">{{ $siteSettings['site_name'] }}</div>@endif

					</div>
				</div>
				<div class="row" >
					<h1>{{ __('ui.create_new_account') }}</h1>
					<p>{{ __('ui.join_description') }}</p>
				</div>
				<div class="row my-2">
					<div class="col-md-6" >
						<input type="text" class="form-control form-control-lg" id="fname" value="{{ old('firstname') }}" name="firstname" placeholder="{{ __('ui.first_name') }}" autocomplete="given-name" required>
					</div>
					<div class="col-md-6" >
						<input type="text" class="form-control form-control-lg" id="lname" value="{{ old('lastname') }}" name="lastname" placeholder="{{ __('ui.last_name') }}" autocomplete="family-name" required>
					</div>
				</div>
				<div class="row my-2" >
					<div class="col-md-12">
						<input id="emailsignup" class="form-control form-control-lg" value="{{ old('emailsignup') }}" name="emailsignup" required="required" type="email" autocomplete="email" placeholder="{{ __('ui.email') }}"/>
					</div>
				</div>
				<div class="row my-2">
					<div class="col-md-12" >
						<input id="passwordsignup" class="form-control form-control-lg" name="passwordsignup" required="required" minlength="8" autocomplete="new-password" type="password" placeholder="{{ __('ui.password') }}"/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12" >
						<input id="passwordsignup_confirm" class="form-control form-control-lg" name="passwordsignup_confirm" required="required" minlength="8" autocomplete="new-password" type="password" placeholder="{{ __('ui.confirm_password') }}"/>
					</div>
				</div>
				<div class="row my-2" >
					<span>
						{{ __('ui.use_your_email') }}
					</span>
				</div>
				<div class="row my-2" >
					<div class="col-md-12">
						<input class="btn btn-success w-100" type="submit" value="{{ __('ui.create_account') }}" name="regi"/>
					</div>
				</div>
				<div class="text-center mb-3">
					<a href="{{ route('login') }}">{{ __('ui.already_have_account') }}</a>
				</div>
	</form>
			</div>
		</div>
			</div>
		</div>


 	
	</div>
 	
</body>


</html>
