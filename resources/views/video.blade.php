
@extends('layout.backblank')
@section('content')
<div class="row">
    <div class=" d-flex justify-content-start align-items-start ">
        <a href="{{asset('/')}}" style="width: 55px;height:55px;" class="px-0 ms-4 ">
            <svg viewBox="0 0 48 48" style="width: 55px;height:55px;" >
                <path style="filter: invert(39%) sepia(57%) saturate(200%) saturate(200%) saturate(200%) saturate(200%) saturate(200%) saturate(147.75%) hue-rotate(202deg) brightness(97%) contrast(96%);" d="M20.181 35.87C29.094 34.791 36 27.202 36 18c0-9.941-8.059-18-18-18S0 8.059 0 18c0 8.442 5.811 15.526 13.652 17.471L14 34h5.5l.681 1.87Z"></path>
                <path fill="#fff" d="M13.651 35.471v-11.97H9.936V18h3.715v-2.37c0-6.127 2.772-8.964 8.784-8.964 1.138 0 3.103.223 3.91.446v4.983c-.425-.043-1.167-.065-2.081-.065-2.952 0-4.09 1.116-4.09 4.025V18h5.883l-1.008 5.5h-4.867v12.37a18.183 18.183 0 0 1-6.53-.399Z"></path>
            </svg>
        </a>
        <button type="button" class="btn p-0 " data-bs-dismiss="modal" aria-label="إغلاق">
            <div class="widget-icon rounded-circle bg-gray d-flex align-items-center justify-content-center  ms-0  " style="width: 43px;height:43px;">
                <i class="fa-solid fa-x fs-4 " ></i>
            </div>
        </button>
    </div>
