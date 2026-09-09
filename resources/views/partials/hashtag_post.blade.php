<article class="card border-0 shadow-sm mb-3 postes">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-3">
            <img src="{{ $post->user->avatar_url }}" width="42" height="42" class="rounded-circle object-fit-cover" alt="">
            <div><a href="{{ url('/profile/'.$post->user_id) }}" class="fw-bold text-dark text-decoration-none">{{ $post->user->first_name }} {{ $post->user->last_name }}</a><div class="small text-muted">{{ $post->created_at?->diffForHumans() }}</div></div>
        </div>
        <p class="mb-3 fs-6">{!! \App\Support\HashtagFormatter::linkify($post->post_text) !!}</p>
        @if(!empty($videos[$post->id][0][0]))
            @php($video = $videos[$post->id][0][0])
            <a href="{{ url('/video/'.$video->id) }}" class="d-block bg-black rounded overflow-hidden">
                <video class="w-100" preload="metadata" poster="{{ $video->thumbnail_path ? asset($video->thumbnail_path) : '' }}"><source src="{{ asset($video->path.$video->id.'/playlist.m3u8') }}" type="application/x-mpegURL"></video>
            </a>
        @endif
        <div class="d-flex justify-content-around border-top mt-3 pt-2 text-muted"><span><i class="far fa-thumbs-up"></i> {{ $post->react->count() }}</span><span><i class="far fa-comment"></i> {{ $post->commentes->count() }}</span></div>
    </div>
</article>
