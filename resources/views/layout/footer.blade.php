
    <div class="bg-white absolute-bottom  p-4 m-0">
        <div class="row  " >
            <ul class="list-inline" aria-label="اختيار اللغة">
                <li class="list-inline-item">{{ app()->getLocale() === 'ar' ? 'العربية' : 'English (UK)' }}</li>
                @if(app()->getLocale() !== 'ar')<li class="list-inline-item"><a href="{{ route('language', 'ar') }}" lang="ar">العربية</a></li>@else<li class="list-inline-item"><a href="{{ route('language', 'en') }}" lang="en">English (UK)</a></li>@endif
            </ul>
        <hr>
        </div>
            <div class="copyright">
                <div>
                    <span>{{ $siteSettings['footer_text'] ?? '© Social Network' }}</span>
                    @if(!empty($siteSettings['support_email']))<span class="mx-2">·</span><a href="mailto:{{ $siteSettings['support_email'] }}">{{ __('ui.contact_support') }}</a>@endif
                </div>
            </div>
    </div>
    
   