</div> 
<div class="d-flex justify-content-center ">
    <div class="col-md-8 m-0 p-0 ">
            <div class="row border-0 m-0 p-0 h-100 w-100">
                <video class="video-js vjs-default-skin  VDlol " id="video_{{ $video->id }}" 
                    controls 
                    preload="auto" 
                    poster="path_to_image.jpg" 
                    data-setup='{}'
                    
                    >
                    <source src="{{ asset($video->path . $video->id . '/playlist.m3u8') }}">
                </video>
            </div>
        </div>
    <div class="col-md-4 m-0 p-0  ">
        <div class="card text-dark bg-white  shadow-sm border-0 ">
            <!-- BEGIN timeline-header -->
            <div class="card-header bg-white ">
                <div class="row justify-content-between" >
                    <div class="col-md-1 ">
                        <div class="widget-icon rounded-circle bgr  text-white">
                            @if(isset($video->user['photopro']['path']))
                                <img src="{{asset($video->user['photopro']['path'].$video->user['photopro']['id'].$video->user['photopro']['type'])}}" class="rounded-circle" width="40" height="40" alt="">
                            @else
                                <img src="assets/img/user/user-1.jpg" alt="">    
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 mx-3   text-start">
                        <div class="row justify-content-start text-start fs-15px fw-bolder ">
                            <a class="text-dark text-decoration-none my-0" href="profile/{{$video->user['id']}}">
                            {{$video->user['first_name']}}  {{$video->user['last_name']}}
                            <i class="fa fa-check-circle text-blue ms-1"></i></a>
                            <div class="text-muted ">
                            <a class="text-muted   text-decoration-none" href="video/{{$video->id}}"> 8 mins <i class="fa fa-globe-americas opacity-5 ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md offset-md-2 text-end">
                            <a href="#" class="btn btn-lg border-0 rounded-pill w-40px h-40px p-0 d-flex align-items-center justify-content-center bg-transparent text-gray-500" data-bs-toggle="dropdown">
                                <i class="fa fa-ellipsis-h"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#" class="dropdown-item d-flex align-items-center">
                                    <i class="fa fa-fw fa-bookmark fa-lg"></i> 
                                    <div class="flex-1 ps-1">
                                        <div>Save Post</div>
                                        <div class="mt-n1 text-gray-500"><small>Add this to your saved items</small></div>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                                @if(request()->photowner)
                                <a href="#" class="dropdown-item"><i class="fa fa-fw fa-edit fa-lg me-1"></i> Edit video</a>
                                <a href="#modal-message" data-bs-toggle="modal" class="dropdown-item make-profile-picture" data-user-id="{{$video->user['id']}}" data-video-id="{{$video->id}}"><i class="fa fa-fw fa-user fa-lg me-1"></i> Make profile picture</a>
                                <a href="#modal-messagec" data-bs-toggle="modal" class="dropdown-item make-profile-cover" data-user-id="{{$video->user['id']}}" data-video-id="{{$video->id}}"><i class="fa fa-fw fa-bell fa-lg me-1"></i> Make profile Cover</a>
                                <a href="#" class="dropdown-item"><i class="fa fa-fw fa-bell fa-lg me-1"></i> Turn off notifications for this post</a>
                                <a href="#" class="dropdown-item"><i class="fa fa-fw fa-language fa-lg me-1"></i> Turn off translations</a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item"><i class="fa fa-fw fa-archive fa-lg me-1"></i> Move to archive</a>
                                <a href="#" class="dropdown-item"><i class="fa fa-fw fa-trash-alt fa-lg me-1"></i> Move to Recycle bin</a>
                                @endif
                            </div>
                        </div>
                </div>    
            </div>
            <!-- END timeline-header -->
        
            <!-- BEGIN timeline-body -->
            <div class="card-body bg-white  m-0 p-0">
                <!-- timeline-post -->
                
        
                <!-- timeline-stats -->
                <div class="d-flex align-items-center  mb-2">
                    <div class="d-flex align-items-center">
                        <span class="fa-stack fs-10px">
                            <i class="fa fa-circle fa-stack-2x text-danger"></i>
                            <i class="fa fa-heart fa-stack-1x fa-inverse fs-11px"></i>
                        </span>
                        <span class="fa-stack fs-10px">
                            <i class="fa fa-circle fa-stack-2x text-blue"></i>
                            <i class="fa fa-thumbs-up fa-stack-1x fa-inverse fs-11px bottom-0 mb-1px"></i>
                        </span>
                        <span class="ms-1">4.3k</span>
                    </div>
                    <div class="d-flex align-items-center ms-auto">
                        <div>259 Shares</div>
                        <div class="ms-3">21 Comments</div>
                    </div>
                </div>
        
                <!-- timeline-action -->
                <hr class="my-10px">
                <div class="d-flex align-items-center fw-bold"> 
                        @php $like = false; @endphp
                        @foreach($video['videoreact'] as $react) 
                        @if(Auth::check() && ($react->user_id==Auth::id()) && ($video->id ==$react->video_id))
                    @php $like=   true;   @endphp
                    @break 
                        @endif
                        @endforeach
                    <a href="javascript:;"
                        class="flex-fill text-decoration-none text-center text-gray-400 like"
                        data-liked="{{$like}}" 
                        data-type_id='1' 
                        data-video_id='{{$video->id}}' 
                        data-user-id='@if(Auth::check()) {{ Auth::user()->id}} @endif ' >
                        <button  id="like" 
                        class="btn btn-link text-{{$like?'blue' : 'gray'}}-400 text-decoration-none"  >
                        <i class="fa fa-thumbs-up fa-fw me-3px"></i> Like</button>
                    </a>
                    <a href="javascript:;" class="flex-fill text-decoration-none text-center text-gray-400">
                        <i class="fa fa-comments fa-fw me-3px"></i> Comment
                    </a> 
                    <a href="javascript:;" class="flex-fill text-decoration-none text-center text-gray-400">
                        <i class="fa fa-share fa-fw me-3px"></i> Share
                    </a>
                </div>
                <hr class="mt-10px mb-3">
                <div id="comment_photo_block_{{$video->id}}">
                    @if(count($video->videocommentes)>0)
                    @foreach($video->videocommentes as $comment)
                <div class="d-flex m-3 flex-row comment" id="videocomment_{{$video->id}}_{{$comment->id}}">
                    <div class="col-md-1 col-1 my-3 p-0 d-flex justify-content-start " >
                        <a class="p-0 me-0 " href="javascript:;">
                            <img id="videocomment_img_{{$comment->id}}" src="{{asset($comment['user']['photopro']['path'].$comment['user']['photopro']['id'].$comment['user']['photopro']['type'])}}" width="35" height="35" alt="" class="img-fluid rounded-circle p-0 m-0 ">
                        </a>
                    </div>
                    <div class="col-md-10 col-10 m-0 p-0">
                        <div class="my-0 py-1 me-2 ms-1 bg-gray-200 radius_30 mt-2" >
                            <a id="user_link_{{$comment->user['id']}}" href="profile/{{$comment->user['id']}}" >
                                <h5 class="mb-1 px-3" id="videocomment_name_{{$comment->id}}">{{$comment->user['first_name']}} {{$comment->user['last_name']}}</h5>
                            </a>
                            <p  class="  my-1 px-3" id="videocomment_text_{{$comment->id}}" >{{$comment['comment']}}.</p>
                        </div>
                        <p class="my-0">
                            <a href="javascript:;" class="btn btn-sm btn-link text-gray-600 fw-bolder text-decoration-none ms-3  px-0">2 hr</a>
                            <a href="javascript:;" class="btn btn-sm btn-link text-gray-600 fw-bolder text-decoration-none mx-1  px-0">Like</a>
                            <a href="javascript:;" class="btn btn-sm btn-link text-gray-600 fw-bolder text-decoration-none mx-1 px-0" onclick="ReplyBox()">Reply</a>
                            </p>
                            <div class="reply-box" style="display: none;">
                                <form action="/post-reply" method="POST">
                                    @csrf
                                    <div class="ps-2 flex-1 m-2">
                                        <div class="position-relative m-2">
                                            <textarea name="comment" class="form-control rounded-pill ps-3" placeholder="Write a reply...">{{ $comment->user['first_name'] }} {{ $comment->user['last_name'] }}</textarea>
                                            <input type="hidden" name="comment_id" value="{{ $comment->id }}">
                                            <input type="hidden" name="userreplay_id" value="{{ $comment->user['id'] }}">
                                            <button type="submit">reply</button>
                                            <div class="position-absolute end-0 top-0 bottom-0 d-flex align-items-center px-2">
                                                <a href="#" class="btn bg-none  shadow-none px-1"><i class="far fa-smile fa-fw fa-lg d-block"></i></a>
                                                <a href="#" class="btn bg-none  shadow-none px-1"><i class="fa fa-camera fa-fw fa-lg d-block"></i></a>
                                                <a href="#" class="btn bg-none  shadow-none px-1"><i class="fa fa-film fa-fw fa-lg d-block"></i></a>
                                                <a href="#" class="btn bg-none  shadow-none px-1"><i class="far fa-sticky-note fa-fw fa-lg d-block"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div>
                                
                            </div>
                        </div>
                        <div class="col-md-1 col-1 m-1 p-0 d-flex justify-content-end">
                            <a href="#" class="opt_comment d-none btn btn-lg border-0 rounded-pill w-40px h-40px p-0 d-flex align-items-center justify-content-center bg-transparent text-gray-500" data-bs-toggle="dropdown">
                                <i class="fa fa-ellipsis-h"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow">
                                <a id="videocomment_opt_{{$comment->id}}" href="#" onclick="Delete('videocomment','{{$comment->id}}')" class="dropdown-item d-flex align-items-center">
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
                                    <img src="" height="35" class="rounded-pill">
                                </a>
                            </div>
                            @endif
                        </div> 
                        <div class="col-md-11 ps-2 flex-1">
                            <div class="position-relative">
                                <input type="text" data-video_id="{{$video->id}}" name="comment" class="form-control comment rounded-pill ps-3 py-2 fs-13px" placeholder="Write a comment...">
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

    

@endsection('content')
