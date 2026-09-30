@extends('layout.masterhome')
@section('content')
<style>
    .community-page { width: min(100%, 1100px); margin-inline: auto; padding: 24px 16px; }
    .community-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px; }
    .community-grid > section { min-width: 0; }
    @media (max-width: 767.98px) { .community-grid { grid-template-columns: minmax(0, 1fr); } }
</style>
<div class="community-page" dir="rtl">
    <h2 class="mb-4">الصفحات والجروبات</h2>

    @if(session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif

    <div class="community-grid">
        <section aria-labelledby="pages-heading">
            <div class="card p-3 h-100">
                <h3 id="pages-heading" class="h4">الصفحات</h3>
                <form method="POST" action="{{ url('/pages') }}" class="mb-3">
                    @csrf
                    <label class="sr-only" for="page-name">اسم الصفحة</label>
                    <input id="page-name" class="form-control mb-2" name="name" maxlength="120" value="{{ old('name') }}" placeholder="اسم الصفحة" required>
                    <label class="sr-only" for="page-description">وصف الصفحة</label>
                    <textarea id="page-description" class="form-control mb-2" name="description" maxlength="500" rows="3" placeholder="وصف الصفحة">{{ old('description') }}</textarea>
                    @if($errors->getBag('page')->any())
                        <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->getBag('page')->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                    @endif
                    <button class="btn btn-primary" type="submit">إنشاء صفحة</button>
                </form>
                <hr>
                @forelse($pages as $page)
                    <article class="p-2 border-bottom">
                        <h4 class="h6 mb-1">{{ $page->name }}</h4>
                        @if($page->description)<p class="text-muted mb-1">{{ $page->description }}</p>@endif
                        <small class="text-muted">بواسطة {{ trim(($page->owner->first_name ?? '').' '.($page->owner->last_name ?? '')) ?: 'مستخدم' }} · {{ $page->created_at->diffForHumans() }}</small>
                    </article>
                @empty
                    <p class="text-muted mb-0">لا توجد صفحات بعد. أنشئ أول صفحة لمجتمعك.</p>
                @endforelse
                <div class="mt-3">{{ $pages->links() }}</div>
            </div>
        </section>

        <section aria-labelledby="groups-heading">
            <div class="card p-3 h-100">
                <h3 id="groups-heading" class="h4">الجروبات</h3>
                <form method="POST" action="{{ url('/groups') }}" class="mb-3">
                    @csrf
                    <label class="sr-only" for="group-name">اسم الجروب</label>
                    <input id="group-name" class="form-control mb-2" name="name" maxlength="120" value="{{ old('name') }}" placeholder="اسم الجروب" required>
                    <label class="sr-only" for="group-description">وصف الجروب</label>
                    <textarea id="group-description" class="form-control mb-2" name="description" maxlength="500" rows="3" placeholder="وصف الجروب">{{ old('description') }}</textarea>
                    @if($errors->getBag('group')->any())
                        <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->getBag('group')->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                    @endif
                    <button class="btn btn-primary" type="submit">إنشاء جروب</button>
                </form>
                <hr>
                @forelse($groups as $group)
                    <article class="p-2 border-bottom">
                        <h4 class="h6 mb-1">{{ $group->name }}</h4>
                        @if($group->description)<p class="text-muted mb-1">{{ $group->description }}</p>@endif
                        <small class="text-muted">بواسطة {{ trim(($group->owner->first_name ?? '').' '.($group->owner->last_name ?? '')) ?: 'مستخدم' }} · {{ $group->created_at->diffForHumans() }}</small>
                    </article>
                @empty
                    <p class="text-muted mb-0">لا توجد جروبات بعد. أنشئ أول جروب وابدأ دعوة الأعضاء.</p>
                @endforelse
                <div class="mt-3">{{ $groups->links() }}</div>
            </div>
        </section>
    </div>
</div>
@endsection
