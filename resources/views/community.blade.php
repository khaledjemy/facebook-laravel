@extends('layout.masterhome')
@section('content')
<div class="container py-4" style="max-width:900px"><h2 class="mb-4">الصفحات والجروبات</h2>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="row g-4"><div class="col-md-6"><div class="card p-3"><h4>الصفحات</h4><form method="POST" action="{{ url('/pages') }}">@csrf<input class="form-control mb-2" name="name" placeholder="اسم الصفحة" required><textarea class="form-control mb-2" name="description" placeholder="وصف الصفحة"></textarea><button class="btn btn-primary">إنشاء صفحة</button></form><hr>@foreach($pages as $page)<div class="p-2 border-bottom"><strong>{{ $page->name }}</strong><div class="text-muted">{{ $page->description }}</div></div>@endforeach</div></div>
<div class="col-md-6"><div class="card p-3"><h4>الجروبات</h4><form method="POST" action="{{ url('/groups') }}">@csrf<input class="form-control mb-2" name="name" placeholder="اسم الجروب" required><textarea class="form-control mb-2" name="description" placeholder="وصف الجروب"></textarea><button class="btn btn-primary">إنشاء جروب</button></form><hr>@foreach($groups as $group)<div class="p-2 border-bottom"><strong>{{ $group->name }}</strong><div class="text-muted">{{ $group->description }}</div></div>@endforeach</div></div></div></div>
@endsection
