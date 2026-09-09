@extends('layout.masterhome')
@section('content')
    <!-- About Section-->
<div class="row m-0 p-0 home-feed-layout">
    <div class="col-md-3 col-0 m-0 p-0 home-left-rail" >
       @include('layout.left_sb')
    </div>
    <div class="col-md-6 col-12 m-0 p-0 home-feed-column" >
        <div id="content" class="mt-3 m-0 p-0">
            <div class="row m-0 p-0">
                <div class="col-md-12 m-0 p-0">
                    <div class="row w-100 m-0 p-0">
                        @include('partials.stories_tray')
                        <div class="row w-100 m-0 p-0">
                            <div class="card bg-white  m-0 shadow-sm" style="border-radius: 15px;">
                            <!-- #modal-dialog -->
                                <form action="/posts" id="myForm"  method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="visibility" class="post-visibility-input" value="{{ auth()->check() ? (auth()->user()->default_post_visibility ?? 'public') : 'public' }}">
                                    <div class="row  p-2" >
                                        <div class="col-1 p-0 m-0 " >
                                            @if(isset($profile) && isset($profile['photopro']))
                                            <img class="rounded-circle " src=" {{ asset($profile['photopro']->path.$profile['profile_photo_id'].$profile['photopro']->type) }}  " height="40" width="40" alt="" onerror="this.onerror=null;this.src='{{ asset('img/Default_avatar_profile.jpg') }}';"/>
                                            @else
                                            <img class="rounded-circle" src=" {{ asset('img/Default_avatar_profile.jpg') }}  " height="40" width="40" alt=""/>
                                            @endif
                                        </div>
                                        <div class="col-8  m-0 " >
                                            <a href="#modal-dialog" class="text-decoration-none " data-bs-toggle="modal">
                                                <input type="text" class="form-control rounded-pill bgr text-decoration-none inpotbox text-start border-0 shadow-sm mx-1" name="post_text" placeholder="{{ __('ui.whats_on_your_mind') }}" >
                                            </a>
                                        </div>
                                        <div class="col-3 " >
                                            <div class="row justify-content-end  border-0 text-decoration-none">
                                                <button type="button" class="btn btn-0  rounded  border-0 col-4 ">
                                                        <a href="#" class="text-decoration-none text-muted p-0 m-0" >
                                                            <img src="style/c0dWho49-X3.png" width="23" height="23"  >
                                                        </a>
                                                </button>
                                                <button type="button" class="btn btn-0  rounded  border-0 col-4">
                                                        <a class="text-decoration-none text-muted p-0 m-0" href="#">
                                                            <img src="style/Ivw7nhRtXyo.png" width="23" height="23" >
                                                            <input type="file" id="fileInput2" name="files[]" class="card-body opacity-0" accept="image/*,video/*,.mkv,.avi,.mov,.webm,.mp4,.mpeg,.3gp" style="display:none;" multiple>
                                                        </a>
                                                </button>
                                                <button type="button" class="btn btn-0  rounded  border-0 col-4">
                                                    <a class="text-decoration-none text-muted p-0 m-0" href="#">
                                                        <img src="style/Y4mYLVOhTwq.png" width="23" height="23" >

                                                    </a>
                                                </button>
                                            </div>
                                        </div>
                                    </div>


                                    @error('post_content')
                                    <div>{{ $message }}</div>
                                    @enderror
                                </form>
                            </div>
                        </div>
                        <div class="row p-0 m-0 ">
                            <div id="allpost" class="m-0 p-0">
                                    {{-- {{ $posts->links() }} --}}
                                    {{-- @dd($posts) --}}
                                    @foreach ($posts as $post)
                                <div class="m-0 p-0 postes post-card-container-{{ $post->id }}">
                                    <div class="col mt-3">
                                        <div class="card text-dark bg-white rounded">
                                            <!-- BEGIN timeline-header -->
                                            <div class="card-header border-0 mt-1 bg-white">
                                                <div class="row justify-content-between" >
                                                    <div class="col-md-1" >
                                                        <div class="widget-icon rounded-circle bgr text-white">
                                                            @if(isset($post->user['photopro']['path']))
                                                                <img src="{{asset($post->user['photopro']['path'].$post->user['photopro']['id'].$post->user['photopro']['type'])}}" class="rounded-circle" width="40" height="40" alt="" onerror="this.onerror=null;this.src='{{ asset('img/Default_avatar_profile.jpg') }}';">
                                                            @else
                                                                <img src="{{ asset('img/Default_avatar_profile.jpg') }}" class="rounded-circle" width="40" height="40" alt="">
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mx-2 text-start" >
                                                        <div class="row justify-content-start text-start fs-15px fw-bolder ">
                                                            <a class="post-author text-dark text-decoration-none my-0" href="profile/{{$post->user['id']}}">{{$post->user['first_name']}} {{$post->user['last_name']}}<i class="fa fa-check-circle text-blue ms-1"></i></a>
                                                            <div class="text-muted ">
                                                            <a class="post-time text-muted text-decoration-none" href="post/{{$post->id}}"><time class="js-relative-time" datetime="{{ $post->created_at?->toIso8601String() }}">{{ $post->created_at?->diffForHumans() ?? 'just now' }}</time> <i class="fa {{ $post->visibility === 'only_me' ? 'fa-lock' : ($post->visibility === 'friends' ? 'fa-user-friends' : 'fa-globe-americas') }} opacity-5 ms-1 post-visibility-icon-{{ $post->id }}" title="{{ ucfirst(str_replace('_', ' ', $post->visibility ?? 'public')) }}"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md offset-md-2 text-end" >
                                                    <div class=" row justify-content-end text-end">
                                                        <a href="#" class="btn btn-lg border-0 rounded-pill w-40px h-40px p-0 d-flex align-items-center justify-content-center bg-transparent text-gray-500" data-bs-toggle="dropdown">
                                                            <i class="fa fa-ellipsis-h"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end shadow">
                                                            <a href="#" class="dropdown-item d-flex align-items-center save-post" data-post-id="{{$post->id}}">
                                                                <i class="fa fa-fw fa-bookmark fa-lg"></i>
                                                                <div class="flex-1 ps-1">
                                                                    <div>{{ __('ui.save_post') }}</div>
                                                                    <div class="mt-n1 text-gray-500">
                                                                        <small>Add this to your saved items</small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            @if(Auth::check() && $post->user['id']==Auth::id())
                                                                <a href="javascript:;" class="dropdown-item btn-edit-post" data-post-id="{{ $post->id }}" data-post-text="{{ $post->post_text }}" data-visibility="{{ $post->visibility ?? 'public' }}"><i class="fa fa-fw fa-edit fa-lg me-1"></i> {{ app()->getLocale() === 'ar' ? 'تعديل المنشور' : 'Edit post' }}</a>
                                                                <div class="dropdown-divider"></div>
                                                                <a href="javascript:;" class="dropdown-item text-danger btn-delete-post" data-post-id="{{ $post->id }}"><i class="fa fa-fw fa-trash-alt fa-lg me-1"></i> {{ app()->getLocale() === 'ar' ? 'حذف المنشور' : 'Delete post' }}</a>
                                                            @else
                                                                <a href="#" class="dropdown-item"><i class="fa fa-fw fa-bell fa-lg me-1"></i> Turn off notifications for this post</a>
                                                                <div class="dropdown-divider"></div>
                                                                <a href="javascript:;" class="dropdown-item text-danger btn-block-user" data-user-id="{{ $post->user['id'] }}" data-user-name="{{ $post->user['first_name'] }} {{ $post->user['last_name'] }}">
                                                                    <i class="fa fa-fw fa-ban fa-lg me-1"></i> حظر {{ $post->user['first_name'] }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END timeline-header -->
                                            <!-- BEGIN timeline-body -->
                                            <div class="card-body rounded bg-white p-0 m-0">

                                                <!-- timeline-post -->
                                                <div class="mb-3">
                                                    <div class="m-2 post-text-content-{{ $post->id }}">
                                                            {!! \App\Support\HashtagFormatter::linkify($post->post_text) !!}
                                                    </div>
                                                    @if($post->shared_post_id && $post->sharedPost)
                                                        <div class="shared-post-card border rounded m-2 p-2 bg-light">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <img src="{{ $post->sharedPost->user?->avatar_url ?? asset('img/Default_avatar_profile.jpg') }}" class="rounded-circle me-2" width="35" height="35" alt="">
                                                                <div>
                                                                    <a href="{{ url('profile/' . $post->sharedPost->user_id) }}" class="fw-bold text-dark text-decoration-none">
                                                                        {{ $post->sharedPost->user?->first_name }} {{ $post->sharedPost->user?->last_name }}
                                                                    </a>
                                                                    <div class="text-muted small">
                                                                        {{ $post->sharedPost->created_at?->diffForHumans() }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="shared-post-text mb-2 text-dark">
                                                                {!! \App\Support\HashtagFormatter::linkify($post->sharedPost->post_text) !!}
                                                            </div>
                                                            @php
                                                                $s_images = $imges[$post->sharedPost->id] ?? [];
                                                                $s_videos = $videos[$post->sharedPost->id] ?? [];
                                                            @endphp
                                                            @if(!empty($s_images))
                                                                <div class="row gx-1 p-0 m-0">
                                                                    @foreach($s_images as $sImgGroup)
                                                                        @foreach($sImgGroup as $sImg)
                                                                            <div class="col-12 mb-1">
                                                                                <img src="{{ asset($sImg->path . '/' . $sImg->id . $sImg->type) }}" class="img-fluid rounded w-100" alt="">
                                                                            </div>
                                                                        @endforeach
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                            @if(!empty($s_videos))
                                                                @foreach($s_videos as $sVidGroup)
                                                                    @foreach($sVidGroup as $sVid)
                                                                        <div class="col-12 mb-1">
                                                                            <video class="w-100 rounded" controls preload="metadata">
                                                                                <source src="{{ asset($sVid->path . $sVid->id . '/playlist.m3u8') }}" type="application/x-mpegURL">
                                                                            </video>
                                                                        </div>
                                                                    @endforeach
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @php
                                                        $i_videos = $videos[$post->id] ?? [];
                                                            $i_images = $imges[$post->id] ?? [];
                                                            $i_allMedia = array_merge($i_videos, $i_images);
                                                            $i_totalItems = count($i_allMedia);
                                                            $i_maxDisplay = min($i_totalItems, 4);
                                                            $i_numVideos = count($i_videos);
                                                            $col_1=null;
                                                            $col_2=null;
                                                            if($i_totalItems==1){
                                                                $col_1=12;
                                                                $col_2=0;

                                                            }elseif($i_totalItems==2){
                                                                $col_1=6;
                                                                $col_2=6;

                                                            }elseif($i_totalItems>=3){
                                                                $col_1=9;
                                                                $col_2=3;

                                                            }
                                                    @endphp
                                                    <div class="row gx-1 container p-0 m-0">
                                                        @for ($r=0;$r<$i_maxDisplay;$r++ )
                                                                @if($r==2)
                                                                    @break
                                                                @endif
                                                            <div class="col-md-{{ ($r==0 )?$col_1:$col_2 }} p-0 m-0">
                                                                @if($i_numVideos > 0)

                                                                    <div class="ratio ratio-1x1  p-0 m-0">
                                                                        <a href="javascript:;" class="bg-size-cover bg-position-center" >
                                                                            <video class="video-js vjs-default-skin p-0 VDlol" id="video_{{$i_videos[$r][0]->id }}"
                                                                                controls
                                                                                preload="auto"
                                                                                poster="{{ $i_videos[$r][0]->thumbnail_path ? asset($i_videos[$r][0]->thumbnail_path) : '' }}"
                                                                                data-setup='{}' >
                                                                                <source src="{{ asset($i_videos[$r][0]->path . $i_videos[$r][0]->id . '/playlist.m3u8') }}" type="application/x-mpegURL">
                                                                            </video>
                                                                        </a>
                                                                    </div>

                                                                    @if($col_2==3 && $r>0)
                                                                    @for ($r2=2;$r2<$i_maxDisplay;$r2++ )
                                                                    <div class="ratio ratio-1x1  p-0 m-0">
                                                                        <a href="javascript:;" class="bg-size-cover bg-position-center" >
                                                                            <video class="video-js vjs-default-skin p-0 VDlol" id="video_{{ $i_videos[$r2][0]->id }}"
                                                                                controls
                                                                                preload="auto"
                                                                                poster="{{ $i_videos[$r2][0]->thumbnail_path ? asset($i_videos[$r2][0]->thumbnail_path) : '' }}"
                                                                                data-setup='{}' >
                                                                                <source src="{{ asset($i_videos[$r2][0]->path . $i_videos[$r2][0]->id . '/playlist.m3u8') }}" type="application/x-mpegURL">
                                                                            </video>
                                                                        </a>
                                                                    </div>
                                                                    @endfor
                                                                    @endif
                                                                @else
                                                                    <div class="ratio ratio-1x1 p-0 m-0">
                                                                        <a href="javascript:;" onclick="showphoto({{$i_images[$r][0]->id }})" data-lity class="bg-size-cover bg-position-center" style="background-image: url({{ asset($i_images[$r][0]->path . '/' . $i_images[$r][0]->id . $i_images[$r][0]->type) }})"></a>
                                                                    </div>
                                                                    @if($col_2==3 && $r>0)
                                                                    @for ($r2=2;$r2<$i_maxDisplay;$r2++ )
                                                                    <div class="ratio ratio-1x1  p-0 m-0">
                                                                        <a href="javascript:;" onclick="showphoto({{$i_images[$r2][0]->id}})" data-lity class="bg-size-cover bg-position-center" style="background-image: url({{ asset($i_images[$r2][0]->path . '/' . $i_images[$r2][0]->id . $i_images[$r2][0]->type) }})"></a>

                                                                    </div>
                                                                    @endfor
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        @endfor


                                                    </div>
                                                </div>

                                                <!-- timeline-stats -->
                                                @include('partials.post-engagement-summary', ['post' => $post])
                                                <!-- timeline-action -->
                                                <hr class="my-10px">
                                                <div class="d-flex align-items-center fw-bold">
                                                        @php $like = false; @endphp
                                                        @foreach($post['react'] as $react)
                                                        @if(Auth::check() && ($react->user_id==Auth::id()) && ($post->id ==$react->post_id))
                                                    @php $like=   true;   @endphp
                                                    @break
                                                        @endif
                                                        @endforeach
                                                    <a href="javascript:;"
                                                        class="flex-fill text-decoration-none text-center text-gray-400 like"
                                                        data-liked="{{$like}}"
                                                        data-type_id='1'
                                                        data-post_id='{{$post->id}}'
                                                        data-user-id='@if(Auth::check()) {{ Auth::user()->id}} @endif ' >
                            <button  id="like"
                                                            class="btn btn-link text-{{$like?'blue' : 'gray'}}-400 text-decoration-none"  >
                            <i class="fa fa-thumbs-up fa-fw me-3px"></i> {{ __('ui.like') }}</button>
                            <span class="reaction-picker" role="group" aria-label="Reaction type">
                                @foreach([1=>'👍',2=>'❤️',3=>'🤗',4=>'😂',5=>'😮',6=>'😢',7=>'😡'] as $reactionId => $reactionEmoji)
                                    <button type="button" class="reaction-option" data-type="{{$reactionId}}">{{$reactionEmoji}}</button>
                                @endforeach
                            </span>
                                                    </a>
                                                    <a href="javascript:;" class="flex-fill text-decoration-none text-center text-gray-400">
                                                        <i class="fa fa-comments fa-fw me-3px"></i> {{ __('ui.comment') }}
                                                    </a>
                                                    <a href="javascript:;" class="flex-fill text-decoration-none text-center text-gray-400 btn-share-post"
                                                       data-post-id="{{$post->id}}"
                                                       data-author="{{$post->user['first_name']}} {{$post->user['last_name']}}"
                                                       data-snippet="{{ Str::limit($post->post_text, 80) }}">
                                                        <i class="fa fa-share fa-fw me-3px"></i> {{ __('ui.share') }}
                                                    </a>
                                                </div>
                                                <hr class="mt-10px mb-3">
                                                <div id="comment_block_{{$post->id}}">
                                                    @if(count($post->commentes)>0)
                                                    @foreach($post->commentes as $comment)
                                                    <div class="d-flex m-3 flex-row comment" id="comment_{{$post->id}}_{{$comment->id}}">
                                                    <div class="col-md-1 col-1 my-3 p-0 d-flex justify-content-start " >
                                                        <a class="p-0 me-0 " href="javascript:;">
                                                            @php $commentPhoto = $comment->user?->photopro; @endphp
                                                            <img id="comment_img_{{$comment->id}}" src="{{ $commentPhoto ? asset($commentPhoto->path.$commentPhoto->id.$commentPhoto->type) : asset('img/Default_avatar_profile.jpg') }}" width="35" height="35" alt="" class="img-fluid rounded-circle p-0 m-0" onerror="this.onerror=null;this.src='{{ asset('img/Default_avatar_profile.jpg') }}';">
                                                        </a>
                                                    </div>
                                                    <div id="father_cid_{{ $comment->id }}" class=" col-md-10 col-10 m-0 p-0">
                                                        <div class="my-0 py-1 me-2 ms-1 bg-gray-200 radius_30 mt-2" >
                                                            <a id="user_link_{{$comment->user['id']}}" href="profile/{{$comment->user['id']}}" >
                                                                <h5 class="mb-1 px-3" id="comment_name_{{$comment->id}}">{{$comment->user['first_name']}} {{$comment->user['last_name']}}</h5>
                                                            </a>
                                                            <p class="my-1 px-3" id="comment_text_{{$comment->id}}">{{$comment['text_co']}}</p>
                                                            @if($comment->media_path)
                                                                @if($comment->media_type === 'video')<video class="comment-media video-js vjs-default-skin" controls preload="metadata" data-setup='{}'><source src="{{ asset($comment->media_path) }}" @if(str_ends_with($comment->media_path, '.m3u8')) type="application/x-mpegURL" @endif></video>@else<img class="comment-media" src="{{ asset($comment->media_path) }}" alt="">@endif
                                                            @endif
                                                        </div>
                                                        <p class="my-0">
                                                            <time class="comment-time js-relative-time" datetime="{{ $comment->created_at?->toIso8601String() }}">{{ $comment->created_at?->diffForHumans(short: true) ?? 'now' }}</time>
                                                                @php $like_comment = false; @endphp
                                                                    @foreach($comment['react'] as $react)
                                                                    @if(Auth::check() && ($react->user_id==Auth::id()) && ($comment->id ==$react->comment_id))
                                                                @php $like_comment=   true;   @endphp
                                                                @break
                                                                @endif
                                                                @endforeach
                                                            <a href="javascript:;" data-comment_liked="{{$like_comment}}"  data-type_id='1' data-comment_id='{{$comment->id}}' data-user-id='@if(Auth::check()) {{ Auth::user()->id}} @endif ' data-like_comment="{{$like_comment}}" class="btn btn-sm btn-link like_comment text-{{$like_comment?'blue' : 'gray'}}-600 fw-bolder text-decoration-none mx-1  px-0">Like</a>
                                                            <a href="javascript:;" data-id_raplay_comment_="{{ $comment->id }}" id="id_raplay_comment_{{ $comment->id }}" class="id_raplay_comment_ btn btn-sm btn-link text-gray-600 fw-bolder text-decoration-none mx-1 px-0" >{{ __('ui.reply') }}</a>
                                                        </p>
                                                            <div id="reply-box_{{$comment->id}}" style="display: none;">
                                                                    <div class="ps-2 flex-1 m-2">
                                                                        <div class="position-relative m-2">
                                                                            <textarea id="commen_rplay_{{$comment->id}}" data-user_id="@if(Auth::check()) {{ Auth::user()->id??0}} @endif " data-cuser_id="{{ $comment->user['id'] }}"  name="comment" class="form-control rounded-pill ps-3" placeholder="Write a reply...">{{ $comment->user['first_name'] }} {{ $comment->user['last_name'] }}</textarea>

                                                                            <button id="replay_{{$comment->id}}" class="reply-send" type="submit" aria-label="{{ __('ui.reply') }}"><i class="fa fa-paper-plane"></i></button>
                                                                                <div class="position-absolute end-0 top-0 bottom-0 d-flex align-items-center px-2">
                                                                                    <a href="#" class="btn bg-none  shadow-none px-1"><i class="far fa-smile fa-fw fa-lg d-block"></i></a>
                                                                                    <a href="#" class="btn bg-none  shadow-none px-1"><i class="fa fa-camera fa-fw fa-lg d-block"></i></a>
                                                                                    <a href="#" class="btn bg-none  shadow-none px-1"><i class="fa fa-film fa-fw fa-lg d-block"></i></a>
                                                                                    <a href="#" class="btn bg-none  shadow-none px-1"><i class="far fa-sticky-note fa-fw fa-lg d-block"></i></a>
                                                                                </div>
                                                                        </div>
                                                                    </div>

                                                            </div>

                                                        <div id="rid_{{ $comment->id }}">

                                                                @foreach ($comment['replie'] as $item)
                                                                <div  id="replay_id_{{ $item->id }}"class="ps-2 d-flex flex-row m-1 p-1 ">
                                                                    <div class="col-md-1 m-0" >
                                                                        <a class="p-0 me-0 " href="javascript:;">
                                                                            @php $replyPhoto = $item->userreply?->photopro; @endphp
                                                                            <img id="replay_photo_id_{{ $item->id  }}" src="{{ $replyPhoto ? asset($replyPhoto->path.$replyPhoto->id.$replyPhoto->type) : asset('img/Default_avatar_profile.jpg') }}" width="35" height="35" alt="avatar" class="img-fluid rounded-circle p-0 m-0 ">
                                                                        </a>
                                                                    </div>
                                                                    <div class="mx-1 col-md-11 reply-content">
                                                                        <div class="reply-bubble">
                                                                        <a id="link_id_{{ $item->id  }}" href="/profile/{{$item->userreply['id']}}"  >
                                                                            <h5 id="userdata_fl_{{ $item->id  }}" class="mb-1 mx-3">{{$item->userreply['first_name']}} {{$item->userreply['last_name']}}</h5></a>
                                                                        <p id="replay_comen_{{ $item->id }}" class="mx-3 mb-1">{{$item->reply}}.</p>
                                                                        @if($item->media_path)
                                                                            @if($item->media_type === 'video')<video class="comment-media video-js vjs-default-skin" controls preload="metadata" data-setup='{}'><source src="{{ asset($item->media_path) }}" @if(str_ends_with($item->media_path, '.m3u8')) type="application/x-mpegURL" @endif></video>@else<img class="comment-media" src="{{ asset($item->media_path) }}" alt="">@endif
                                                                        @endif
                                                                        </div>
                                                                        <time class="reply-time js-relative-time" datetime="{{ $item->created_at?->toIso8601String() }}">{{ $item->created_at?->diffForHumans(short: true) ?? 'now' }}</time>
                                                                    </div>
                                                                    <hr>
                                                                    </div>
                                                                    @endforeach
                                                            </div>


                                                    </div>
                                                    <div class="col-md-1 col-1 m-1 p-0 d-flex justify-content-end">
                                                    @if(isset(Auth::user()->id) && Auth::user()->id==$comment->user['id'])
                                                        <a href="javascript:;" class="opt_comment d-none btn btn-lg border-0 rounded-pill w-40px h-40px p-0 d-flex align-items-center justify-content-center bg-transparent text-gray-500" data-bs-toggle="dropdown">
                                                            <i class="fa fa-ellipsis-h"></i>
                                                        </a>
                                                        @endif
                                                        <div class="dropdown-menu dropdown-menu-end shadow">
                                                            <a id="comment_opt_{{$comment->id}}" href="javascript:;" onclick="Delete('comment','{{$comment->id}}')" class="dropdown-item d-flex align-items-center">
                                                                <i class="fa fa-fw fa-trash fa-lg"></i>
                                                                <div class="flex-1 ps-1">
                                                                    <div>delete comment</div>
                                                                        <div class="mt-n1 text-gray-500">
                                                                            <small>Add this to  delete comment</small>
                                                                        </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                                @endif
                                                </div>
                                        <!-- timeline-input -->
                                            <div class="row m-1 rounded" >

                                                    <div class="col-md-1 my-3  d-flex align-items-center">
                                                        @if(isset($profile['photopro']->path))
                                                        <div class="d-flex align-items-center " >
                                                            <a class="radius_30 d-flex align-items-center text-center justify-content-center" href="javascript:;">
                                                                <img  src="{{ asset($profile['photopro']->path.$profile['profile_photo_id'].$profile['photopro']->type)}}" width="30" height="30"  class="rounded-pill">
                                                            </a>
                                                        </div>
                                                        @else
                                                        <div>
                                                            <a class="w-30px" href="javascript:;">
                                                                <img src="{{ asset('img/Default_avatar_profile.jpg') }}" width="35" height="35" class="rounded-pill" alt="avatar">
                                                            </a>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-11 ps-2 flex-1">
                                                        <div class="position-relative">
                                                            <input type="text" data-post_id="{{$post->id}}" name="comment" class="form-control comment rounded-pill ps-3 py-2 fs-13px" placeholder="{{ __('ui.write_comment') }}">
                                                            <div class="position-absolute end-0 top-0 bottom-0 d-flex align-items-center px-2">
                                                                <a href="#" class="btn bg-none  shadow-none px-1"><i class="far fa-smile fa-fw fa-lg d-block"></i></a>
                                                                <a href="#" class="btn bg-none  shadow-none px-1"><i class="fa fa-camera fa-fw fa-lg d-block"></i></a>
                                                                <a href="#" class="btn bg-none  shadow-none px-1"><i class="fa fa-film fa-fw fa-lg d-block"></i></a>
                                                                <a href="#" class="btn bg-none  shadow-none px-1"><i class="far fa-sticky-note fa-fw fa-lg d-block"></i></a>
                                                            </div>
                                                        </div>

                                                    </div>
                                            </div>
                                        </div>
                                            <!-- END timeline-body -->
                                    </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
    <div class="col-md-3 d-none d-md-block home-right-rail">
    @include('layout.right_sb')
    </div>

</div>













@include('partials.share_modal')
@include('partials.edit_post_modal')
@endsection
