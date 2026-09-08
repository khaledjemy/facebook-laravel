<html>
@include("layout.header")
<body class="bg-light  d-flex flex-column min-vh-100">
<main class="flex-grow-1">
    <div class="container-xxl  bg-light p-0 m-0 ">
        <div class="row my-5 py-5 g-0" >
            <div class="col-md-6 col-12 text-start p-4 mx-4" >
                <div class="row ">
                    <div class="col-md-9 mx-4" >
                        <img  class="w-25 justify-content-left" src="{{asset('/img/4lCu2zih0ca.svg')  }}" alt="">
                    </div>
                </div>
                <div class="text-dark  h2">
                    Facebook helps you connect and share with the people in your life.
                    </div>
            </div>
            <div class="col-md-4 col-12 text-start bg-white p-2  mx-5 rounded shadow" >
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="row p-2" >
                        <div class="col-md-12  " >
                            <input type="text" class="form-control shadow-sm py-3" placeholder="اسم المستخدم او البريد الاليكترونى" name="email" autocomplete="off"/>
                            @error('email')  
                        <strong>{{ $message }}</strong>   
                        @enderror
                        </div> 
                    </div>
                    <div class="row p-2" >
                        <div class="col-md-12 " >
                            <input type="password" autocomplete="off" class="form-control  shadow-sm py-3" placeholder="كلمة السر (المرور)" name="password">
                                @error('password')
                            <strong>{{ $message }}</strong>
                                @enderror
                        </div>
                    </div>
                    <div class="row p-2" >
                        <div class="col-md-12 " >
                            <button class="btn btn-primary form-control py-3" >تسجيل الدخول</button>
                    <div class="text-center py-2" >
                        <strong class="h3 ">
                            <a href="#" >هل نسيت كلمة السر؟</a>
                        </strong>
                    </div>
                    <hr>
                    <div class="row justify-content-center" >
                        <div class="col-md-8 p-2 d-flex justify-content-center text-center" >
                            <a href="reg" class="btn btn-success form-control py-3" >إنشاء حساب جديد </a>
                        </div>
                    </div>
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