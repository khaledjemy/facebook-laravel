@php $skeletonType = $type ?? 'post'; @endphp

@if($skeletonType === 'video')
    <div class="skeleton-media-grid skeleton-video-grid" aria-label="Loading videos" role="status">
        @for($i = 0; $i < 6; $i++)
            <div class="skeleton-video-card" aria-hidden="true">
                <div class="skeleton skeleton-video-frame">
                    <span class="skeleton-video-play"><i class="fa fa-play"></i></span>
                    <span class="skeleton skeleton-video-duration"></span>
                </div>
                <div class="skeleton skeleton-media-title"></div>
                <div class="skeleton skeleton-media-meta"></div>
            </div>
        @endfor
    </div>
@elseif($skeletonType === 'photo')
    <div class="skeleton-media-grid skeleton-photo-grid" aria-label="Loading photos" role="status">
        @for($i = 0; $i < 8; $i++)
            <div class="skeleton skeleton-photo-tile" aria-hidden="true">
                <i class="far fa-image skeleton-photo-icon"></i>
            </div>
        @endfor
    </div>
@else
    @include('layout.post_skeleton')
@endif
