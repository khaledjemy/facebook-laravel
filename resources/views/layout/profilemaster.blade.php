<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

@include('layout.header')
<body>
    <div id="app" class="app  ">

        @include('layout.navbar')
        @yield('content')

    </div>


 <!-- Footer-->
    @include('js_')
    @include('_Componant')
    <div class=" fade">

    </div>
    <div style="display: none" id="loading">
        @include('layout.post_skeleton')
    </div>

</body>
</html>
