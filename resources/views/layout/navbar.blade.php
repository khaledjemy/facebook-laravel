

<!-- النافبار العلوي (ثابت في الأعلى) -->
<nav class="navbar navbar-expand fixed-top bg-white shadow-sm my-0 py-0" style="min-height:56px;">
  <div class="container-fluid h-100 my-0 py-0">
    <div class="row w-100 h-100 align-items-center flex-nowrap mb-0 pb-0">

      <!-- الجزء الأيسر: اللوجو + البحث -->
      <div class="col-3 navbar-brand-area d-flex align-items-center flex-shrink-0 p-0 m-0">
        <a href="{{asset('/')}}" class="facebook-mark" aria-label="Facebook">
          <svg viewBox="0 0 36 36" width="40" height="40" aria-hidden="true"><circle cx="18" cy="18" r="18" fill="#0866ff"/><path fill="#fff" d="M20.2 31V19.4h3.9l.6-4.5h-4.5V12c0-1.3.4-2.2 2.3-2.2h2.4v-4c-.4-.1-1.8-.2-3.5-.2-3.5 0-5.9 2.1-5.9 6.1v3.2h-4v4.5h4V31h4.7z"/></svg>
        </a>
        <form action="{{ url('/search') }}" method="GET" class="navbar-search-form"><i class="fa fa-search"></i><input type="search" name="q" value="{{ request('q') }}" class="form-control rounded-pill border-0" placeholder="{{ __('ui.search') }}"></form>
      </div>

      <!-- الجزء الأوسط: التبويبات (لشاشات الكمبيوتر فقط) -->
      <div id="desktop-tabs" class="col-6 d-flex justify-content-center align-items-end desktop-tabs-col mb-0 pb-0">
        <div class="row w-100 g-0 justify-content-center">
          <div class="fb-nav-tab nav-item navlink text-center">
            <a href="{{ url('/') }}" aria-label="{{ __('ui.home') }}" title="{{ __('ui.home') }}" class="nav-link text-secondary p-3 {{ request()->is('/') ? 'active' : '' }}">
              <i class="fa fa-home fb-tab-fa"></i>
              <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M9.464 1.286C10.294.803 11.092.5 12 .5c.908 0 1.707.303 2.537.786.795.462 1.7 1.142 2.815 1.977l2.232 1.675c1.391 1.042 2.359 1.766 2.888 2.826.53 1.059.53 2.268.528 4.006v4.3c0 1.355 0 2.471-.119 3.355-.124.928-.396 1.747-1.052 2.403-.657.657-1.476.928-2.404 1.053-.884.119-2 .119-3.354.119H7.93c-1.354 0-2.471 0-3.355-.119-.928-.125-1.747-.396-2.403-1.053-.656-.656-.928-1.475-1.053-2.403C1 18.541 1 17.425 1 16.07v-4.3c0-1.738-.002-2.947.528-4.006.53-1.06 1.497-1.784 2.888-2.826L6.65 3.263c1.114-.835 2.02-1.515 2.815-1.977zM10.5 13A1.5 1.5 0 0 0 9 14.5V21h6v-6.5a1.5 1.5 0 0 0-1.5-1.5h-3z"></path></svg>
            </a>
          </div>
          <div class="fb-nav-tab nav-item navlink text-center">
            <a href="{{ url('/watch') }}" aria-label="{{ __('ui.watch') }}" title="{{ __('ui.watch') }}" class="nav-link text-secondary p-3 {{ request()->is('watch*') ? 'active' : '' }}">
              <i class="fa fa-tv fb-tab-fa"></i>
              <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M10.996 8.132A1 1 0 0 0 9.5 9v4a1 1 0 0 0 1.496.868l3.5-2a1 1 0 0 0 0-1.736l-3.5-2z"></path><path d="M14.573 2H9.427c-1.824 0-3.293 0-4.45.155-1.2.162-2.21.507-3.013 1.31C1.162 4.266.817 5.277.655 6.477.5 7.634.5 9.103.5 10.927v.146c0 1.824 0 3.293.155 4.45.162 1.2.507 2.21 1.31 3.012.802.803 1.813 1.148 3.013 1.31C6.134 20 7.603 20 9.427 20h5.146c1.824 0 3.293 0 4.45-.155 1.2-.162 2.21-.507 3.012-1.31.803-.802 1.148-1.813 1.31-3.013.155-1.156.155-2.625.155-4.449v-.146c0-1.824 0-3.293-.155-4.45-.162-1.2-.507-2.21-1.31-3.013-.802-.802-1.813-1.147-3.013-1.309C17.866 2 16.397 2 14.573 2zM3.38 4.879c.369-.37.887-.61 1.865-.741C6.251 4.002 7.586 4 9.5 4h5c1.914 0 3.249.002 4.256.138.978.131 1.496.372 1.865.74.37.37.61.888.742 1.866.135 1.007.137 2.342.137 4.256 0 1.914-.002 3.249-.137 4.256-.132.978-.373 1.496-.742 1.865-.369.37-.887.61-1.865.742-1.007.135-2.342.137-4.256.137h-5c-1.914 0-3.249-.002-4.256-.137-.978-.132-1.496-.373-1.865-.742-.37-.369-.61-.887-.741-1.865C2.502 14.249 2.5 12.914 2.5 11c0-1.914.002-3.249.138-4.256.131-.978.372-1.496.74-1.865zM8 21.5a1 1 0 1 0 0 2h8a1 1 0 1 0 0-2H8z"></path></svg>
            </a>
          </div>
          <div class="fb-nav-tab nav-item navlink text-center">
            <a href="{{ url('/groups') }}" aria-label="{{ __('ui.groups') }}" title="{{ __('ui.groups') }}" class="nav-link text-secondary p-3 {{ request()->is('groups*') ? 'active' : '' }}">
              <i class="fa fa-user-group fb-tab-fa"></i>
              <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M12 5a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm-2 4a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"></path><path d="M12 .5C5.649.5.5 5.649.5 12S5.649 23.5 12 23.5 23.5 18.351 23.5 12 18.351.5 12 .5zM2.5 12c0-.682.072-1.348.209-1.99a2 2 0 0 1 0 3.98A9.539 9.539 0 0 1 2.5 12zm4 0a4.001 4.001 0 0 0-3.16-3.912A9.502 9.502 0 0 1 12 2.5a9.502 9.502 0 0 1 8.66 5.588 4.001 4.001 0 0 0 0 7.824 9.514 9.514 0 0 1-1.755 2.613A5.002 5.002 0 0 0 14 14.5h-4a5.002 5.002 0 0 0-4.905 4.025 9.515 9.515 0 0 1-1.755-2.613A4.001 4.001 0 0 0 6.5 12zm13 0a2 2 0 0 1 1.791-1.99 9.538 9.538 0 0 1 0 3.98A2 2 0 0 1 19.5 12zm-2.51 8.086A9.455 9.455 0 0 1 12 21.5c-1.83 0-3.54-.517-4.99-1.414a1.004 1.004 0 0 1-.01-.148V19.5a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v.438a1 1 0 0 1-.01.148z"></path></svg>
            </a>
          </div>
          <div class="fb-nav-tab nav-item navlink text-center">
            <a href="{{ url('/pages') }}" aria-label="{{ __('ui.pages') }}" title="{{ __('ui.pages') }}" class="nav-link text-secondary p-3 {{ request()->is('pages*') ? 'active' : '' }}">
              <i class="fa fa-flag fb-tab-fa"></i>
              <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M8 8a1 1 0 0 1 1 1v2h2a1 1 0 1 1 0 2H9v2a1 1 0 1 1-2 0v-2H5a1 1 0 1 1 0-2h2V9a1 1 0 0 1 1-1zm8 2a1.5 1.5 0 1 0 3 0 1.5 1.5 0 0 0-3 0zm-2 4a1.5 1.5 0 1 0 3 0 1.5 1.5 0 0 0-3 0z"></path><path d="M.5 11a7 7 0 0 1 7-7h9a7 7 0 0 1 7 7v2a7 7 0 0 1-7 7h-9a7 7 0 0 1-7-7v-2zm7-5a5 5 0 0 0-5 5v2a5 5 0 0 0 5 5h9a5 5 0 0 0 5-5v-2a5 5 0 0 0-5-5h-9z"></path></svg>
            </a>
          </div>
        </div>
      </div>

      <!-- الجزء الأيمن: الأيقونات + الصورة الشخصية -->
      <div class="col-3 d-flex justify-content-end align-items-center p-0 m-0 navbar-right-layer" style="padding-right:0 !important;">
        <div class="d-flex gap-2 align-items-center" style="margin-right: 10px;">
          
          <div class="icon-wrapper position-relative" id="notifications-icon">
            <div class="widget-icon rounded-circle bg-light position-relative">
              <svg viewBox="0 0 24 24" width="20" height="20">
                <path d="M3 9.5a9 9 0 1 1 18 0v2.927c0 1.69.475 3.345 1.37 4.778a1.5 1.5 0 0 1-1.272 2.295h-4.625a4.5 4.5 0 0 1-8.946 0H2.902a1.5 1.5 0 0 1-1.272-2.295A9.01 9.01 0 0 0 3 12.43V9.5zm6.55 10a2.5 2.5 0 0 0 4.9 0h-4.9z"></path></svg>
              <span id="notification-badge" class="badge rounded-pill bg-danger position-absolute" style="display:none; font-size: 10px; top: -2px; right: -2px; padding: 3px 6px;">0</span>
            </div>
            <div id="notifications-popup" class="fb-popup">
              <div class="popup-header d-flex justify-content-between align-items-center">
                <span>الإشعارات</span>
                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-primary" id="mark-all-notifications-read" style="display:none; font-size: 12px;">تحديد الكل كمقروء</button>
              </div>
              <div class="popup-list" id="notifications-list" style="max-height: 360px; overflow-y: auto;">
                <div class="popup-item text-muted"><span class="popup-item-icon"><i class="far fa-bell"></i></span><span>لا توجد إشعارات جديدة</span></div>
              </div>
            </div>
          </div>

          <div class="icon-wrapper" id="chat-icon">
            <div class="widget-icon rounded-circle bg-light">
              <svg viewBox="0 0 12 13" width="20" height="20">
                <g fill-rule="evenodd" transform="translate(-450 -1073)">
                  <path d="m459.603 1077.948-1.762 2.851a.89.89 0 0 1-1.302.245l-1.402-1.072a.354.354 0 0 0-.433.001l-1.893 1.465c-.253.196-.583-.112-.414-.386l1.763-2.851a.89.89 0 0 1 1.301-.245l1.402 1.072a.354.354 0 0 0 .434-.001l1.893-1.465c.253-.196.582.112.413.386M456 1073.5c-3.38 0-6 2.476-6 5.82 0 1.75.717 3.26 1.884 4.305.099.087.158.21.162.342l.032 1.067a.48.48 0 0 0 .674.425l1.191-.526a.473.473 0 0 1 .32-.024c.548.151 1.13.231 1.737.231 3.38 0 6-2.476 6-5.82 0-3.344-2.62-5.82-6-5.82"></path></g></svg>
            </div>
            <div id="chat-popup" class="fb-popup">
              <div class="popup-header">الدردشة</div>
              <div class="popup-list">
                @auth
                  <a class="popup-item text-decoration-none" href="{{ url('/messanger') }}"><span class="popup-item-icon"><i class="fab fa-facebook-messenger"></i></span><span>فتح الرسائل</span></a>
                @else
                  <a class="popup-item text-decoration-none" href="{{ route('login') }}"><span class="popup-item-icon"><i class="fab fa-facebook-messenger"></i></span><span>تسجيل الدخول للمحادثة</span></a>
                @endauth
              </div>
            </div>
          </div>

          <div class="icon-wrapper" id="menu-icon">
            <div class="widget-icon rounded-circle bg-light">
              <svg viewBox="0 0 24 24" width="20" height="20">
                <path d="M18.5 1A1.5 1.5 0 0 0 17 2.5v3A1.5 1.5 0 0 0 18.5 7h3A1.5 1.5 0 0 0 23 5.5v-3A1.5 1.5 0 0 0 21.5 1h-3zm0 8a1.5 1.5 0 0 0-1.5 1.5v3a1.5 1.5 0 0 0 1.5 1.5h3a1.5 1.5 0 0 0 1.5-1.5v-3A1.5 1.5 0 0 0 21.5 9h-3zm-16 8A1.5 1.5 0 0 0 1 18.5v3A1.5 1.5 0 0 0 2.5 23h3A1.5 1.5 0 0 0 7 21.5v-3A1.5 1.5 0 0 0 5.5 17h-3zm8 0A1.5 1.5 0 0 0 9 18.5v3a1.5 1.5 0 0 0 1.5 1.5h3a1.5 1.5 0 0 0 1.5-1.5v-3a1.5 1.5 0 0 0-1.5-1.5h-3zm8 0a1.5 1.5 0 0 0-1.5 1.5v3a1.5 1.5 0 0 0 1.5 1.5h3a1.5 1.5 0 0 0 1.5-1.5v-3a1.5 1.5 0 0 0-1.5-1.5h-3zm-16-8A1.5 1.5 0 0 0 1 10.5v3A1.5 1.5 0 0 0 2.5 15h3A1.5 1.5 0 0 0 7 13.5v-3A1.5 1.5 0 0 0 5.5 9h-3zm0-8A1.5 1.5 0 0 0 1 2.5v3A1.5 1.5 0 0 0 2.5 7h3A1.5 1.5 0 0 0 7 5.5v-3A1.5 1.5 0 0 0 5.5 1h-3zm8 0A1.5 1.5 0 0 0 9 2.5v3A1.5 1.5 0 0 0 10.5 7h3A1.5 1.5 0 0 0 15 5.5v-3A1.5 1.5 0 0 0 13.5 1h-3zm0 8A1.5 1.5 0 0 0 9 10.5v3a1.5 1.5 0 0 0 1.5 1.5h3a1.5 1.5 0 0 0 1.5-1.5v-3A1.5 1.5 0 0 0 13.5 9h-3z"></path></svg>
            </div>
            <div id="menu-popup" class="fb-popup">
              <div class="popup-header">القائمة</div>
              <div class="popup-list">
                @auth<a class="popup-item text-decoration-none" href="{{ url('/community') }}"><span class="popup-item-icon"><i class="fa fa-users"></i></span><span>{{ __('ui.pages_groups') }}</span></a>@endauth
                <a class="popup-item text-decoration-none" href="{{ route('language', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"><span class="popup-item-icon"><i class="fa fa-globe"></i></span><span>{{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}</span></a>
                <a class="popup-item text-decoration-none" href="{{ route('settings') }}"><span class="popup-item-icon"><i class="fa fa-cog"></i></span><span>الإعدادات والخصوصية</span></a>
                <div class="popup-item"><span class="popup-item-icon"><i class="far fa-question-circle"></i></span><span>المساعدة والدعم</span></div>
                <div class="popup-item" id="nav-toggle-dark-mode" style="cursor: pointer;"><span class="popup-item-icon"><i class="far fa-moon"></i></span><span>الوضع المظلم</span><span class="badge bg-secondary ms-auto small" id="nav-dark-mode-status">إيقاف</span></div>
              </div>
            </div>
          </div>

          <!-- الصورة الشخصية -->
          <div class="icon-wrapper" id="profile-icon">
            <div class="widget-icon rounded-circle" style="padding:0;">
              @if(Auth::check())
                @if(isset($profile) && isset($profile['photopro']))
                  <img class="rounded-circle" src="{{ asset($profile['photopro']->path.$profile['profile_photo_id'].$profile['photopro']->type) }}" width="40" height="40" style="object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('img/Default_avatar_profile.jpg') }}';">
                @else
                  <img class="rounded-circle" src="{{ asset('img/Default_avatar_profile.jpg') }}" width="40" height="40">
                @endif
              @endif
            </div>
            <div id="profile-popup" class="profile-menu">
              <div class="popup-list">
                @auth<a class="popup-item text-decoration-none" href="{{ url('/profile/'.Auth::id()) }}"><span class="popup-item-icon"><i class="fa fa-user"></i></span><span>ملفي الشخصي</span></a>@endauth
                <a class="popup-item text-decoration-none" href="{{ route('settings') }}"><span class="popup-item-icon"><i class="fa fa-cog"></i></span><span>الإعدادات والخصوصية</span></a>
                <div class="popup-item"><span class="popup-item-icon"><i class="far fa-question-circle"></i></span><span>مساعدة</span></div>
                @auth
                <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf<button type="submit" class="popup-item border-0 bg-transparent w-100 text-start"><span class="popup-item-icon"><i class="fa fa-sign-out-alt"></i></span><span>تسجيل الخروج</span></button></form>
                @endauth
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</nav>

<!-- الشريط السفلي الثابت للتبويبات (يظهر فقط في الموبايل) -->
<div class="mobile-bottom-nav">
  <div class="nav-tabs-bottom">
    <div class="nav-item navlink text-center">
      <a href="{{ url('/') }}" class="nav-link text-secondary active">
        <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M9.464 1.286C10.294.803 11.092.5 12 .5c.908 0 1.707.303 2.537.786.795.462 1.7 1.142 2.815 1.977l2.232 1.675c1.391 1.042 2.359 1.766 2.888 2.826.53 1.059.53 2.268.528 4.006v4.3c0 1.355 0 2.471-.119 3.355-.124.928-.396 1.747-1.052 2.403-.657.657-1.476.928-2.404 1.053-.884.119-2 .119-3.354.119H7.93c-1.354 0-2.471 0-3.355-.119-.928-.125-1.747-.396-2.403-1.053-.656-.656-.928-1.475-1.053-2.403C1 18.541 1 17.425 1 16.07v-4.3c0-1.738-.002-2.947.528-4.006.53-1.06 1.497-1.784 2.888-2.826L6.65 3.263c1.114-.835 2.02-1.515 2.815-1.977zM10.5 13A1.5 1.5 0 0 0 9 14.5V21h6v-6.5a1.5 1.5 0 0 0-1.5-1.5h-3z"></path></svg>
      </a>
    </div>
    <div class="nav-item navlink text-center">
      <a href="{{ url('/') }}#allpost" class="nav-link text-secondary">
        <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M10.996 8.132A1 1 0 0 0 9.5 9v4a1 1 0 0 0 1.496.868l3.5-2a1 1 0 0 0 0-1.736l-3.5-2z"></path><path d="M14.573 2H9.427c-1.824 0-3.293 0-4.45.155-1.2.162-2.21.507-3.013 1.31C1.162 4.266.817 5.277.655 6.477.5 7.634.5 9.103.5 10.927v.146c0 1.824 0 3.293.155 4.45.162 1.2.507 2.21 1.31 3.012.802.803 1.813 1.148 3.013 1.31C6.134 20 7.603 20 9.427 20h5.146c1.824 0 3.293 0 4.45-.155 1.2-.162 2.21-.507 3.012-1.31.803-.802 1.148-1.813 1.31-3.013.155-1.156.155-2.625.155-4.449v-.146c0-1.824 0-3.293-.155-4.45-.162-1.2-.507-2.21-1.31-3.013-.802-.802-1.813-1.147-3.013-1.309C17.866 2 16.397 2 14.573 2zM3.38 4.879c.369-.37.887-.61 1.865-.741C6.251 4.002 7.586 4 9.5 4h5c1.914 0 3.249.002 4.256.138.978.131 1.496.372 1.865.74.37.37.61.888.742 1.866.135 1.007.137 2.342.137 4.256 0 1.914-.002 3.249-.137 4.256-.132.978-.373 1.496-.742 1.865-.369.37-.887.61-1.865.742-1.007.135-2.342.137-4.256.137h-5c-1.914 0-3.249-.002-4.256-.137-.978-.132-1.496-.373-1.865-.742-.37-.369-.61-.887-.741-1.865C2.502 14.249 2.5 12.914 2.5 11c0-1.914.002-3.249.138-4.256.131-.978.372-1.496.74-1.865zM8 21.5a1 1 0 1 0 0 2h8a1 1 0 1 0 0-2H8z"></path></svg>
      </a>
    </div>
    <div class="nav-item navlink text-center">
      <a href="@auth{{ url('/profile/'.Auth::id()) }}@else{{ url('/login') }}@endauth" class="nav-link text-secondary">
        <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M12 5a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm-2 4a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"></path><path d="M12 .5C5.649.5.5 5.649.5 12S5.649 23.5 12 23.5 23.5 18.351 23.5 12 18.351.5 12 .5zM2.5 12c0-.682.072-1.348.209-1.99a2 2 0 0 1 0 3.98A9.539 9.539 0 0 1 2.5 12zm4 0a4.001 4.001 0 0 0-3.16-3.912A9.502 9.502 0 0 1 12 2.5a9.502 9.502 0 0 1 8.66 5.588 4.001 4.001 0 0 0 0 7.824 9.514 9.514 0 0 1-1.755 2.613A5.002 5.002 0 0 0 14 14.5h-4a5.002 5.002 0 0 0-4.905 4.025 9.515 9.515 0 0 1-1.755-2.613A4.001 4.001 0 0 0 6.5 12zm13 0a2 2 0 0 1 1.791-1.99 9.538 9.538 0 0 1 0 3.98A2 2 0 0 1 19.5 12zm-2.51 8.086A9.455 9.455 0 0 1 12 21.5c-1.83 0-3.54-.517-4.99-1.414a1.004 1.004 0 0 1-.01-.148V19.5a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v.438a1 1 0 0 1-.01.148z"></path></svg>
      </a>
    </div>
    <div class="nav-item navlink text-center">
      <a href="@auth{{ url('/messanger') }}@else{{ url('/login') }}@endauth" class="nav-link text-secondary">
        <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M8 8a1 1 0 0 1 1 1v2h2a1 1 0 1 1 0 2H9v2a1 1 0 1 1-2 0v-2H5a1 1 0 1 1 0-2h2V9a1 1 0 0 1 1-1zm8 2a1.5 1.5 0 1 0 3 0 1.5 1.5 0 0 0-3 0zm-2 4a1.5 1.5 0 1 0 3 0 1.5 1.5 0 0 0-3 0z"></path><path d="M.5 11a7 7 0 0 1 7-7h9a7 7 0 0 1 7 7v2a7 7 0 0 1-7 7h-9a7 7 0 0 1-7-7v-2zm7-5a5 5 0 0 0-5 5v2a5 5 0 0 0 5 5h9a5 5 0 0 0 5-5v-2a5 5 0 0 0-5-5h-9z"></path></svg>
      </a>
    </div>
  </div>
</div>
