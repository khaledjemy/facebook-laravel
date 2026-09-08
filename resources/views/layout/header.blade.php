<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="description" content="" />
<meta name="author" content="" />
<title>{{ config('app.name', 'Social Network') }}</title>
<!-- Favicon-->
<link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
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
<script>
    if (localStorage.getItem('fb_dark_mode') === 'true') {
        document.addEventListener('DOMContentLoaded', function() { document.body.classList.add('dark-theme'); });
    }
</script>
</head>
