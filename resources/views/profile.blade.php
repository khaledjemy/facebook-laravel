
@extends('layout.profilemaster')
@section('content')
    <!-- About Section-->

  
    <!-- BEGIN page-cover -->
	<!-- END page-cover -->

 
	<div  class="row b-0 m-0">
			<!-- BEGIN profile -->
			<div class="profile bg-white   ">
                <div class="container w-75 px-0" >
					<div class="profile-header rounded-bottom">
						<!-- BEGIN profile-header-content -->
						<div class="profile-header-content    p-0 rounded rounded-bottom" >
							<div class="row p-0 m-0 p_co rounded-bottom ">
								@if($profile_page->coverpro)
                                <img id="profile-cover-preview" role="button" tabindex="0" data-profile-photo-id="{{ $profile_page->coverpro->id }}" class="p-0 m-0 rounded-bottom profile-cover-image profile-media-view-trigger" src="{{ asset($profile_page->coverpro->path.$profile_page->coverpro->id.$profile_page->coverpro->type) }}" alt="{{ $profile_page->first_name }} cover"/>
                                @else
                                <div id="profile-cover-preview" class="profile-cover-image profile-cover-fallback"></div>
                                @endif
								@if(Auth::id() === $profile_page->id)
								<label class="profile-photo-edit profile-cover-edit" title="{{ __('ui.change_cover') }}">
									<i class="fa fa-camera"></i> <span>{{ __('ui.change_cover') }}</span>
									<input type="file" class="profile-media-input" data-cover="1" accept="image/jpeg,image/png,image/webp" hidden>
								</label>
								@endif
							</div>
						</div>
						<!-- END profile-header-content -->
					</div>
                <div class="   container" >
                    <div class="row">
                        <div class="col-md-3">
                    	<!-- BEGIN profile-header-img -->
							<div class="profile-header-img rounded-circle position-sticky mt-n5">
								@if($profile_page->photopro)
                                <img id="profile-avatar-preview" role="button" tabindex="0" data-profile-photo-id="{{ $profile_page->photopro->id }}" class="img-thumbnail rounded-circle profile-avatar-image profile-media-view-trigger" src="{{ asset($profile_page->photopro->path.$profile_page->photopro->id.$profile_page->photopro->type) }}" alt="{{ $profile_page->first_name }}"/>
                                @else
                                <img id="profile-avatar-preview" class="img-thumbnail rounded-circle profile-avatar-image" src="{{ asset('img/Default_avatar_profile.jpg') }}" alt="{{ $profile_page->first_name }}"/>
                                @endif
								@if(Auth::id() === $profile_page->id)
								<label class="profile-photo-edit profile-avatar-edit" title="{{ __('ui.change_profile_photo') }}">
									<i class="fa fa-camera"></i>
									<input type="file" class="profile-media-input" data-cover="0" accept="image/jpeg,image/png,image/webp" hidden>
								</label>
								@endif
							</div>
						<!-- END profile-header-img -->
                        </div>
                        <div class="col-md-4 pt-5">
						<!-- BEGIN profile-header-info -->
						<div class="profile-header-info">
							<h4 class="mt-0 mb-1">{{$profile_page['first_name']}} {{$profile_page['last_name']}}</h4>
						</div>
                        </div>
						<div class="col-md-4 pt-5">
							@if($friends[0]==1)
                <div class="btn-group">
                    <a href="javascript:;" class="btn btn-default dropdown-toggle" data-bs-toggle="dropdown">friend
                    <i class="fa fa-user-group"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <a href="javascript:;" class="dropdown-item">Action 1</a>
                        <a href="javascript:;" class="dropdown-item">Action 2</a>
                        <a href="javascript:;" class="dropdown-item">Action 3</a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:;" class="dropdown-item" >
							<form action="/f_action" method="POST">
							@csrf
							<button name="add" type="submit" class="btn btn-link">
								<input name="user" type="hidden" value="{{$profile_page['id']}}">	
								<input name="type" type="hidden" value="remove">
                    			remove friend
                				</button>
						</form>
					</a>
                    </ul>
                </div>
						@elseif($friends[0]==2)
						<form action="/f_action" method="POST">
							@csrf
							<button name="add" type="submit" class="btn btn-primary">
								<input name="user" type="hidden" value="{{$profile_page['id']}}">	
								<input name="type" type="hidden" value="cancel">
                    			Cancel friend request
                				</button>
						</form>
						@elseif($friends[0]==3)
						<div class="row">
							<div class="col-md-3 m-1">
						<form action="/f_action" method="POST">
							@csrf
							<button name="add" type="submit" class="btn btn-primary">
								<input name="user" type="hidden" value="{{$profile_page['id']}}">	
								<input name="type" type="hidden" value="accept">
								Accept
							</button>
						</form>
							</div>
							<div class="col-md-8 m-1">
						<form action="/f_action" method="POST">
							@csrf
							<button name="add" type="submit" class="btn btn-primary">
								<input name="user" type="hidden" value="{{$profile_page['id']}}">	
								<input name="type" type="hidden" value="cancel_r">
                    			Cancel request
                				</button>
						</form>
							</div>
						</div>
						@elseif($friends[0]==4)
						<form action="/f_action" method="POST">
							@csrf
							<button name="add" type="submit" class="btn btn-primary">
							<input name="user" type="hidden" value="{{$profile_page['id']}}">	
							<input name="type" type="hidden" value="add">
								Add friend
							</button>
						</form>
						@endif
						</div>
                    </div>
						<!-- END profile-header-info -->
                    <hr class='mt-5'>
                        <!-- BEGIN profile-header-tab -->
                            <ul class="profile-header-tab nav nav-tabs bg-white">
                                <li class="nav-item"><a href="#profile-post" class="nav-link active" data-bs-toggle="tab">POSTS</a></li>
                                <li class="nav-item"><a href="#profile-about" class="nav-link" data-bs-toggle="tab">ABOUT</a></li>
                                <li class="nav-item"><a href="#profile-photos" class="nav-link" data-bs-toggle="tab">PHOTOS</a></li>
                                <li class="nav-item"><a href="#profile-videos" class="nav-link" data-bs-toggle="tab">VIDEOS</a></li>
                                <li class="nav-item"><a href="#profile-friends" class="nav-link" data-bs-toggle="tab">FRIENDS</a></li>
                            </ul>
        
                            <!-- END profile-header-tab -->
                    </div>   
			</div>
            </div>    
			<!-- END profile -->
			<!-- BEGIN profile-content -->
            <div class="container w-75 " >
				<!-- BEGIN tab-content -->
				<div class="row tab-content  panel-bg-0 justify-content-center mt-4 ">
					<!-- BEGIN #profile-post tab -->
					<div class="tab-pane fade active show " id="profile-post">
						<div class="row  p-0">
						<!-- BEGIN timeline -->
							<div class="col-md-4 ">
									<div class="row me-1 bg-white rounded ">
										<p class="p-2 fw-bold mb-0">{{ __('ui.intro') }}</p>
										<hr>
										<div class="p-3 text-muted"><i class="fa fa-user me-2"></i>{{ $profile_page->first_name }} {{ $profile_page->last_name }}</div>
									</div>
									<div class="row me-1  bg-white rounded  mt-4 ">
										<p class="p-2 fw-bold mb-0">{{ __('ui.photos') }}</p>
										<hr>
										<div class="p-3 text-muted">{{ count($allphoto) }} {{ __('ui.photos') }}</div>
									</div>
							</div>
							<div class="col-md-8 ">
								<div class="row">
									<div class="bg-0 ">
										<div class="card bg-white rounded p-2">
						<!-- #modal-dialog -->
							<form action="/posts" id="myForm"  method="POST" enctype="multipart/form-data">
								@csrf
								<input type="hidden" name="visibility" class="post-visibility-input" value="{{ auth()->check() ? (auth()->user()->default_post_visibility ?? 'public') : 'public' }}">
								<input type="hidden" name="post_text" class="inpotbox">
								<div class="profile-composer-top">
									@if(isset($profile) && isset($profile['photopro']))
                                        <img class="profile-composer-avatar" src="{{ asset($profile['photopro']->path.$profile['profile_photo_id'].$profile['photopro']->type) }}" onerror="this.onerror=null;this.src='{{ asset('img/Default_avatar_profile.jpg') }}';" alt="">
                                    @else
                                        <img class="profile-composer-avatar" src="{{ asset('img/Default_avatar_profile.jpg') }}" alt="">
                                    @endif
									<a href="#modal-dialog" class="profile-composer-prompt" data-bs-toggle="modal">{{ __('ui.whats_on_your_mind') }}</a>
								</div>

								<div class="profile-composer-actions">
									<button type="button" class="profile-composer-action" data-bs-toggle="modal" data-bs-target="#modal-dialog">
										<i class="fa fa-video text-danger"></i><span>Live video</span>
									</button>
									<label for="fileInput2" class="profile-composer-action">
										<i class="fa fa-images text-success"></i><span>Photo/video</span>
									</label>
									<input type="file" id="fileInput2" name="files[]" accept="image/*,video/*,.mkv,.avi,.mov,.webm,.mp4,.mpeg,.3gp" hidden multiple>
									<button type="button" class="profile-composer-action" data-bs-toggle="modal" data-bs-target="#modal-dialog">
										<i class="far fa-smile text-warning"></i><span>Feeling/activity</span>
									</button>
								</div>

								@error('post_content')
								<div>{{ $message }}</div>
								@enderror
							</form>
										</div>
									</div>
									
									<div id="allpost" class="m-0 p-0">
									@if(@empty($p_postes))

									@else
									@foreach ($p_postes as $post)
						
						<div class="row  mx-0 p-0">
                            <div class="row mx-0 mt-4">
                                <div class="card text-dark bg-white m-0 rounded">
                                    <!-- BEGIN timeline-header -->
                                    <div class="card-header border-0 mt-1 bg-white">
                                        <div class="row justify-content-between" >
                                            <div class="col-md-1  " >
                                                <div class="widget-icon rounded-circle bgr  text-white">
                                                    @if(isset($post->user['photopro']['path']))
                                                        <img src="{{asset($post->user['photopro']['path'].$post->user['photopro']['id'].$post->user['photopro']['type'])}}" class="rounded-circle" width="40" height="40" alt="">
                                                    @else 
                                                        <img src="" alt="">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-4 mx-2   text-start" >
                                                <div class="row justify-content-start text-start fs-15px fw-bolder ">
                                                    <a class="post-author text-dark text-decoration-none my-0" href="profile/{{$post->user['id']}}">{{$post->user['first_name']}}  {{$post->user['last_name']}}<i class="fa fa-check-circle text-blue ms-1"></i></a>
                                                    <div class="text-muted ">
                                                    <a class="post-time text-muted text-decoration-none" href="{{ url('/post/'.$post->id) }}"><time class="js-relative-time" datetime="{{ $post->created_at?->toIso8601String() }}">{{ $post->created_at?->diffForHumans() ?? 'just now' }}</time> <i class="fa {{ $post->visibility === 'only_me' ? 'fa-lock' : ($post->visibility === 'friends' ? 'fa-user-friends' : 'fa-globe-americas') }} opacity-5 ms-1" title="{{ ucfirst(str_replace('_', ' ', $post->visibility ?? 'public')) }}"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md offset-md-2 text-end" >
                                            <div class=" row justify-content-end  text-end">
                                                <a href="#" class="btn btn-lg border-0 rounded-pill w-40px h-40px p-0 d-flex align-items-center justify-content-center bg-transparent text-gray-500" data-bs-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end shadow">
                                                    <a href="#" class="dropdown-item d-flex align-items-center">
                                                        <i class="fa fa-fw fa-bookmark fa-lg"></i> 
                                                        <div class="flex-1 ps-1">
                                                            <div>{{ __('ui.save_post') }}</div>
                                                            <div class="mt-n1 text-gray-500">
                                                                <small>Add this to your saved items</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div class="dropdown-divider"></div>
													@if (isset(Auth::user()->id))
														@if($post->user['id']==Auth::user()->id)
															<a href="#" class="dropdown-item"><i class="fa fa-fw fa-edit fa-lg me-1"></i> Edit post</a>
															<a href="#" class="dropdown-item"><i class="fa fa-fw fa-user fa-lg me-1"></i> Edit audience</a>
															<a href="#" class="dropdown-item"><i class="fa fa-fw fa-bell fa-lg me-1"></i> Turn on notifications for this post</a>
															<a href="#" class="dropdown-item"><i class="fa fa-fw fa-language fa-lg me-1"></i> Turn off translations</a>
															<a href="#" class="dropdown-item"><i class="fa fa-fw fa-calendar-alt fa-lg me-1"></i> Turn date</a>
															<div class="dropdown-divider"></div>
															<a href="#" class="dropdown-item"><i class="fa fa-fw fa-archive fa-lg me-1"></i> Move to archive</a>
															<a href="#" class="dropdown-item"><i class="fa fa-fw fa-trash-alt fa-lg me-1"></i> Move to Recycle bin</a>
															@else 
															<a href="#" class="dropdown-item"><i class="fa fa-fw fa-bell fa-lg me-1"></i> Turn off notifications for this post</a>
														@endif
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
                                        <div class="mb-3 mx-0">
                                            <div class="m-2">
                                                    {!! \App\Support\HashtagFormatter::linkify($post->post_text) !!}
                                            </div>
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
                                                                    <video class="video-js vjs-default-skin p-0 VDlol" id="video_{{$i_videos[$r][0]->id}}" 
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
													@if(isset(Auth::user()->id))
                                                @if(($react->user_id==Auth::user()->id) && ($post->id ==$react->post_id))
                                            @php $like=   true;   @endphp
                                            @break 
                                                @endif
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
                                                <span class="reaction-picker" role="group" aria-label="{{ __('ui.reactions') }}">
                                                    @foreach([1=>'👍',2=>'❤️',3=>'🤗',4=>'😂',5=>'😮',6=>'😢',7=>'😡'] as $reactionId => $reactionEmoji)
                                                        <button type="button" class="reaction-option" data-type="{{$reactionId}}">{{$reactionEmoji}}</button>
                                                    @endforeach
                                                </span>
                                            </a>
                                            <a href="javascript:;" class="flex-fill text-decoration-none text-center text-gray-400">
                                                <i class="fa fa-comments fa-fw me-3px"></i> {{ __('ui.comment') }}
                                            </a> 
                                            <a href="javascript:;" class="flex-fill text-decoration-none text-center text-gray-400">
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
                                                    <img id="comment_img_{{$comment->id}}" src="{{ $commentPhoto ? asset($commentPhoto->path.$commentPhoto->id.$commentPhoto->type) : asset('img/Default_avatar_profile.jpg') }}" width="35" height="35" alt="avatar" class="img-fluid rounded-circle p-0 m-0 ">
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
                                                            @if(($react->user_id==Auth::user()->id) && ($comment->id ==$react->comment_id))
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
                                                        <img src="{{ asset($profile['photopro']->path.$profile['profile_photo_id'].$profile['photopro']->type)}}" width="30" height="30"  class="rounded-pill">
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
									@endif
									</div>
								</div>
							</div>
						</div>
						<!-- END timeline -->
					</div>
					<!-- END #profile-post tab -->
					<!-- BEGIN #profile-about tab -->
					<div class="tab-pane fade  " id="profile-about">
						<!-- BEGIN table -->
							<div class="row about bg-white rounded">
								
								<div class="col-4 border-secondary">
							<ul class="nav  flex-column nav-tabs">
								<span ><h1 class="p-3">About</h1></span>

								<li class="nav-item">
								  <a href="#overview" data-bs-toggle="tab" class="nav-link active">Overview</a>
								</li>
								<li class="nav-item">
									<a href="#work_and_education" data-bs-toggle="tab" class="nav-link">Work and education</a>
								  </li>
								  <li class="nav-item">
									<a href="#places_lived" data-bs-toggle="tab" class="nav-link">Places lived</a>
								  </li>
								  <li class="nav-item">
									  <a href="#contact_and_basic_info" data-bs-toggle="tab" class="nav-link">Contact and basic info</a>
								  </li>
								  <li class="nav-item">
									<a href="#family_and_relationships" data-bs-toggle="tab" class="nav-link">Family and relationships</a>
								  </li>
								  <li class="nav-item">
									  <a href="#details_about_you" data-bs-toggle="tab" class="nav-link ">Details about you</a>
								  </li>
								  <li class="nav-item">
									<a href="#life_events" data-bs-toggle="tab" class="nav-link ">Life events</a>
								  </li>
							  </ul>
							</div>
							<div class="col-8 p-5 ">
							  <div class="tab-content panel p-3 rounded-0 rounded-bottom">
								<div class="tab-pane fade active show" id="overview">
								  	{{ __('ui.no_items') }}
								</div>
								<div class="tab-pane fade " id="work_and_education">
									{{ __('ui.no_items') }}
								  </div>
								  <div class="tab-pane fade " id="places_lived">
									{{ __('ui.no_items') }}
								  </div>
								  <div class="tab-pane fade " id="contact_and_basic_info">
									{{ __('ui.no_items') }}
								  </div>
								  <div class="tab-pane fade " id="family_and_relationships">
									{{ __('ui.no_items') }}
								  </div>
								  <div class="tab-pane fade " id="details_about_you">
									{{ __('ui.no_items') }}
								  </div>
								  <div class="tab-pane fade " id="life_events">
									{{ __('ui.no_items') }}
								  </div>
							  </div>
							</div>
							</div>
							<div class="row bg-white rounded mt-4">
								<span><h1 class="p-3">Photo</h1></span>
								<div class="col-12  p-5"></div>
							</div>
					</div>

					<!-- END #profile-about tab -->
					<!-- BEGIN #profile-photos tab -->
					<div class="tab-pane bg-white rounded fade" id="profile-photos">
						<div class="row p-2">
							<div class="col-8"><h1>Photo</h1></div>
							<div class="col-3">
								<form action="photovideo" method="POST" enctype="multipart/form-data">
								<input type="file" name="files[]" style="display: none" id="in"  multiple />
						<label for="in"> <div class="adphoto btn btn-link rounded">Add Photos/Videos</div></label>
						 <button type='submit' class="btn btn-defult rounded">Add</button>

								</form>
							</div>
							
							<div class="col-1">
								<div class="btn-group">
									<a href="#" class="btn btn-default dropdown-toggle" data-bs-toggle="dropdown">...</a>
									<ul class="dropdown-menu dropdown-menu-end">
									 <li>1</li>
									 <li>2</li>
									 <li>3</li>
									 <li>4</li>
									</ul>
								  </div>
									</div>
						</div>

						<!-- BEGIN gallery-v2 -->
						<div class="gallery-v1" id="gallery">
							<div class="gallery">
								<div class="row">
									@if(count($allphoto) > 0)
									@php $chunks = $allphoto->chunk(4); @endphp <!-- تقسيم الصور إلى صفوف تحتوي كل منها على 4 صور -->
									@foreach($chunks as $chunk)
										<div class="row">
											@foreach($chunk as $photo)
												<div class="col-md-3">
													<div class="image gallery-group-4">
														<a href="../photo/{{$photo->id}}" class="ratio ratio-4x3" data-pswp-src="{{asset($photo->path.'/'.$photo->id.$photo->type)}}" data-pswp-width="1200" data-pswp-height="800">
															<div class="bg-size-cover bg-position-center" style="background-image: url({{asset($photo->path.'/'.$photo->id.$photo->type)}});"></div>
														</a>
													</div>
												</div>
											@endforeach
										</div>
									@endforeach
								@endif

									
									</div>
							</div>
							</div>
						</div>
						<!-- END gallery-v2 -->
					
					<!-- END #profile-photos tab -->
					<!-- BEGIN #profile-videos tab -->
					<div class="tab-pane bg-white rounded fade" id="profile-videos">
						<div class="d-flex align-items-center justify-content-between p-3 pb-2">
							<h4 class="mb-0">{{ __('ui.videos') }}</h4>
							<span class="text-muted">{{ $allvideo->count() }}</span>
						</div>
						<div class="row g-3 p-3 pt-1">
							@forelse($allvideo as $profileVideo)
								<div class="col-xl-4 col-md-6">
									<div class="profile-video-card">
										<video class="video-js vjs-default-skin profile-gallery-video" controls preload="metadata" data-setup='{}'>
											<source src="{{ asset($profileVideo->path.$profileVideo->id.'/playlist.m3u8') }}" type="application/x-mpegURL">
										</video>
									</div>
								</div>
							@empty
								<div class="col-12 py-5 text-center text-muted">
									<i class="fa fa-video fa-2x mb-3 d-block"></i>{{ __('ui.no_items') }}
								</div>
							@endforelse
						</div>
						{{--
						<div class="row gx-1">
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=RQ5ljyGg-ig" data-lity>
									<img src="https://img.youtube.com/vi/RQ5ljyGg-ig/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=5lWkZ-JaEOc" data-lity>
									<img src="https://img.youtube.com/vi/5lWkZ-JaEOc/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=9ZfN87gSjvI" data-lity>
									<img src="https://img.youtube.com/vi/9ZfN87gSjvI/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=w2H07DRv2_M" data-lity>
									<img src="https://img.youtube.com/vi/w2H07DRv2_M/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=PntG8KEVjR8" data-lity>
									<img src="https://img.youtube.com/vi/PntG8KEVjR8/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=q8kxKvSQ7MI" data-lity>
									<img src="https://img.youtube.com/vi/q8kxKvSQ7MI/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=cutu3Bw4ep4" data-lity>
									<img src="https://img.youtube.com/vi/cutu3Bw4ep4/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=gCspUXGrraM" data-lity>
									<img src="https://img.youtube.com/vi/gCspUXGrraM/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=COtpTM1MpAA" data-lity>
									<img src="https://img.youtube.com/vi/COtpTM1MpAA/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=8NVkGHVOazc" data-lity>
									<img src="https://img.youtube.com/vi/8NVkGHVOazc/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=QgQ7MWLsw1w" data-lity>
									<img src="https://img.youtube.com/vi/QgQ7MWLsw1w/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=Dmw0ucCv8aQ" data-lity>
									<img src="https://img.youtube.com/vi/Dmw0ucCv8aQ/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=r1d7ST2TG2U" data-lity>
									<img src="https://img.youtube.com/vi/r1d7ST2TG2U/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=WUR-XWBcHvs" data-lity>
									<img src="https://img.youtube.com/vi/WUR-XWBcHvs/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=A7sQ8RWj0Cw" data-lity>
									<img src="https://img.youtube.com/vi/A7sQ8RWj0Cw/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
							<!-- BEGIN col-3 -->
							<div class="col-md-3 col-sm-4 mb-1">
								<a href="https://www.youtube.com/watch?v=IMN2VfiXls4" data-lity>
									<img src="https://img.youtube.com/vi/IMN2VfiXls4/mqdefault.jpg" class="d-block w-100" />
								</a>
							</div>
							<!-- END col-3 -->
						</div>
						--}}
						<!-- END row -->
					</div>
					<!-- END #profile-videos tab -->
					<!-- BEGIN #profile-friends tab -->
					<div class="tab-pane bg-white rounded fade" id="profile-friends">
						<h4 class="mb-3">Friend List (14)</h4>
						<!-- BEGIN row -->
						<div class="row gx-1">
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-1.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">James Pittman</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-2.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">Mitchell Ashcroft</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-3.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">Ella Cabena</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-4.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">Declan Dyson</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-5.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">George Seyler</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-6.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">Patrick Musgrove</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-7.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">Taj Connal</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-8.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">Laura Pollock</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-9.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">Dakota Mannix</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-10.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">Timothy Woolley</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-11.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">Benjamin Congreve</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-12.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">Mariam Maddock</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-13.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">Blake Gerrald</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
							<!-- BEGIN col-6 -->
							<div class="col-xl-4 col-lg-6 mb-1">
								<div class="p-2 d-flex align-items-center card flex-row border-0 rounded">
									<a href="javascript:;">
										<img src="{{asset('assets/img/user/user-14.jpg')}}" alt="" class="rounded" width="64" />
									</a>
									<div class="flex-1 ps-3">
										<b class="text-white">Gabrielle Bunton</b>
									</div>
									<div>
										<a href="javascript:;" class="btn border-0 w-40px h-40px text-gray-500 rounded-pill d-flex align-items-center justify-content-center bg-none" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h fa-lg"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
											<a href="javascript:;" class="dropdown-item">Action 1</a>
											<a href="javascript:;" class="dropdown-item">Action 2</a>
											<a href="javascript:;" class="dropdown-item">Action 3</a>
											<div class="dropdown-divider"></div>
											<a href="javascript:;" class="dropdown-item">Action 4</a>
										</div>
									</div>
								</div>
							</div>
							<!-- END col-6 -->
						</div>
						<!-- END row -->
					</div>
					<!-- END #profile-friends tab -->
				</div>
				<!-- END tab-content -->
			</div>
		</div>

		<!-- END #content -->
        </div>
		<!-- BEGIN scroll-top-btn -->
		<!-- END scroll-top-btn -->
	</div>
	<!-- END #app -->
	@if(Auth::id() === $profile_page->id)
	<div class="modal fade" id="profileCropModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content profile-crop-modal">
				<div class="modal-header border-0">
					<h5 class="modal-title" id="profileCropTitle">ضبط الصورة</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body pt-0">
					<div class="profile-crop-stage"><img id="profileCropImage" alt="معاينة الصورة"></div>
					<div class="profile-crop-tools">
						<button type="button" class="btn btn-light rounded-circle" id="cropRotateLeft" title="تدوير لليسار"><i class="fa fa-rotate-left"></i></button>
						<button type="button" class="btn btn-light rounded-circle" id="cropZoomOut" title="تصغير"><i class="fa fa-minus"></i></button>
						<button type="button" class="btn btn-light rounded-circle" id="cropZoomIn" title="تكبير"><i class="fa fa-plus"></i></button>
						<button type="button" class="btn btn-light rounded-circle" id="cropRotateRight" title="تدوير لليمين"><i class="fa fa-rotate-right"></i></button>
					</div>
				</div>
				<div class="modal-footer border-0">
					<button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">إلغاء</button>
					<button type="button" class="btn btn-primary px-4" id="saveProfileCrop"><i class="fa fa-check me-2"></i>حفظ الصورة</button>
				</div>
			</div>
		</div>
	</div>
	<script>
	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.profile-media-view-trigger').forEach(function (image) {
			function openProfileMedia(event) {
				if (event.type === 'keydown' && event.key !== 'Enter' && event.key !== ' ') return;
				event.preventDefault();
				showphoto(image.dataset.profilePhotoId);
			}
			image.addEventListener('click', openProfileMedia);
			image.addEventListener('keydown', openProfileMedia);
		});
		const modalElement = document.getElementById('profileCropModal');
		const cropModal = new bootstrap.Modal(modalElement);
		const cropImage = document.getElementById('profileCropImage');
		const saveButton = document.getElementById('saveProfileCrop');
		let cropper = null;
		let selectedInput = null;
		let objectUrl = null;

		document.querySelectorAll('.profile-media-input').forEach(function (input) {
			input.addEventListener('change', function () {
				if (!this.files || !this.files[0]) return;
				selectedInput = this;
				objectUrl = URL.createObjectURL(this.files[0]);
				cropImage.src = objectUrl;
				document.getElementById('profileCropTitle').textContent = this.dataset.cover === '1' ? 'ضبط صورة الغلاف' : 'ضبط صورة الحساب';
				cropModal.show();
			});
		});

		modalElement.addEventListener('shown.bs.modal', function () {
			if (cropper) cropper.destroy();
			const isCover = selectedInput && selectedInput.dataset.cover === '1';
			cropper = new Cropper(cropImage, {
				aspectRatio: isCover ? 16 / 6 : 1,
				viewMode: 1,
				dragMode: 'move',
				autoCropArea: isCover ? 0.92 : 0.82,
				responsive: true,
				background: false,
				guides: true,
				center: true,
				cropBoxMovable: true,
				cropBoxResizable: true,
			});
		});

		modalElement.addEventListener('hidden.bs.modal', function () {
			if (cropper) { cropper.destroy(); cropper = null; }
			if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
			if (selectedInput) selectedInput.value = '';
		});

		document.getElementById('cropRotateLeft').addEventListener('click', function () { if (cropper) cropper.rotate(-90); });
		document.getElementById('cropRotateRight').addEventListener('click', function () { if (cropper) cropper.rotate(90); });
		document.getElementById('cropZoomOut').addEventListener('click', function () { if (cropper) cropper.zoom(-0.1); });
		document.getElementById('cropZoomIn').addEventListener('click', function () { if (cropper) cropper.zoom(0.1); });

		saveButton.addEventListener('click', function () {
			if (!cropper || !selectedInput) return;
			const isCover = selectedInput.dataset.cover === '1';
			saveButton.disabled = true;
			saveButton.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>جارٍ الحفظ';
			const canvas = cropper.getCroppedCanvas({
				width: isCover ? 1600 : 800,
				height: isCover ? 600 : 800,
				imageSmoothingEnabled: true,
				imageSmoothingQuality: 'high',
			});
			canvas.toBlob(async function (blob) {
				const data = new FormData();
				data.append('files', blob, 'cropped-profile.jpg');
				data.append('cover', isCover ? '1' : '0');
				try {
					const response = await fetch('/make-profile-picture', {
						method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}, body: data
					});
					if (!response.ok) throw new Error('upload failed');
					window.location.reload();
				} catch (error) {
					alert(@json(__('ui.upload_failed')));
					saveButton.disabled = false;
					saveButton.innerHTML = '<i class="fa fa-check me-2"></i>حفظ الصورة';
				}
			}, 'image/jpeg', 0.9);
		});
	});
	</script>
	@endif

@endsection
