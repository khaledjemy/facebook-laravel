<!-- Share Post Modal -->
<div class="modal fade" id="sharePostModal" tabindex="-1" aria-labelledby="sharePostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="sharePostModalLabel">مشاركة المنشور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="shareTargetPostId" value="">
                <div class="d-flex align-items-center mb-3">
                    @if(isset($profile) && isset($profile['photopro']))
                        <img src="{{ asset($profile['photopro']->path.$profile['profile_photo_id'].$profile['photopro']->type) }}" class="rounded-circle me-2" width="40" height="40" alt="">
                    @else
                        <img src="{{ asset('img/Default_avatar_profile.jpg') }}" class="rounded-circle me-2" width="40" height="40" alt="">
                    @endif
                    <div>
                        <div class="fw-bold">{{ auth()->user()?->first_name }} {{ auth()->user()?->last_name }}</div>
                        <select id="sharePostVisibility" class="form-select form-select-sm py-0 px-2 rounded-pill mt-1" style="width: auto; font-size: 12px;">
                            <option value="public" selected>🌍 عام (Public)</option>
                            <option value="friends">👥 الأصدقاء (Friends)</option>
                            <option value="only_me">🔒 أنا فقط (Only Me)</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <textarea id="sharePostCaption" class="form-control border-0 shadow-none fs-5 p-0" rows="3" placeholder="قل شيئاً عن هذا المنشور..."></textarea>
                </div>
                <div id="sharePostPreviewContainer" class="border rounded p-3 bg-light text-muted small">
                    <!-- Post snippet preview will be placed here -->
                    <span id="sharePostAuthorPreview" class="fw-bold text-dark"></span>: <span id="sharePostSnippetPreview"></span>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" id="btnConfirmSharePost" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="fa fa-share me-1"></i> مشاركة الآن
                </button>
            </div>
        </div>
    </div>
</div>
