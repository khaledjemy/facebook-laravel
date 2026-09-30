<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="description" content="{{ $siteSettings['site_description'] ?? '' }}" />
<meta name="author" content="" />
<title>{{ config('app.name', 'Social Network') }}</title>
<!-- Favicon-->
<link rel="icon" href="{{ !empty($siteSettings['favicon_path']) ? asset('storage/'.$siteSettings['favicon_path']) : asset('favicon.ico') }}" />
<!-- Custom Google font-->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet" />
<!-- Bootstrap icons-->
<!-- Core theme CSS (includes Bootstrap)-->
<link href="{{ asset('./assets/css/bootstrap-icons.css')}}" rel="stylesheet" />
    <!-- ================== BEGIN core-css ================== -->
<link href="{{ asset('./assets/css/vendor.min.css')}}" rel="stylesheet" />
<link href="{{ asset('./assets/css/facebook/app.min.css')}}" rel="stylesheet" />
	<!-- ================== END core-css ================== -->

	<!-- ================== BEGIN page-css ================== -->
<link href="{{ asset('./assets/plugins/lity/dist/lity.min.css')}}" rel="stylesheet" />
<link href="{{ asset('./assets/plugins/x-editable-bs4/dist/bootstrap4-editable/css/bootstrap-editable.css')}}" rel="stylesheet" />
<link href="{{ asset('./assets/plugins/x-editable-bs4/dist/inputs-ext/address/address.css')}}" rel="stylesheet" />
<link href="{{ asset('./assets/plugins/x-editable-bs4/dist/inputs-ext/typeaheadjs/lib/typeahead.js-bootstrap.css')}}" rel="stylesheet" />
<link href="{{ asset('./assets/plugins/bootstrap-datetime-picker/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet" />
<link href="{{ asset('./assets/plugins/bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.min.css')}}" rel="stylesheet" />
<link href="{{ asset('./assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')}}" rel="stylesheet" />
<link href="{{ asset('./assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" />
<link href="{{ asset('./assets/plugins/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<link href="{{ asset('./assets/css/cropper.min.css')}}" rel="stylesheet">
<link href="{{ asset('./assets/plugins/dropzone/dist/min/dropzone.min.css')}}" rel="stylesheet" />
<link href="{{ asset('./assets/css/video-js.css')}}" rel="stylesheet">
<link href="{{ asset('./assets/css/castuomizing-face.css')}}" rel="stylesheet" />
<link href="{{ asset('style/style.css') }}?v={{ filemtime(public_path('style/style.css')) }}" rel="stylesheet" />
@if(($siteSettings['font_family'] ?? 'Arial') === 'Cairo')<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">@endif
<style>
:root{--brand-primary:{{ $siteSettings['primary_color'] ?? '#0866ff' }};--brand-accent:{{ $siteSettings['accent_color'] ?? '#42b72a' }};--brand-background:{{ $siteSettings['page_background'] ?? '#f0f2f5' }};--brand-navbar:{{ $siteSettings['navbar_color'] ?? '#ffffff' }};--brand-radius:{{ (int)($siteSettings['card_radius'] ?? 8) }}px;--brand-font:'{{ $siteSettings['font_family'] ?? 'Arial' }}',Arial,sans-serif;--fb-blue:var(--brand-primary);--fb-bg:var(--brand-background)}
html,body{background-color:var(--brand-background)!important;font-family:var(--brand-font)!important}.navbar{background-color:var(--brand-navbar)!important}.card,.modal-content,.fb-popup,.profile-menu{border-radius:var(--brand-radius)!important}
.btn-primary,.bg-primary{background-color:var(--brand-primary)!important;border-color:var(--brand-primary)!important}.text-primary,a{--bs-link-color:var(--brand-primary)}.btn-success{background-color:var(--brand-accent)!important;border-color:var(--brand-accent)!important}.site-brand img{width:40px;height:40px;object-fit:contain;border-radius:8px}
</style>
<script>
    if (localStorage.getItem('fb_dark_mode') === 'true') {
        document.addEventListener('DOMContentLoaded', function() { document.body.classList.add('dark-theme'); });
    }
</script>
</head>
