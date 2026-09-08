@php
    $currentVisibility = auth()->check() ? (auth()->user()->default_post_visibility ?? 'public') : 'public';
    $visibilityConfig = [
        'public' => ['icon' => 'fa-globe-americas', 'color' => 'text-primary', 'label' => __('ui.public'), 'desc' => __('ui.public_desc')],
        'friends' => ['icon' => 'fa-user-friends', 'color' => 'text-success', 'label' => __('ui.friends'), 'desc' => __('ui.friends_desc')],
        'only_me' => ['icon' => 'fa-lock', 'color' => 'text-secondary', 'label' => __('ui.only_me'), 'desc' => __('ui.only_me_desc')],
    ];
    $activeVis = $visibilityConfig[$currentVisibility] ?? $visibilityConfig['public'];
    $authorName = auth()->check() ? (auth()->user()->first_name . ' ' . auth()->user()->last_name) : 'User';
@endphp

    <!-- Modal 2: Create Post with Media/Dropzone -->
    <div class="modal fade" id="modal-dialog2" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
            <div class="modal-content shadow-lg border-0" style="border-radius: 16px;">
                <div class="modal-header border-bottom position-relative py-3">
                    <h5 class="modal-title fw-bold text-center w-100 mb-0">{{ __('ui.create_post') }}</h5>
                    <a href="#modal-dialog" class="btn btn-light rounded-circle position-absolute start-0 ms-3 d-flex align-items-center justify-content-center" data-bs-toggle="modal" style="width: 36px; height: 36px;" title="Back">
                        <i class="fa fa-arrow-left text-muted"></i>
                    </a>
                </div>
                <div class="modal-body p-3">
                    <div id="progressWrapper" style="width: 100%; background-color: #f3f3f3; border: 1px solid #ccc; height: 24px; border-radius: 12px; overflow: hidden; display: none;" class="mb-2">
                        <div id="progressBar" style="width: 0%; height: 100%; background-color: #1877f2; transition: width 0.3s;"></div>
                    </div>
                    <div id="percentage" class="text-center small fw-bold text-muted mb-2 d-none">0%</div>

                    <!-- Author Info & Audience Selector -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-2">
                            @if(isset($profile) && isset($profile['photopro']))
                                <img class="rounded-circle border" src="{{ asset($profile['photopro']->path.$profile['profile_photo_id'].$profile['photopro']->type) }}" height="44" width="44" style="object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('img/Default_avatar_profile.jpg') }}';" alt=""/>
                            @elseif(Auth::check() && Auth::user()->avatar_url)
                                <img class="rounded-circle border" src="{{ Auth::user()->avatar_url }}" height="44" width="44" style="object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('img/Default_avatar_profile.jpg') }}';" alt=""/>
                            @else
                                <img class="rounded-circle border" src="{{ asset('img/Default_avatar_profile.jpg') }}" height="44" width="44" style="object-fit: cover;" alt=""/>
                            @endif
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">{{ $authorName }}</h6>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border rounded-pill px-2 py-0 dropdown-toggle small d-inline-flex align-items-center gap-1 post-audience-btn shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #e4e6eb; font-size: 13px; font-weight: 600; color: #050505;">
                                    <i class="fa {{ $activeVis['icon'] }} {{ $activeVis['color'] }} post-audience-current-icon" style="font-size: 12px;"></i>
                                    <span class="post-audience-current-label">{{ $activeVis['label'] }}</span>
                                </button>
                                <ul class="dropdown-menu shadow-lg border-0 rounded-3 py-1 post-audience-menu" style="min-width: 270px; z-index: 1060;">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 select-post-audience {{ $currentVisibility === 'public' ? 'active bg-light text-dark' : '' }}" href="javascript:;" data-val="public" data-icon="fa-globe-americas" data-color="text-primary" data-label="{{ __('ui.public') }}">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; min-width: 36px;">
                                                <i class="fa fa-globe-americas text-primary fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold" style="font-size: 14px;">{{ __('ui.public') }} (Public)</div>
                                                <small class="text-muted d-block" style="font-size: 11px;">{{ __('ui.public_desc') }}</small>
                                            </div>
                                            <i class="fa fa-check text-primary check-audience {{ $currentVisibility === 'public' ? '' : 'd-none' }}" data-val="public"></i>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 select-post-audience {{ $currentVisibility === 'friends' ? 'active bg-light text-dark' : '' }}" href="javascript:;" data-val="friends" data-icon="fa-user-friends" data-color="text-success" data-label="{{ __('ui.friends') }}">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; min-width: 36px;">
                                                <i class="fa fa-user-friends text-success fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold" style="font-size: 14px;">{{ __('ui.friends') }} (Friends)</div>
                                                <small class="text-muted d-block" style="font-size: 11px;">{{ __('ui.friends_desc') }}</small>
                                            </div>
                                            <i class="fa fa-check text-primary check-audience {{ $currentVisibility === 'friends' ? '' : 'd-none' }}" data-val="friends"></i>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 select-post-audience {{ $currentVisibility === 'only_me' ? 'active bg-light text-dark' : '' }}" href="javascript:;" data-val="only_me" data-icon="fa-lock" data-color="text-secondary" data-label="{{ __('ui.only_me') }}">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; min-width: 36px;">
                                                <i class="fa fa-lock text-secondary fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold" style="font-size: 14px;">{{ __('ui.only_me') }} (Only Me)</div>
                                                <small class="text-muted d-block" style="font-size: 11px;">{{ __('ui.only_me_desc') }}</small>
                                            </div>
                                            <i class="fa fa-check text-primary check-audience {{ $currentVisibility === 'only_me' ? '' : 'd-none' }}" data-val="only_me"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Textarea -->
                    <div class="mb-3">
                        <textarea class="post-composer-text form-control border-0 shadow-none fs-5 p-1" placeholder="{{ __('ui.whats_on_your_mind') }}" style="min-height: 110px; resize: none;"></textarea>
                    </div>

                    <!-- Dropzone -->
                    <div id="myDropzone" class="position-relative mb-3">
                        <div class="dropzoneDiv w-100 bg-light rounded-3 border-2 border-dashed p-4 text-center text-muted" id="previewDiv" style="cursor: pointer; min-height: 120px;">
                            <i class="fa fa-cloud-upload-alt fs-2 mb-2 d-block text-primary"></i>
                            <span>Drop files here or click to upload</span>
                        </div>
                        <button id="sendButton" class="d-none">Send</button>
                    </div>

                    <!-- Add to your post -->
                    <div class="rounded-3 border p-2 mb-3 d-flex justify-content-between align-items-center">
                        <div class="fw-bold text-dark px-2 small">{{ __('ui.add_to_your_post') }}</div>
                        <div>
                            <ul class="nav nav-pills align-items-center">
                                <li class="nav-item mx-1">
                                    <a href="#modal-dialog" data-bs-toggle="modal" class="btn btn-sm btn-light rounded-circle p-1" title="Write Text">
                                        <img class="rounded img-fluid" style="width: 24px; height: 24px;" src="{{ asset('style/Ivw7nhRtXyo.png') }}" alt="">
                                    </a>
                                </li>
                                <li class="nav-item mx-1">
                                    <a href="#modal-dialog2" data-bs-toggle="tab" class="btn btn-sm btn-light rounded-circle p-1" title="Tag People">
                                        <img class="rounded img-fluid" style="width: 24px; height: 24px;" src="{{ asset('style/b37mHA1PjfK.png') }}" alt="">
                                    </a>
                                </li>
                                <li class="nav-item mx-1">
                                    <a href="#default-tab-1" data-bs-toggle="tab" class="btn btn-sm btn-light rounded-circle p-1" title="Feeling/Activity">
                                        <img class="rounded img-fluid" style="width: 24px; height: 24px;" src="{{ asset('style/Y4mYLVOhTwq.png') }}" alt="">
                                    </a>
                                </li>
                                <li class="nav-item mx-1">
                                    <a href="#default-tab-1" data-bs-toggle="tab" class="btn btn-sm btn-light rounded-circle p-1" title="Check in">
                                        <img class="rounded img-fluid" style="width: 24px; height: 24px;" src="{{ asset('style/8zlaieBcZ72.png') }}" alt="">
                                    </a>
                                </li>
                                <li class="nav-item mx-1">
                                    <a href="#default-tab-1" data-bs-toggle="tab" class="btn btn-sm btn-light rounded-circle p-1" title="More">
                                        <img class="rounded img-fluid" style="width: 24px; height: 24px;" src="{{ asset('style/h_kj6ECZ7Ii.png') }}" alt="">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="modal-footer border-0 p-0">
                        <button type="button" id="PostSubmit" class="col-12 btn btn-primary rounded-3 fw-bold py-2 fs-6">{{ __('ui.post') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 1: Create Post (Text Main Composer) -->
    <div class="modal fade" id="modal-dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
            <div class="modal-content shadow-lg border-0" style="border-radius: 16px;">
                <div class="modal-header border-bottom position-relative py-3">
                    <h5 class="modal-title fw-bold text-center w-100 mb-0">{{ __('ui.create_post') }}</h5>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <!-- Author Info & Audience Selector -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-2">
                            @if(isset($profile) && isset($profile['photopro']))
                                <img class="rounded-circle border" src="{{ asset($profile['photopro']->path.$profile['profile_photo_id'].$profile['photopro']->type) }}" height="44" width="44" style="object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('img/Default_avatar_profile.jpg') }}';" alt=""/>
                            @elseif(Auth::check() && Auth::user()->avatar_url)
                                <img class="rounded-circle border" src="{{ Auth::user()->avatar_url }}" height="44" width="44" style="object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('img/Default_avatar_profile.jpg') }}';" alt=""/>
                            @else
                                <img class="rounded-circle border" src="{{ asset('img/Default_avatar_profile.jpg') }}" height="44" width="44" style="object-fit: cover;" alt=""/>
                            @endif
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">{{ $authorName }}</h6>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border rounded-pill px-2 py-0 dropdown-toggle small d-inline-flex align-items-center gap-1 post-audience-btn shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #e4e6eb; font-size: 13px; font-weight: 600; color: #050505;">
                                    <i class="fa {{ $activeVis['icon'] }} {{ $activeVis['color'] }} post-audience-current-icon" style="font-size: 12px;"></i>
                                    <span class="post-audience-current-label">{{ $activeVis['label'] }}</span>
                                </button>
                                <ul class="dropdown-menu shadow-lg border-0 rounded-3 py-1 post-audience-menu" style="min-width: 270px; z-index: 1060;">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 select-post-audience {{ $currentVisibility === 'public' ? 'active bg-light text-dark' : '' }}" href="javascript:;" data-val="public" data-icon="fa-globe-americas" data-color="text-primary" data-label="{{ __('ui.public') }}">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; min-width: 36px;">
                                                <i class="fa fa-globe-americas text-primary fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold" style="font-size: 14px;">{{ __('ui.public') }} (Public)</div>
                                                <small class="text-muted d-block" style="font-size: 11px;">{{ __('ui.public_desc') }}</small>
                                            </div>
                                            <i class="fa fa-check text-primary check-audience {{ $currentVisibility === 'public' ? '' : 'd-none' }}" data-val="public"></i>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 select-post-audience {{ $currentVisibility === 'friends' ? 'active bg-light text-dark' : '' }}" href="javascript:;" data-val="friends" data-icon="fa-user-friends" data-color="text-success" data-label="{{ __('ui.friends') }}">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; min-width: 36px;">
                                                <i class="fa fa-user-friends text-success fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold" style="font-size: 14px;">{{ __('ui.friends') }} (Friends)</div>
                                                <small class="text-muted d-block" style="font-size: 11px;">{{ __('ui.friends_desc') }}</small>
                                            </div>
                                            <i class="fa fa-check text-primary check-audience {{ $currentVisibility === 'friends' ? '' : 'd-none' }}" data-val="friends"></i>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 select-post-audience {{ $currentVisibility === 'only_me' ? 'active bg-light text-dark' : '' }}" href="javascript:;" data-val="only_me" data-icon="fa-lock" data-color="text-secondary" data-label="{{ __('ui.only_me') }}">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; min-width: 36px;">
                                                <i class="fa fa-lock text-secondary fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold" style="font-size: 14px;">{{ __('ui.only_me') }} (Only Me)</div>
                                                <small class="text-muted d-block" style="font-size: 11px;">{{ __('ui.only_me_desc') }}</small>
                                            </div>
                                            <i class="fa fa-check text-primary check-audience {{ $currentVisibility === 'only_me' ? '' : 'd-none' }}" data-val="only_me"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Textarea -->
                    <div class="mb-3">
                        <textarea class="post-composer-text form-control border-0 shadow-none fs-5 p-1" id="postComposerText" placeholder="{{ __('ui.whats_on_your_mind') }}" style="min-height: 120px; resize: none;"></textarea>
                    </div>

                    <!-- Add to your post -->
                    <div class="rounded-3 border p-2 mb-3 d-flex justify-content-between align-items-center">
                        <div class="fw-bold text-dark px-2 small">{{ __('ui.add_to_your_post') }}</div>
                        <div>
                            <ul class="nav nav-pills align-items-center">
                                <li class="nav-item mx-1">
                                    <a href="#modal-dialog2" data-bs-toggle="modal" class="btn btn-sm btn-light rounded-circle p-1" title="Photo/Video">
                                        <img class="rounded img-fluid" style="width: 24px; height: 24px;" src="{{ asset('style/Ivw7nhRtXyo.png') }}" alt="">
                                    </a>
                                </li>
                                <li class="nav-item mx-1">
                                    <a href="#modal-dialog2" data-bs-toggle="tab" class="btn btn-sm btn-light rounded-circle p-1" title="Tag People">
                                        <img class="rounded img-fluid" style="width: 24px; height: 24px;" src="{{ asset('style/b37mHA1PjfK.png') }}" alt="">
                                    </a>
                                </li>
                                <li class="nav-item mx-1">
                                    <a href="#default-tab-1" data-bs-toggle="tab" class="btn btn-sm btn-light rounded-circle p-1" title="Feeling/Activity">
                                        <img class="rounded img-fluid" style="width: 24px; height: 24px;" src="{{ asset('style/Y4mYLVOhTwq.png') }}" alt="">
                                    </a>
                                </li>
                                <li class="nav-item mx-1">
                                    <a href="#default-tab-1" data-bs-toggle="tab" class="btn btn-sm btn-light rounded-circle p-1" title="Check in">
                                        <img class="rounded img-fluid" style="width: 24px; height: 24px;" src="{{ asset('style/8zlaieBcZ72.png') }}" alt="">
                                    </a>
                                </li>
                                <li class="nav-item mx-1">
                                    <a href="#default-tab-1" data-bs-toggle="tab" class="btn btn-sm btn-light rounded-circle p-1" title="More">
                                        <img class="rounded img-fluid" style="width: 24px; height: 24px;" src="{{ asset('style/h_kj6ECZ7Ii.png') }}" alt="">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="modal-footer border-0 p-0">
                        <button type="button" id="PostSubmit2" class="col-12 btn btn-primary rounded-3 fw-bold py-2 fs-6">{{ __('ui.post') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade  bg-dark m-0 p-0" id="showphoto"  aria-labelledby="showphoto" >
  <div class="modal-dialog modal-dialog-scrollable modal-fullscreen  m-0 p-0" id="modalDialog" >
    <div class="modal-content ">  
       
      <div class="modal-body bg-black" id="modalContent">
        جاري التحميل...
      </div>
    </div>
  </div>
</div>


<?php

    ?>
<!-- comment componant  -->
<div class="d-flex m-3 d-none flex-row comment" id="comment_">
    <div class="col-md-1 my-3 p-0 d-flex justify-content-start " >
        <a class="p-0 me-0 " href="javascript:;">
            <img src="" id="comment_img" width="35" height="35" alt="" class="img-fluid rounded-circle p-0 m-0 ">
        </a>
    </div>
    <div id="father_cid_" class=" col-md-10 m-0 p-0">
        <div class="my-0 py-1 me-2 ms-1 bg-gray-200 radius_30 mt-2" >
            <a id="user_link" href="profile/" >
                <h5 class="mb-1 px-3" id="comment_name"></h5>
            </a>
            <p  class="  my-1 px-3" id="comment_text"></p>
        </div>
        <p class="my-0">
            <a href="javascript:;" class="btn btn-sm btn-link text-gray-600 fw-bolder text-decoration-none ms-3  px-0">2 hr</a>
            <a href="javascript:;" class="btn btn-sm btn-link text-gray-600 fw-bolder text-decoration-none mx-1  px-0">Like</a>
            <a href="javascript:;" id="id_raplay_comment_" class="id_raplay_comment_ btn btn-sm btn-link text-gray-600 fw-bolder text-decoration-none mx-1 px-0" >Reply</a>
        </p>
        
    </div>
    <div class="col-md-1 col-1 m-1 p-0 d-flex justify-content-end">
        <a href="#" class="opt_comment d-none btn btn-lg border-0 rounded-pill w-40px h-40px p-0 d-flex align-items-center justify-content-center bg-transparent text-gray-500" data-bs-toggle="dropdown">
            <i class="fa fa-ellipsis-h"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end shadow">
            <a id="comment_opt" class="dropdown-item d-flex align-items-center">
                <i class="fa fa-fw fa-trash fa-lg"></i> 
                <div class="flex-1 ps-1">
                    <div>delete comment</div>
                        <div class="mt-n1 text-gray-500">
                            <small>Add this to  deleted commentd</small>
                        </div>
                </div>
            </a>
        </div>
    </div>
</div>

<div id="replay_id_" class="ps-2 d-flex flex-row m-1 p-1 d-none " >
    <div class="col-md-1 m-0" > 
        <a class="p-0 me-0" href="javascript:;">
            <img id="replay_photo_id_" src="" width="35" height="35" alt="" class="img-fluid rounded-circle p-0 m-0 ">
        </a>
    </div>
    <div class="mx-1 col-md-11 bg-gray-200 radius_30">
        <a id="link_id_" href=""  >
            <h5 id="userdata_fl_" class="mb-1 mx-3"></h5>
        </a>
        <p id="replay_comen_"  class="mx-3 mb-2"></p>
    </div>
    <hr>
</div>

<div id="reply-box_"   style="display: none;" >
        <div class="ps-2 flex-1 m-2">
            <div class="position-relative m-2">
                <textarea id="commen_rplay_" data-user_id="" data-cuser_id="" name="comment" class="form-control rounded-pill ps-3" placeholder="Write a reply..."></textarea>
                <button  id="replay_"type="submit">reply</button>
                <div class="position-absolute end-0 top-0 bottom-0 d-flex align-items-center px-2">
                    <a href="#" class="btn bg-none  shadow-none px-1"><i class="far fa-smile fa-fw fa-lg d-block"></i></a>
                    <a href="#" class="btn bg-none  shadow-none px-1"><i class="fa fa-camera fa-fw fa-lg d-block"></i></a>
                    <a href="#" class="btn bg-none  shadow-none px-1"><i class="fa fa-film fa-fw fa-lg d-block"></i></a>
                    <a href="#" class="btn bg-none  shadow-none px-1"><i class="far fa-sticky-note fa-fw fa-lg d-block"></i></a>
                </div>
            </div>
        </div>
</div>

<div id="reaction-modal" class="reaction-modal d-none" role="dialog" aria-modal="true" aria-labelledby="reaction-modal-title">
    <div class="reaction-modal-card">
        <div class="reaction-modal-header">
            <strong id="reaction-modal-title">{{ __('ui.reactions') }}</strong>
            <button type="button" id="reaction-modal-close" aria-label="Close">×</button>
        </div>
        <div id="reaction-modal-tabs" class="reaction-modal-tabs"></div>
        <div id="reaction-modal-list" class="reaction-modal-list"></div>
    </div>
</div>
