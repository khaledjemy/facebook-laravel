<div class="stories-wrapper mb-3 position-relative">
    <!-- Left / Right scroll arrows -->
    <button type="button" id="btnScrollStoriesLeft" class="story-tray-nav-btn left d-none" aria-label="السابق">
        <i class="fa fa-chevron-right"></i>
    </button>
    <button type="button" id="btnScrollStoriesRight" class="story-tray-nav-btn right" aria-label="التالي">
        <i class="fa fa-chevron-left"></i>
    </button>

    <div class="stories-container" id="storiesTray">
        <!-- Create Story Card -->
        <div class="card story-card create-story-card me-2 text-center shadow-sm flex-shrink-0"
             data-bs-toggle="modal" data-bs-target="#createStoryModal">
            <div class="story-avatar-top">
                @if(isset($profile) && isset($profile['photopro']))
                    <img src="{{ asset($profile['photopro']->path.$profile['profile_photo_id'].$profile['photopro']->type) }}" alt="" onerror="this.onerror=null;this.src='{{ asset('img/Default_avatar_profile.jpg') }}';">
                @else
                    <img src="{{ asset('img/Default_avatar_profile.jpg') }}" alt="">
                @endif
            </div>
            <div class="story-plus-btn">
                <i class="fa fa-plus text-white" style="font-size: 15px;"></i>
            </div>
            <div class="story-card-title">
                <span>إنشاء قصة</span>
            </div>
        </div>

        <!-- Stories loaded dynamically via JS -->
        <div id="storiesItems" class="d-inline-flex gap-2"></div>
    </div>
</div>

<!-- Create Story Modal -->
<div class="modal fade" id="createStoryModal" tabindex="-1" aria-labelledby="createStoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border-radius: 16px; border: none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="createStoryModalLabel">إنشاء قصة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <ul class="nav nav-pills nav-fill mb-3 bg-light p-1 rounded-pill" id="storyTypeTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill fw-bold py-2" id="text-story-tab" data-bs-toggle="pill" data-bs-target="#text-story-pane" type="button" role="tab">
                            <i class="fa fa-font me-1"></i> قصة نصية
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill fw-bold py-2" id="media-story-tab" data-bs-toggle="pill" data-bs-target="#media-story-pane" type="button" role="tab">
                            <i class="fa fa-image me-1"></i> صورة أو فيديو
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="storyTabContent">
                    <!-- Text Story -->
                    <div class="tab-pane fade show active" id="text-story-pane" role="tabpanel">
                        <div id="storyTextPreview" class="d-flex align-items-center justify-content-center p-4 rounded-3 mb-3 text-white text-center fw-bold shadow-sm"
                             style="min-height: 220px; font-size: 22px; line-height: 1.4; background: linear-gradient(135deg, #4f46e5, #06b6d4); word-break: break-word;">
                            اكتب شيئاً...
                        </div>
                        <div class="mb-3">
                            <textarea id="storyTextInput" class="form-control rounded-3" rows="3" placeholder="ماذا يدور في ذهنك؟" maxlength="1000"></textarea>
                        </div>
                        <label class="form-label fw-bold small text-muted">اختر تدرج الخلفية:</label>
                        <div class="d-flex gap-2 mb-2">
                            <div class="story-bg-option rounded-circle border border-2 border-white shadow-sm" style="width: 34px; height: 34px; cursor: pointer; background: linear-gradient(135deg, #4f46e5, #06b6d4);" data-bg="linear-gradient(135deg, #4f46e5, #06b6d4)"></div>
                            <div class="story-bg-option rounded-circle border border-2 border-white shadow-sm" style="width: 34px; height: 34px; cursor: pointer; background: linear-gradient(135deg, #f43f5e, #fb923c);" data-bg="linear-gradient(135deg, #f43f5e, #fb923c)"></div>
                            <div class="story-bg-option rounded-circle border border-2 border-white shadow-sm" style="width: 34px; height: 34px; cursor: pointer; background: linear-gradient(135deg, #10b981, #06b6d4);" data-bg="linear-gradient(135deg, #10b981, #06b6d4)"></div>
                            <div class="story-bg-option rounded-circle border border-2 border-white shadow-sm" style="width: 34px; height: 34px; cursor: pointer; background: linear-gradient(135deg, #8b5cf6, #ec4899);" data-bg="linear-gradient(135deg, #8b5cf6, #ec4899)"></div>
                            <div class="story-bg-option rounded-circle border border-2 border-white shadow-sm" style="width: 34px; height: 34px; cursor: pointer; background: #1877f2;" data-bg="#1877f2"></div>
                            <div class="story-bg-option rounded-circle border border-2 border-white shadow-sm" style="width: 34px; height: 34px; cursor: pointer; background: #242526;" data-bg="#242526"></div>
                        </div>
                    </div>
                    <!-- Media Story -->
                    <div class="tab-pane fade" id="media-story-pane" role="tabpanel">
                        <div class="mb-3">
                            <input type="file" id="storyMediaInput" class="form-control rounded-3" accept="image/*,video/*">
                        </div>
                        <div id="storyMediaPreview" class="d-none text-center mb-3 bg-dark rounded-3 p-2">
                            <img id="storyPreviewImg" src="" class="img-fluid rounded-3" style="max-height: 240px; object-fit: contain;" alt="">
                        </div>
                        <div class="mb-3">
                            <input type="text" id="storyMediaCaption" class="form-control rounded-3" placeholder="أضف تعليقاً على القصة (اختياري)..." maxlength="255">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" id="btnPublishStory" class="btn btn-primary rounded-pill px-4 fw-bold">نشر القصة</button>
            </div>
        </div>
    </div>
