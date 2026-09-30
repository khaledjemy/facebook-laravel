<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ __('ui.maintenance_title') }}</title>
    <style>body{margin:0;font-family:{{ $siteSettings['font_family'] ?? 'Tahoma' }},Arial;background:{{ $siteSettings['page_background'] ?? '#f0f2f5' }};color:#1c1e21;display:grid;place-items:center;min-height:100vh}.card{max-width:560px;margin:20px;padding:48px;background:#fff;border-radius:{{ $siteSettings['card_radius'] ?? 20 }}px;box-shadow:0 8px 30px #0001;text-align:center}.icon{font-size:55px}.brand{max-width:180px;max-height:70px}.card h1{font-size:28px}.card p{color:#65676b;line-height:1.8}.card a{color:{{ $siteSettings['primary_color'] ?? '#0866ff' }};text-decoration:none;font-weight:bold}</style>
</head>
<body><main class="card">@if(!empty($siteSettings['logo_path']))<img class="brand" src="{{ asset('storage/'.$siteSettings['logo_path']) }}" alt="{{ config('app.name') }}">@else<div class="icon">🛠️</div>@endif<h1>{{ __('ui.maintenance_heading') }}</h1><p>{{ __('ui.maintenance_message') }}</p><a href="{{ route('login') }}">{{ __('ui.admin_sign_in') }}</a></main></body>
</html>
