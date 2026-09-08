<div id="ms-sidebar-root" class="ms-sidebar-container fixed-top" style="top: 56px; z-index: 1040;">
    <div class="ms-sidebar-content-wrapper" data-scrollbar="true">
        <div class="ms-sidebar-inner">

            <style>
                /* =========================
                   السايد بار الرئيسي
                ========================= */
                .ms-sidebar-container {
                    position: fixed;
                    top: 56px;
                    left: 0;
                    width: 280px;
                    height: calc(100vh - 56px);
                    z-index: 1040;
                }

                /* =========================
                   إخفاء السايد بار في الموبايل
                ========================= */
                @media (max-width: 767px) {
                    .ms-sidebar-container {
                        display: none !important;
                    }
                }

                /* =========================
                   إظهار ثابت للديسكتوب
                ========================= */
                @media (min-width: 768px) {
                    .ms-sidebar-container {
                        display: block !important;
                    }
                }

                /* =========================
                   محتوي السايد بار
                ========================= */
                .ms-sidebar-content-wrapper {
                    height: 100%;
                    overflow-y: auto;
                    overflow-x: hidden;
                    width: 100%;
                    padding: 10px;
                }

                /* =========================
                   Scrollbar
                ========================= */
                .ms-sidebar-content-wrapper::-webkit-scrollbar {
                    width: 6px;
                }

                .ms-sidebar-content-wrapper::-webkit-scrollbar-track {
                    background: #f0f2f5;
                }

                .ms-sidebar-content-wrapper::-webkit-scrollbar-thumb {
                    background: #bdc1c6;
                    border-radius: 10px;
                }

                /* =========================
                   العناصر الداخلية
                ========================= */
                .ms-sidebar-inner {
                    width: 100%;
                }

                .ms-sidebar-list {
                    list-style: none;
                    padding: 0;
                    margin: 0;
                }

                .ms-sidebar-item {
                    border-radius: 12px;
                    margin-bottom: 2px;
                    background: transparent;
                }

                .ms-sidebar-link {
                    display: flex;
                    align-items: center;
                    padding: 10px 12px;
                    border-radius: 12px;
                    transition: background-color 0.2s ease;
                    text-decoration: none;
                }

                .ms-sidebar-link:hover {
                    background-color: #f0f2f5;
                }

                .ms-sidebar-icon {
                    background-image: url(style/FlnJwE1zAUa.png);
                    background-size: 100%;
                    width: 35px;
                    height: 35px;
                    display: block;
                    flex-shrink: 0;
                }

                .ms-sidebar-text {
                    font-size: 15px;
                    font-weight: 500;
                    color: #1c1e21;
                    margin-left: 12px;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                /* =========================
                   العنصر النشط
                ========================= */
                .ms-sidebar-item.active {
                    background-color: #e7f3ff;
                }

                .ms-sidebar-item.active .ms-sidebar-text {
                    color: #1b74e4;
                    font-weight: 600;
                }

                /* =========================
                   صورة البروفايل
                ========================= */
                .ms-sidebar-avatar {
                    width: 35px;
                    height: 35px;
                    border-radius: 50%;
                    object-fit: cover;
                }
            </style>

            <div class="ms-sidebar-padder w-100">
                <ul class="ms-sidebar-list">

                    <!-- البروفايل -->
                    <li class="ms-sidebar-item">
                        <a href="profile/{{Auth::id()}}" class="ms-sidebar-link">

                            <div class="ms-sidebar-avatar-wrapper">
                                @if(Auth::check())
                                    @if(isset($profile) && isset($profile['photopro']))
                                        <img
                                            class="ms-sidebar-avatar"
                                            src="{{ asset($profile['photopro']->path.$profile['profile_photo_id'].$profile['photopro']->type) }}"
                                            alt=""
                                            onerror="this.onerror=null;this.src='{{ asset('img/Default_avatar_profile.jpg') }}';"
                                        >
                                    @else
                                        <img
                                            class="ms-sidebar-avatar"
                                            src="{{ asset('img/Default_avatar_profile.jpg') }}"
                                            alt=""
                                        >
                                    @endif
                                @endif
                            </div>

                            <span class="ms-sidebar-text">
                                {{ Auth::user()->first_name }}
                                {{ Auth::user()->last_name }}
                            </span>
                        </a>
                    </li>

                    <!-- Watch -->
                    <li class="ms-sidebar-item">
                        <a href="{{ url('/watch') }}" class="ms-sidebar-link">
                            <div class="ms-sidebar-icon" style="background-position: 0px -108px;"></div>
                            <span class="ms-sidebar-text">{{ __('ui.watch') }}</span>
                        </a>
                    </li>

                    <!-- Friends -->
                    <li class="ms-sidebar-item">
                        <a href="{{ url('/friends') }}" class="ms-sidebar-link">
                            <div class="ms-sidebar-icon" style="background-position: 0px -737px;"></div>
                            <span class="ms-sidebar-text">{{ __('ui.friends') }}</span>
                        </a>
                    </li>

                    <!-- Pages & Groups -->
                    <li class="ms-sidebar-item">
                        <a href="{{ url('/community') }}" class="ms-sidebar-link">
                            <div class="ms-sidebar-icon" style="background-position: 0px -522px;"></div>
                            <span class="ms-sidebar-text">{{ __('ui.pages_groups') }}</span>
                        </a>
                    </li>

                    <!-- Saves -->
                    <li class="ms-sidebar-item">
                        <a href="{{ url('/saved') }}" class="ms-sidebar-link">
                            <div class="ms-sidebar-icon" style="background-position: 0px -457px;"></div>
                            <span class="ms-sidebar-text">{{ __('ui.saved') }}</span>
                        </a>
                    </li>

                    <!-- Memories -->
                    <li class="ms-sidebar-item">
                        <a href="{{ url('/memories') }}" class="ms-sidebar-link">
                            <div class="ms-sidebar-icon" style="background-position: 0px 453px;"></div>
                            <span class="ms-sidebar-text">{{ __('ui.memories') }}</span>
                        </a>
                    </li>

                </ul>
            </div>

        </div>
    </div>
</div>

<script>
    // العنصر النشط حسب الصفحة الحالية
    document.querySelectorAll('.ms-sidebar-item .ms-sidebar-link').forEach(link => {

        const href = link.getAttribute('href');

        if (
            href &&
            href !== '#' &&
            window.location.pathname === href
        ) {
            link.closest('.ms-sidebar-item').classList.add('active');
        }

        link.addEventListener('click', function () {

            document.querySelectorAll('.ms-sidebar-item')
                .forEach(item => item.classList.remove('active'));

            this.closest('.ms-sidebar-item')
                .classList.add('active');
        });

    });
</script>
