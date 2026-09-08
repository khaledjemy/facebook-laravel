<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

@include('layout.header')
<body>
    <div id="app" class="app  app-sidebar-fixed  app-with-wide-sidebar">
        @include('layout.navbar')
        @yield('content')
    </div>


 <!-- Footer-->
    @include('js_')
    @include('_Componant')
        <div class=" fade">
                @include('layout.footer')
        </div>
    <div style="display: none" id="loading" data-loading-type="post">
        @include('layout.media_skeleton', ['type' => 'post'])
    </div>

</body>
</html>
