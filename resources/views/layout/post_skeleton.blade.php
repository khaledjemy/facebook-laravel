<div class="feed-loading" aria-label="Loading posts" role="status">
    @for($skeletonIndex = 0; $skeletonIndex < 2; $skeletonIndex++)
        <div class="skeleton-post" aria-hidden="true">
            <div class="skeleton skeleton-avatar"></div>
            <div class="skeleton-lines">
                <div class="skeleton skeleton-line short"></div>
                <div class="skeleton skeleton-line tiny"></div>
            </div>
            <div class="skeleton skeleton-text"></div>
            <div class="skeleton skeleton-text medium"></div>
            <div class="skeleton skeleton-media"></div>
            <div class="skeleton-actions">
                <span class="skeleton skeleton-action"></span>
                <span class="skeleton skeleton-action"></span>
                <span class="skeleton skeleton-action"></span>
            </div>
        </div>
    @endfor
</div>
