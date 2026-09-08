<!-- Edit Post Modal -->
<div class="modal fade" id="editPostModal" tabindex="-1" aria-labelledby="editPostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border-radius: 16px; border: none;">
            <div class="modal-header border-0 pb-0 position-relative">
                <h5 class="modal-title fw-bold text-center w-100" id="editPostModalLabel">تعديل المنشور</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <input type="hidden" id="editPostId" value="">

                <!-- Author and Audience -->
                <div class="d-flex align-items-center mb-3">
                    @if(Auth::check())
                        <img src="{{ Auth::user()->avatar_url }}" class="rounded-circle border me-2" width="44" height="44" style="object-fit: cover;" alt="">
                        <div>
                            <h6 class="mb-1 fw-bold">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h6>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border rounded-pill px-2 py-0 dropdown-toggle small" type="button" id="editPostAudienceDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-globe-americas me-1" id="editAudienceIcon"></i>
                                    <span id="editAudienceLabel">العامة</span>
                                </button>
                                <input type="hidden" id="editPostVisibilityInput" value="public">
                                <ul class="dropdown-menu shadow small" aria-labelledby="editPostAudienceDropdown">
                                    <li><a class="dropdown-item select-edit-audience" href="javascript:;" data-val="public" data-icon="fa-globe-americas"><i class="fa fa-globe-americas me-2 text-primary"></i> العامة</a></li>
                                    <li><a class="dropdown-item select-edit-audience" href="javascript:;" data-val="friends" data-icon="fa-user-friends"><i class="fa fa-user-friends me-2 text-success"></i> الأصدقاء فقط</a></li>
                                    <li><a class="dropdown-item select-edit-audience" href="javascript:;" data-val="only_me" data-icon="fa-lock"><i class="fa fa-lock me-2 text-secondary"></i> أنا فقط</a></li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Textarea -->
                <div class="mb-3">
                    <textarea class="form-control border-0 fs-5" id="editPostTextInput" rows="4" placeholder="ماذا يدور في ذهنك؟" style="resize: none; box-shadow: none;"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" id="btnSaveEditedPost" class="btn btn-primary rounded-pill px-4 fw-bold">حفظ التعديلات</button>
            </div>
        </div>
    </div>
</div>
