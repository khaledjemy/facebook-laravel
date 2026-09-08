@if (Auth::check())
    @include('welcome')
@else
    @include('layout.login')
    
@endif
