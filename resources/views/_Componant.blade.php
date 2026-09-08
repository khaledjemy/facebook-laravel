    <div class="modal fade"  id="modal-dialog2">
        <div class="modal-dialog dropzoneDiv" >
            <div class="modal-content" style="width: 600px;">
                <div class="modal-header">
                    <h3 class="modal-title">Create Posts </h3>
                    
                        <a href="#modal-dialog" class="bg-gray rounded-circle  btn"  data-bs-toggle="modal"><-  </a>
                    
                </div>
                <div class="modal-body " >
                <div id="progressWrapper" style="width: 100%; background-color: #f3f3f3; border: 1px solid #ccc; height: 30px; display: none;">
                    <div id="progressBar" style="width: 0%; height: 100%; background-color: #4caf50;"></div>
                </div>
                <div id="percentage">0%</div>
                    <div class="row">
                        <div class="col-2 meny-pro-pic">
                          @if(isset($profile) && isset($profile['photopro']))
                        <img class="rounded-circle" src=" {{ asset($profile['photopro']->path.$profile['profile_photo_id'].$profile['photopro']->type) }}  " height="40" width="40" alt=""/>
                        @else
                          <img class="rounded-circle" src=" {{ asset('img/Default_avatar_profile.jpg') }}  " height="40" width="40" alt=""/>
                        @endif
                                            </div>
                        <div class="col-10 overflow-auto">
                            <div class="row m-1">
                                <div class="col-3">
                                    <span><h5>khaled jemy</h5></span>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-default">Dropdown</a>
                                        <a href="#" class="btn btn-default dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fa fa-caret-down"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                        ...
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-9">
                                    <textarea class="wysihtml5" placeholder="Enter text ..."></textarea>
                                    <input type="hidden" name="example[]" id="fileInput" />
                                </div>
                            </div>
                            <div id="myDropzone" class="position-relative">
                                <div class="dropzoneDiv w-100 h-100 bg-gray " id="previewDiv">
                                    Drop files here or click to upload
                                </div>
                                <button id="sendButton">Send</button>
                            </div>
                        </div>
                        <div class="row rounded border p-3 mx-1">
                            <div class="col-md-4">add to your post</div>
                            <div class="col-md-8" >
                                <ul class="nav nav-pills">
                                    <li class="nav-item mx-2 rounded-circle">
                                        <a href="#modal-dialog" data-bs-toggle="modal" class=" "><img class="rounded img-fluid w-25px h-25px"  src="{{asset('style/Ivw7nhRtXyo.png')}}"></a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#modal-dialog2" data-bs-toggle="tab" class=" "><img class="rounded img-fluid w-25px h-25px"  src="{{asset('style/b37mHA1PjfK.png')}}"></a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#default-tab-1" data-bs-toggle="tab" class=" "><img class="rounded img-fluid w-25px h-25px"  src="{{asset('style/Y4mYLVOhTwq.png')}}"></a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#default-tab-1" data-bs-toggle="tab" class=" "><img class="rounded img-fluid w-25px h-25px"  src="{{asset('style/8zlaieBcZ72.png')}}"></a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#default-tab-1" data-bs-toggle="tab" class=" "><img class="rounded img-fluid w-25px h-25px"  src="{{asset('style/h_kj6ECZ7Ii.png')}}"></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row-mx-2  modal-footer px-4">
                        <button type="button" id="PostSubmit"  href="javascript:;" id="post" class="col-12 btn btn-primary ">POST</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade "  id="modal-dialog">
        <div class="modal-dialog dropzoneDiv" >
            <div class="modal-content" style="width: 600px;">
                <div class="modal-header">
                    <h3 class="modal-title">Create Posts </h3>
                    <button type="button" class="btn-close bg-gray rounded-circle p-3" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body ">
                
                    <div class="row">
                        <div class="col-md-1 meny-pro-pic">
                            @if(isset($profile) && isset($profile['photopro']))
                            <img class="rounded-circle" src=" {{ asset($profile['photopro']->path.$profile['profile_photo_id'].$profile['photopro']->type) }}  " height="40" width="40" alt=""/>
                            @else
                            <img class="rounded-circle" src=" {{ asset('img/Default_avatar_profile.jpg') }}  " height="40" width="40" alt=""/>
                            @endif
                        </div>
                    <div class="col-md-11 ">
                        <div class="row m-1">
                            <div class="col-md-3">
                                <span>
                                    <h5>khaled jemy</h5>
                                </span>
                                <div class="btn-group">
                                    <a href="#" class="btn btn-default">Dropdown</a>
                                    <a href="#" class="btn btn-default dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fa fa-caret-down"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>public</li>
                                        <li>freinds</li>
                                        <li>only me</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-9">
                                <textarea class="wysihtml5" id='wysihtml5' placeholder="Enter text ..."></textarea>
                            </div>                 
                        </div>
                    </div>
                    <div class="row rounded border p-3 mx-1">
                        <div class="col-md-4">add to your post</div>
                        <div class="col-md-8" >
                            <ul class="nav nav-pills">
                                <li class="nav-item mx-2 rounded-circle">
                                    <a href="#modal-dialog2" data-bs-toggle="modal" class=" "><img class="rounded img-fluid w-25px h-25px"  src="{{asset('style/Ivw7nhRtXyo.png')}}"></a>
                                </li>
                                <li class="nav-item">
                                    <a href="#modal-dialog2" data-bs-toggle="tab" class=" "><img class="rounded img-fluid w-25px h-25px"  src="{{asset('style/b37mHA1PjfK.png')}}"></a>
                                </li>
                                <li class="nav-item">
                                    <a href="#default-tab-1" data-bs-toggle="tab" class=" "><img class="rounded img-fluid w-25px h-25px"  src="{{asset('style/Y4mYLVOhTwq.png')}}"></a>
                                </li>
                                <li class="nav-item">
                                    <a href="#default-tab-1" data-bs-toggle="tab" class=" "><img class="rounded img-fluid w-25px h-25px"  src="{{asset('style/8zlaieBcZ72.png')}}"></a>
                                </li>
                                <li class="nav-item">
                                    <a href="#default-tab-1" data-bs-toggle="tab" class=" "><img class="rounded img-fluid w-25px h-25px"  src="{{asset('style/h_kj6ECZ7Ii.png')}}"></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    </div>
                    <div class="row-mx-2  modal-footer px-4">
                        <button type="button" id="PostSubmit2"  href="javascript:;" class="col-12 btn btn-primary ">Next</button>
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