</div>

<!-- View Story Modal / Lightbox (Instagram/Facebook Style) -->
<div class="modal fade" id="viewStoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content bg-dark text-white border-0 shadow-2xl" style="border-radius: 20px; overflow: hidden; height: 680px; max-height: 92vh;">
            <div class="modal-body p-0 position-relative d-flex flex-column h-100">
                <!-- Segmented Progress Bar -->
                <div class="story-progress-segments" id="storyProgressSegments">
                    <!-- Segment bars generated dynamically by JS -->
                </div>

                <!-- Story Header -->
                <div class="position-absolute start-0 end-0 px-3 pt-4 pb-2 d-flex align-items-center justify-content-between" style="top: 8px; z-index: 15; background: linear-gradient(to bottom, rgba(0,0,0,0.65) 0%, transparent 100%);">
                    <div class="d-flex align-items-center">
                        <img id="modalStoryAuthorAvatar" src="" class="rounded-circle border border-white border-2 me-2" width="40" height="40" style="object-fit: cover;" alt="">
                        <div>
                            <div id="modalStoryAuthorName" class="fw-bold text-white fs-6" style="line-height: 1.2;"></div>
                            <div id="modalStoryTime" class="text-white-50 small" style="font-size: 11px;"></div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" id="btnDeleteCurrentStory" class="btn btn-sm btn-outline-danger border-0 text-white opacity-75" title="حذف القصة">
                            <i class="fa fa-trash"></i>
                        </button>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <!-- Story Display Area -->
                <div id="modalStoryContentArea" class="flex-grow-1 d-flex align-items-center justify-content-center text-center position-relative" style="height: 100%; overflow: hidden;">
                    <!-- Rendered Image, Video, or Text -->
                </div>

                <!-- Tap Zones for Previous / Next -->
                <div id="storyTapPrev" class="position-absolute top-0 bottom-0 start-0" style="width: 35%; z-index: 10; cursor: pointer;"></div>
                <div id="storyTapNext" class="position-absolute top-0 bottom-0 end-0" style="width: 35%; z-index: 10; cursor: pointer;"></div>

                <!-- Floating Navigation Chevrons -->
                <button type="button" id="btnStoryPrev" class="btn position-absolute top-50 start-0 translate-middle-y text-white fs-4 p-2 bg-transparent border-0" style="z-index: 12;">
                    <i class="fa fa-chevron-left opacity-75"></i>
                </button>
                <button type="button" id="btnStoryNext" class="btn position-absolute top-50 end-0 translate-middle-y text-white fs-4 p-2 bg-transparent border-0" style="z-index: 12;">
                    <i class="fa fa-chevron-right opacity-75"></i>
                </button>

                <!-- Story Reaction Floating Feedback -->
                <div id="storyReactionFeedback" class="position-absolute start-50 translate-middle-x bg-dark bg-opacity-75 text-white px-3 py-1 rounded-pill small d-none" style="bottom: 70px; z-index: 25; pointer-events: none;"></div>

                <!-- Bottom Quick Reactions / Reply -->
                <div class="story-reactions-tray" id="storyReactionsTray">
                    <input type="text" id="storyReplyInput" class="story-reply-input" placeholder="أرسل رسالة إلى القصة...">
                    <div class="d-flex gap-1">
                        <button type="button" class="story-quick-react-btn" data-emoji="❤️" title="Love">❤️</button>
                        <button type="button" class="story-quick-react-btn" data-emoji="😂" title="Haha">😂</button>
                        <button type="button" class="story-quick-react-btn" data-emoji="😮" title="Wow">😮</button>
                        <button type="button" class="story-quick-react-btn" data-emoji="🔥" title="Fire">🔥</button>
                        <button type="button" class="story-quick-react-btn" data-emoji="👍" title="Like">👍</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
