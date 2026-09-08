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
    <div style="display: none" id="loading" data-loading-type="post">
        <div class="loading-placeholder loading-placeholder-post">@include('layout.media_skeleton', ['type' => 'post'])</div>
        <div class="loading-placeholder loading-placeholder-photo d-none">@include('layout.media_skeleton', ['type' => 'photo'])</div>
        <div class="loading-placeholder loading-placeholder-video d-none">@include('layout.media_skeleton', ['type' => 'video'])</div>
    </div>

</body>
</html>
