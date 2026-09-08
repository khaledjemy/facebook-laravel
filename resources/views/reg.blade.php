<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
@include('layout.header')
<body>
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
					<h3>Facebook helps you connect and share with the people in your life.</h3>
					<span>Get started on Facebook
							Create an account to connect with friends, family and communities of people who share your interests.</span>
				</p>
			</div>
			<div class="card-body" >
				<div class="row" >
				<div class="col-md-8 bg-white rounded shadow" >
					<form action="/regi" method="POST">
					@csrf
				<div class="row" >
					<div class="col-md-12" >
        				<img  class="w-75 justify-content-left" src="{{asset('/img/4lCu2zih0ca.svg')  }}" alt="">

					</div>
				</div>
				<div class="row" >
					<h1>Create  an account </h1>
					<p>It's free and always will be.</p>
				</div>
				<div class="row my-2">
					<div class="col-md-6" >
						<input type="text" class="form-control form-control-lg" id="fname" value="{{ old('firstname') }}" name="firstname" placeholder="Your name..">
					</div>
					<div class="col-md-6" >
						<input type="text" class="form-control form-control-lg" id="lname" value="{{ old('lastname') }}" name="lastname" placeholder="Your last name..">
					</div>
				</div>
				<div class="row my-2" >
					<div class="col-md-12">
						<input id="emailsignup" class="form-control form-control-lg" value="{{ old('emailsignup') }}" name="emailsignup" required="required" type="email" placeholder="E-mail@ adress"/> 
					</div>
				</div>
				<div class="row my-2">
					<div class="col-md-12" >
						<input id="passwordsignup" class="form-control form-control-lg" name="passwordsignup" required="required" minlength="8" autocomplete="new-password" type="password" placeholder="Enter password"/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12" >
						<input id="passwordsignup_confirm" class="form-control form-control-lg" name="passwordsignup_confirm" required="required" minlength="8" autocomplete="new-password" type="password" placeholder="Confirm password"/>					
					</div>
				</div>
				<div class="row my-2" >
					<span>
						By clicking Create an account, you agree to our Terms and confirm that you have read our Data Policy, including our Cookie Use Policy. You may receive SMS message notifications from Facebook and can opt out at any time.
					</span>
				</div>
				<div class="row my-2" >
					<div class="col-md-12">
		            	<input class="btn btn-success w-100" type="submit" value="Creat an account" name="regi"/> 
					</div>
				</div>
	</form>
			</div>
		</div>
			</div>
		</div>


 	
	</div>
 	
</body>


</html>
