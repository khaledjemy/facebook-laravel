
<script src="{{ asset('./assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('./assets/js/app.min.js') }}"></script>
<script src="{{ asset('./assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('./assets/js/crop_photo.js') }}"></script>
<script src="{{ asset('./assets/plugins/dropzone/dist/min/dropzone.min.js') }}"></script>
<script src="{{ asset('./assets/plugins/bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.all.min.js') }}"></script>
<script src="{{ asset('./assets/js/video.min.js') }}"></script>
<script src="{{ asset('./assets/js/videojs-http-streaming.min.js') }}"></script>
<script src="{{ asset('./assets/js/videojs-hls-quality-selector.min.js') }}"></script>


<script>
    const ASSETS = "/";
    const assets = "/";

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

 function AjaxReqForAll(url,method,formData,post){
    $.ajax({
        url: url, 
        type: method,
        data: formData,
        contentType: false,
        processData: false, 
        xhr: function () {
                var xhr = new XMLHttpRequest();
                xhr.upload.onprogress = function (e) {
                    if (e.lengthComputable) {
                        var percent = (e.loaded / e.total) * 100;
                        $("#progressWrapper").show();  
                        $("#progressBar").css("width", percent + "%");
                        $("#percentage").text(Math.round(percent) + "%");
                    }
                };
                return xhr;
            },
        success: function (response) {
                $('#loading').hide();
                componant(post,response);
        },
        error: function (xhr, status, error) {
            $('#loading').hide();
            $('#PostSubmit, #PostSubmit2').prop('disabled', false).text('{{ __("ui.post") }}');
            console.log(error,status,xhr);
            console.error('Error:', error);
            var msg = xhr.responseJSON?.message || xhr.responseJSON?.errors?.post_text?.[0] || 'حدث خطأ أثناء معالجة الطلب.';
            alert(msg);
        }
    });
            
        }

$(document).on('click', '.contact-chat', function (e) {
    e.preventDefault();
    const id = $(this).data('chat-id');
    $('#mini-chat').removeClass('d-none').data('chat-id', id);
    $('#mini-chat-name').text($(this).data('chat-name'));
    $('#mini-chat-messages').html('<div class="text-muted small text-center">جاري التحميل...</div>');
    $.ajax({
        url: '/messanger/' + id,
        method: 'GET',
        data: {format: 'json'},
        dataType: 'json',
        headers: {'Accept': 'application/json'},
        success: function (messages) {
        const box = $('#mini-chat-messages').empty();
        if (!messages.length) box.html('<div class="text-muted small text-center">ابدأ المحادثة</div>');
        messages.forEach(function (m) {
            let body = $('<div>').text(m.message || '').html();
            if (m.attachment && m.attachment_type === 'image') body += '<img class="mini-chat-image" src="' + m.attachment + '" alt="">';
            box.append('<div class="mini-chat-message ' + (m.my_id == {{ Auth::id() ?? 0 }} ? 'mine' : '') + '">' + body + '</div>');
        });
        box.scrollTop(box[0].scrollHeight);
        },
        error: function () {
            $('#mini-chat-messages').html('<div class="text-danger small text-center">تعذر تحميل المحادثة</div>');
        }
    });
});
$(document).on('click', '#mini-chat-close', function () { $('#mini-chat').addClass('d-none'); });
$(document).on('click', '.reaction-count', function (e) {
    e.preventDefault();
    $.get('/post/' + $(this).data('post-id') + '/reactions', function (items) {
        const modal = $('#reaction-modal'), list = $('#reaction-modal-list').empty(), tabs = $('#reaction-modal-tabs').empty();
        if (!items.length) {
            list.html('<div class="reaction-empty">{{ __('ui.no_reactions') }}</div>');
        } else {
            const counts = {};
            items.forEach(function (item) {
                counts[item.type_id] = counts[item.type_id] || {emoji:item.emoji, count:0};
                counts[item.type_id].count++;
                list.append('<a class="reaction-person" data-type="' + item.type_id + '" href="' + item.profile_url + '"><img src="' + item.avatar + '" alt=""><span><strong>' + $('<div>').text(item.name).html() + '</strong><small>' + item.emoji + ' ' + $('<div>').text(item.type).html() + '</small></span></a>');
            });
            tabs.append('<button type="button" class="active" data-filter="all">{{ __('ui.all') }} ' + items.length + '</button>');
            Object.keys(counts).forEach(function (type) { tabs.append('<button type="button" data-filter="' + type + '">' + counts[type].emoji + ' ' + counts[type].count + '</button>'); });
        }
        modal.removeClass('d-none');
    });
});
$(document).on('click', '#reaction-modal-close, #reaction-modal', function (e) { if (e.target === this) $('#reaction-modal').addClass('d-none'); });
$(document).on('click', '#reaction-modal-tabs button', function () {
    const type = $(this).data('filter');
    $(this).addClass('active').siblings().removeClass('active');
    $('.reaction-person').toggle(type === 'all').filter('[data-type="' + type + '"]').toggle(type !== 'all');
});
$(document).on('click', '.reaction-option', function (e) {
    e.preventDefault(); e.stopPropagation();
    const like = $(this).closest('.like');
    like.attr('data-type_id', $(this).data('type')).data('type_id', $(this).data('type'));
    like.trigger('click');
});
$(document).on('click', '.save-post', function (e) {
    e.preventDefault();
    const button = $(this);
    $.post('/saved/' + button.data('post-id'), {_token:'{{ csrf_token() }}'}, function (data) {
        button.find('div > div:first').text(data.saved ? 'Saved' : 'Save Post');
        button.find('small').text(data.saved ? 'This post is in your saved items' : 'Add this to your saved items');
    });
});
$(document).on('change', '.reaction-type', function () { $(this).closest('.like').attr('data-type_id', $(this).val()); });
$(document).on('submit', '#mini-chat-form', function (e) {
    e.preventDefault();
    const input = $('#mini-chat-input'), message = input.val().trim(), id = $('#mini-chat').data('chat-id');
    if (!message || !id) return;
    $.post('/messanger/' + id, {_token: '{{ csrf_token() }}', text: message}, function () {
        $('#mini-chat-messages').append('<div class="mini-chat-message mine">' + $('<div>').text(message).html() + '</div>');
        input.val(''); const box = $('#mini-chat-messages'); box.scrollTop(box[0].scrollHeight);
    });
});
$(document).on('click', '#mini-emoji', function () { $('#mini-chat-input').val($('#mini-chat-input').val() + ' 😊').trigger('focus'); });
let activeCallStream = null;
$(document).on('click', '.mini-call', async function () {
    const kind = $(this).data('kind');
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) { alert('المتصفح لا يدعم المكالمات من هذا السياق.'); return; }
    try {
        activeCallStream = await navigator.mediaDevices.getUserMedia({audio:true, video: kind === 'video'});
        const video = document.getElementById('call-video'); video.srcObject = activeCallStream; video.style.display = kind === 'video' ? 'block' : 'none';
        $('#call-label').text(kind === 'video' ? 'مكالمة فيديو مع ' + $('#mini-chat-name').text() : 'مكالمة صوتية مع ' + $('#mini-chat-name').text()); $('#call-panel').removeClass('d-none');
    } catch (e) { alert('لم يتم السماح بالوصول إلى الكاميرا أو الميكروفون.'); }
});
$(document).on('click', '#end-call', function () { if (activeCallStream) activeCallStream.getTracks().forEach(t => t.stop()); activeCallStream = null; $('#call-panel').addClass('d-none'); });

$(document).ready(function() {



 

    function setupPopup(triggerId, popupId) {
      $('#' + triggerId).on('click', function(e) {
        e.stopPropagation();
        $('.fb-popup, .profile-menu').not('#' + popupId).removeClass('show');
        $('#' + popupId).toggleClass('show');
      });
    }
    setupPopup('notifications-icon', 'notifications-popup');
    setupPopup('chat-icon', 'chat-popup');
    setupPopup('menu-icon', 'menu-popup');
    setupPopup('profile-icon', 'profile-popup');

    $(document).on('click', function(e) {
      if (!$(e.target).closest('.icon-wrapper').length) {
        $('.fb-popup, .profile-menu').removeClass('show');
      }
    });



    $('.navlink .nav-link').on('click', function() {
      const targetHref = $(this).attr('href');
      $('.navlink .nav-link').removeClass('active');
      $('.navlink .nav-link[href="' + targetHref + '"]').addClass('active');
    });


    
// Create Post Audience Selector
$(document).on('click', '.select-post-audience', function(e) {
    e.preventDefault();
    var val = $(this).data('val');
    var icon = $(this).data('icon');
    var color = $(this).data('color') || 'text-primary';
    var label = $(this).data('label');

    // Update button display in all composer modals
    $('.post-audience-current-icon').attr('class', 'fa ' + icon + ' ' + color + ' post-audience-current-icon');
    $('.post-audience-current-label').text(label);

    // Update hidden inputs in #myForm and anywhere else
    $('.post-visibility-input').val(val);

    // Update active indicators in the menu
    $('.select-post-audience').removeClass('active bg-light text-dark');
    $('.select-post-audience[data-val="' + val + '"]').addClass('active bg-light text-dark');
    $('.check-audience').addClass('d-none');
    $('.check-audience[data-val="' + val + '"]').removeClass('d-none');
});

// Unified Post Submission for both Modals
$(document).on('click', '#PostSubmit, #PostSubmit2', function(e) {
    e.preventDefault();

    // 1. Sync textarea from WYSIHTML5 into .inpotbox
    var textFound = '';
    if (typeof editors !== 'undefined' && editors.length > 0) {
        for (var i = 0; i < editors.length; i++) {
            var v = editors[i].getValue();
            if (v && v.trim() !== '') {
                textFound = v;
                break;
            }
        }
    }
    if (!textFound) textFound = $(this).closest('.modal').find('.post-composer-text').val() || '';
    if (textFound) {
        $('.inpotbox').val(textFound);
    }

    // 2. Prepare Form Data
    var $form = $("#myForm");
    if ($form.length === 0) return;

    var formData = new FormData($form[0]);

    // Ensure post_text is populated in formData
    if (textFound && (!formData.has('post_text') || !formData.get('post_text'))) {
        formData.set('post_text', textFound);
    }

    // Ensure visibility is explicitly set
    var chosenVisibility = $('.post-visibility-input').val() || 'public';
    formData.set('visibility', chosenVisibility);

    var $btn = $(this);
    $btn.prop('disabled', true).text('جاري النشر...');

    AjaxReqForAll("/posts", "POST", formData, "Post");
});
  
const params = new URLSearchParams(window.location.search);
var page = 1;//params.get("page") || 1;
var isLoading =false;

        $(document).on('scroll', function() {
            if (isLoading) return; 

        // console.log($(document).scrollTop());
        // console.log($(document).height());
            
            if (($(document).scrollTop()  >= ($(document).height()-800))) {
                    isLoading = true;
                    page++
                    console.log(page+"_1");
                loadMorePosts(page); 
            }
        });


    Dropzone.autoDiscover = false;

// Initialize Dropzone
$("#myDropzone").each(function() {
var myDropzone = new Dropzone(this, {
    url: "/dummy", // URL وهمي لأننا سنرسل الملفات من خلال input[type="file"]
    autoProcessQueue: false,
    previewsContainer: "#previewDiv",
    clickable: true,
    acceptedFiles: "image/*,video/*", 
    maxFilesize: 200, 
    previewTemplate: `
        <div class="dz-preview dz-image-preview">
            <div class="dz-image"><img data-dz-thumbnail /></div>
            <div class="dz-details">
                <div class="dz-size"><span data-dz-size></span></div>
                <div class="dz-filename"><span data-dz-name></span></div>
            </div>
            <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
            <div class="dz-error-message"><span data-dz-errormessage></span></div>
            <div class="dz-success-mark">
                <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <title>Check</title>
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <path d="M23.5,31.8431458 L17.0303301,25.3734859 C16.7374369,25.0805927 16.2625631,25.0805927 15.9696699,25.3734859 C15.6767767,25.6663791 15.6767767,26.1412529 15.9696699,26.4341461 L22.9696699,33.4341461 C23.2625631,33.7270393 23.7374369,33.7270393 24.0303301,33.4341461 L37.0303301,20.4341461 C37.3232233,20.1412529 37.3232233,19.6663791 37.0303301,19.3734859 C36.7374369,19.0805927 36.2625631,19.0805927 35.9696699,19.3734859 L23.5,31.8431458 Z" fill="#2ECC71"></path>
                    </g>
                </svg>
            </div>
        </div>
    `,
    init: function() {
        var dropzoneInstance = this;
        var fileInput = document.querySelector("#myForm #fileInput2");
        dropzoneInstance.on("addedfile", function(file) {
            var removeButton = Dropzone.createElement(`
            <button class='dz-remove'>
                Remove file
            </button>`);
            removeButton.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzoneInstance.removeFile(file);
                updateFileInput();
            });
            file.previewElement.appendChild(removeButton);
            updateFileInput();
        });
        dropzoneInstance.on("removedfile", function(file) {
            updateFileInput();
        });

        function updateFileInput() {
            if (!fileInput) return;
            const dataTransfer = new DataTransfer();
            // إضافة الملفات من Dropzone إلى DataTransfer
            dropzoneInstance.files.forEach(file => {
                dataTransfer.items.add(file);
            });
            fileInput.files = dataTransfer.files;
         
        }
    }
});
});

        
    // Initialize the WYSIHTML5 editors
    var editors = [];
            $('.wysihtml5').each(function(index) {
                var editor = $(this).wysihtml5({
                    "toolbar": {
                        "font-styles": false,
                        "emphasis": false,
                        "lists": false,
                        "html": false,
                        "link": false,
                        "image": false,
                        "color": false,
                        "blockquote": false,
                        "size": 'sm',
                        "custom": true
                    },
                    "locale": "en-US"
                }).data("wysihtml5").editor;
                editors.push(editor);
            });

            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this, args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
            }

            function updateEditors(content) {
                editors.forEach(editor => {
                    editor.setValue(content);
                });
            }

            const getContent = debounce(function() {
                var content = this.getValue();
                updateEditors(content);
               // console.log(content);
                document.getElementsByClassName('inpotbox')[0].value=content;
               
            }, 100); 

            editors.forEach(editor => {
                editor.on('change', getContent);
            });         
       
    $(document).on('click', '.like', function(event){  
        var post_id = $(this).data('post_id');
        var photo_id = $(this).data('photo_id');
        var video_id = $(this).data('video_id');
        var user_id = $(this).data('user-id');
        var type_id = $(this).data('type_id');
        var liked = $(this).data("liked");
        
        var csrfToken = "{{ csrf_token() }}";
        var form = new FormData();
        
        if (!liked) {
            $(this).data('liked', true);
            $(this).addClass('active');
            $(this).find('#like').addClass('text-blue-400').removeClass('text-gray-400');
        } else {
            $(this).data('liked', false);
            $(this).removeClass('active');
            $(this).find('#like').addClass('text-gray-400').removeClass('text-blue-400');
        }
        var assets = "{{ asset('') }}";
        form.append('post_id', post_id);
        form.append('user_id', user_id);
        form.append('type_id', type_id);
        form.append('liked', liked);
        form.append('_token', csrfToken);
        form.append('photo_id', photo_id);
        form.append('video_id', video_id);
        //console.log(post_id,liked,user_id,type_id,photo_id);
        if(video_id != null && video_id != undefined){
            AjaxReqForAll(assets+"video/like","POST",form,"ReactionMedia");
        }else if(post_id==null||post_id==undefined){
            AjaxReqForAll(assets+"photo/like","POST",form,"ReactionMedia");
        }else{
            AjaxReqForAll("like","POST",form,"ReactionPost");
        }
        
        
    });

   
    $(document).on('click', '.like_comment', function(event){  
        var comment_id = $(this).data('comment_id');
        var user_id = $(this).data('user-id');
        var type_id = $(this).data('type_id');
        var liked = $(this).data("comment_liked");
        
        var csrfToken = "{{ csrf_token() }}";
        var form = new FormData();
        
        if (!liked) {
            $(this).data('comment_liked', true);
            $(this).addClass('text-blue-600').removeClass('text-gray-600');
        } else {
            $(this).data('comment_liked', false);
            $(this).addClass('text-gray-600').removeClass('text-blue-600');
        }
        var assets = "{{ asset('') }}";
        form.append('comment_id', comment_id);
        form.append('user_id', user_id);
        form.append('type_id', type_id);
        form.append('liked', liked);
        form.append('_token', csrfToken);
        //console.log(post_id,liked,user_id,type_id,photo_id);
        if(comment_id==null||comment_id==undefined){
            AjaxReqForAll(assets+"likecomment","POST",form,"ReactionPost");
        }else{
            AjaxReqForAll(assets+"likecomment","POST",form,"ReactionPost");
        }
        
        
    });
    
 

    
$(document).on('focus', '.comment', function(event){
           
      
    $(this).off('keyup').on('keyup', function(e){
            var csrfToken = "{{ csrf_token() }}";
            var form = new FormData();
            var post_id = $(this).data('post_id');
            var comment = $(this).val();
            var photo_id= $(this).data('photo_id');
            var video_id= $(this).data('video_id');
            var assets = "{{ asset('') }}";
        if ((e.key === 'Enter' || e.keyCode === 13 )&&comment.length>0) {
            e.preventDefault();
            form.append('_token', csrfToken);
            form.append('post_id', post_id);
            form.append('comment', comment);
            form.append('photo_id', photo_id);
            form.append('video_id', video_id);
            if(video_id != null && video_id != undefined){
                    AjaxReqForAll(assets+"commentvideo","POST",form,"CommentVideo");
                }else if(post_id==null||post_id==undefined){
                    AjaxReqForAll(assets+"commentphoto","POST",form,"CommentPhoto");
                }else{
                    AjaxReqForAll(assets+"comment","POST",form,"CommentPost");
                }
                    $(this).val('');
            
            //console.log(comment);
        return;
            

        }
    });
});
$(document).on('click','.id_raplay_comment_',function(event){
  var comid=  $(this).data('id_raplay_comment_');
  console.log(comid)
return  ReplyBox(comid);
});


$(document).on("mouseenter", ".comment", function () {
    $(this).find(".opt_comment").removeClass("d-none");
});

$(document).on("mouseleave", ".comment", function () {
    $(this).find(".opt_comment").addClass("d-none");
});


    function loadMorePosts(page) {
      // console.log(page);
        var $feed = $('#allpost');
        var $loader = $('#loading');
        if ($feed.length) {
            $loader.appendTo($feed).addClass('feed-inline-loader').show();
        } else {
            $loader.show();
        }
        setTimeout(function(){
           AjaxReqForAll(ASSETS+"?page="+page,'GET',{page:page},"Postreload");  
           isLoading = false; 
        },1000);
    }
       
});

function ReplyBox(id){
        var status= $('#reply-box_'+id).css('display');

        if(status=='none'){
            $('#reply-box_'+id).css('display','block');
        }else{
            $('#reply-box_'+id).css('display','none');
        }

        $("#replay_"+id).off('click').on('click',function (e){
        e.preventDefault();
        var comment= ($("#commen_rplay_"+id).val());
        var userid=$("#commen_rplay_"+id).data('user_id');
        var cuserid=$("#commen_rplay_"+id).data('cuser_id');
        var csrfToken = "{{ csrf_token() }}";

            var asset= "{{ asset('') }}";
        formdata = new FormData();
        formdata.append('comment_id',id);
        formdata.append('user_id',userid);
        formdata.append('userreplay_id',cuserid);
        formdata.append('comment',comment);
        formdata.append('_token',csrfToken);
        AjaxReqForAll(asset+"post-reply","POST",formdata,"replay")
        //console.log(comment,userid,cuserid,id);

    });
    }

  function Delete(idelemet,id){
    var assets = "{{ asset('') }}";
    var csrf    = "{{ csrf_token() }}";
    var formData = new FormData();  
    formData.append('_token', csrf);
    formData.append('id', id);
    var page=null;
    switch(idelemet){
        case "post":
            page="posts";
            formData.append('post_id', id);
            break;
        case "comment":
            page="commentdelete";
            formData.append('comment_id', id);
            AjaxReqForAll(assets+"commentdelete","POST",formData,"Delete_comment");
            break;
        case "photocomment":
            page="photocommentdelete";
            formData.append('comment_id', id);
            AjaxReqForAll(assets+"photocommentdelete","POST",formData,"dphotocomment");
            break;
        
    }

    
   // console.log(id,idelemet);         
}
function componant($compvar,data){
    switch($compvar){
        case "Post":
            $('#PostSubmit, #PostSubmit2').prop('disabled', false).text('{{ __("ui.post") }}');
            if (data.success=="ok") {
                $("#modal-dialog").modal('hide');
                $("#modal-dialog2").modal('hide');
                if (typeof editors !== 'undefined' && editors.length > 0) {
                    editors.forEach(function(ed) { ed.setValue(''); });
                }
                $('.inpotbox').val('');
                $('.post-composer-text').val('');
                var composerDropzone = document.getElementById('myDropzone');
                if (composerDropzone && composerDropzone.dropzone) {
                    composerDropzone.dropzone.removeAllFiles(true);
                }
                AjaxReqForAll("/post/"+data.data.id,"GET",false,"PostRes");
            } else if(data.success=="failed"){
                alert(data.data || 'فشلت عملية النشر');
            } else {
                console.error('خطأ:', data.error);
                console.error('التفاصيل:', data.details);
                alert('فشل العملية: ' + (data.details || 'حدث خطأ أثناء النشر'));
            }
            break;
        case "PostRes":
            var post= $(data).find("#Post_ONE");
         var  lol= $(post).removeAttr("#Post_ONE");
            var $newPost = $(`<div class='postes'>${post.html()}</div>`);
            $("#allpost").prepend($newPost);
            initializeFeedVideos($newPost[0]);
            break;
        case "Postreload":
            var postes= $(data).find("#allpost");
            var lol=postes.children();
            $('#loading').hide();
            $("#allpost").append(lol);
            var play= $('.VDlol');
            play.each(function(index,element){
                initPlayer(element);
            });
           // createElement
          // return isLoading=true;
            break;
        case "ReactionPost":
           // console.log(data);
            $('.response-container').html(data);
            break;
        case "ReactionMedia":
            if (data && data.status === 'ok') {
                $('[data-'+data.media_type+'_id="'+data.media_id+'"]').data('liked', data.liked);
                $('#'+data.media_type+'_reaction_count_'+data.media_id).text(data.count);
            }
            break;
        case "CommentPost":
        var $original = $("#comment_");
        var $repOriginal = $("#reply-box_");
        var $repcopy = $repOriginal.clone();

        var $comment = $original.clone();
            $comment.removeAttr("id").attr("id", "comment_" + data.details.post_id + "_" + data.details.id);
            $comment.removeClass("d-none");
            $comment.find("#comment_img").removeAttr("id").attr({id: "comment_img_" + data.details.id,src: "{{ asset('') }}"+data.details.user['photopro'].path+data.details.user['photopro'].id+data.details.user['photopro'].type});
            $comment.find("#user_link").removeAttr("id").removeAttr("href").attr({id: "user_link_" + data.details.user['id'],href: "profile/"+data.details.user['id']});
            $comment.find("#comment_text").removeAttr("id").attr("id", "comment_text_" + data.details.id).html(data.details.text_co+".");
            $comment.find("#comment_name").removeAttr("id").attr("id", "comment_name_" + data.details.id).html(data.details.user['first_name']+" "+data.details.user['last_name']);
            $comment.find("#id_raplay_comment_").removeAttr("id").attr({id:"id_raplay_comment_"+data.details.id,"data-id_raplay_comment_":data.details.id});
            $comment.find("#comment_opt").removeAttr("id").attr("id" ,"comment_opt_" + data.details.id).off('click').on("click",function(){
                Delete("comment",data.details.id);
            });
            $repcopy.find("#commen_rplay_").removeAttr("id").attr({id:"commen_rplay_"+data.details.id,"data-user_id":"{{ (Auth::check())?  Auth::user()->id:0}}", "data-cuser_id":data.details.user['id']}).val(data.details.user['first_name']+" "+data.details.user['last_name']);
            $repcopy.removeAttr("id").attr({id:"reply-box_"+data.details.id});
            
            $repcopy.find("#replay_").removeAttr("id").attr({id:"replay_"+data.details.id});
            $comment.find("#father_cid_").removeAttr("id").attr({id:"father_cid_"+data.details.id}).append($repcopy,"<div id='rid_"+data.details.id+"'></div>" );
           // $comment.find("#like_comment").removeAttr("id").attr({id:"like_comment_"+data.details.id,"data-comment_id":data.details.id,"data-type_id":data.details.type_id,"data-user-id":"{{ (Auth::check())?  Auth::user()->id:0}}","data-comment_liked":false});
            $("#comment_block_"+data.details.post_id).append($comment);
            break;
        case "CommentPhoto":
            console.log(data);
            var $original = $("#comment_");
            var $comment = $original.clone();
            $comment.removeAttr("id").attr("id", "photocomment_" + data.details.photo_id + "_" + data.details.id);
            $comment.removeClass("d-none");
            $comment.find("#comment_img").removeAttr("id").attr({id: "photocomment_img_" + data.details.id,src: "{{ asset('') }}"+data.details.user['photopro'].path+data.details.user['photopro'].id+data.details.user['photopro'].type});
            $comment.find("#user_link").removeAttr("id").removeAttr("href").attr({id: "user_link_" + data.details.user['id'],href: "profile/"+data.details.user['id']});
            $comment.find("#comment_text").removeAttr("id").attr("id", "photocomment_text_" + data.details.id).html(data.details.comment+".");
            $comment.find("#comment_name").removeAttr("id").attr("id", "photocomment_name_" + data.details.id).html(data.details.user['first_name']+" "+data.details.user['last_name']);
            $comment.find("#comment_opt").removeAttr("id").attr("id" ,"photocomment_opt_" + data.details.id).off('click').on("click",function(){
                Delete("photocomment",data.details.id);
            });
            $("#comment_photo_block_"+data.details.photo_id).append($comment);
            break;
        case "CommentVideo":
            var details = data.details;
            var photo = details.user && details.user.photopro;
            var avatar = photo ? "{{ asset('') }}"+photo.path+photo.id+photo.type : "{{ asset('img/Default_avatar_profile.jpg') }}";
            var name = details.user ? details.user.first_name+" "+details.user.last_name : '';
            var row = $('<div class="media-comment-row"><img class="media-comment-avatar"><div class="media-comment-main"><div class="media-comment-bubble"><a class="media-comment-name"></a><div class="media-comment-text"></div></div><div class="media-comment-meta">الآن · <button type="button">أعجبني</button> · <button type="button">رد</button></div></div></div>');
            row.find('img').attr('src', avatar);
            row.find('.media-comment-name').attr('href', "{{ asset('profile') }}/"+details.user_id).text(name);
            row.find('.media-comment-text').text(details.comment);
            $('#comment_video_block_'+details.video_id).append(row);
            $('#video_comment_count_'+details.video_id).text(data.count);
            break;
        case "Delete_comment":
            console.log(data);
            var id = data.details.id;
            var post_id = data.details.post_id;
            var comment = $("#comment_" + post_id + "_" + id);
            comment.remove();
            break;
        case "dphotocomment":
            console.log(data);
            var id = data.details.id;
            var photo_id = data.details.photo_id;
            var comment = $("#photocomment_" + photo_id + "_" + id);
            comment.remove();
            break;
        case "replay":
           // console.log(data);
        var $original = $("#replay_id_");
        var $replay = $original.clone();
            $replay.removeAttr("id").attr("id", "replay_id_" + data.details.id);
            $replay.removeClass("d-none");
            $replay.find("#replay_photo_id_").removeAttr("id").attr({id: "replay_photo_id_" + data.details.id,src: "{{ asset('') }}"+data.details.userreply['photopro'].path+data.details.userreply['photopro'].id+data.details.userreply['photopro'].type});
            $replay.find("#link_id_").removeAttr("id").removeAttr("href").attr({id: "link_id_" + data.details.userreply['id'],href: "profile/"+data.details.userreply['id']});
            $replay.find("#replay_comen_").removeAttr("id").attr("id", "replay_comen_" + data.details.id).html(data.details.reply+".");
            $replay.find("#userdata_fl_").removeAttr("id").attr("id", "userdata_fl_" + data.details.id).html(data.details.userreply['first_name']+" "+data.details.userreply['last_name']);
            $("#rid_"+data.details.comment_id).append($replay);
            break;
        case "crobed":
            if(data['cover']==null){
                $('.photop').removeAttr("src").attr({src: "{{ asset('') }}"+data['profile']});
              //console.log(data['profile']);  
            }else{
                $('.photoc').removeAttr("src").attr({src: "{{ asset('') }}"+data['cover']});
                //console.log(data['cover']);
            }
            break;
        }
function getpage(varpage,id){
    $.get(varpage,)
}

}

function initializeFeedVideos(scope) {
    $(scope || document).find('.VDlol').each(function () {
        initPlayer(this);
    });
}

let mediaViewerOpen = false;
let mediaViewerState = {players: [], sourcePlayer: null, viewerPlayer: null, resumeTime: 0, sourceWasPlaying: false};

function pauseBackgroundMedia(sourcePlayer, sourceWasPlaying) {
    mediaViewerOpen = true;
    mediaViewerState.players = [];
    mediaViewerState.sourcePlayer = sourcePlayer || null;
    mediaViewerState.viewerPlayer = null;
    mediaViewerState.resumeTime = sourcePlayer ? (sourcePlayer.currentTime() || 0) : 0;
    mediaViewerState.sourceWasPlaying = sourcePlayer
        ? (typeof sourceWasPlaying === 'boolean' ? sourceWasPlaying : !sourcePlayer.paused())
        : false;

    Object.values(videojs.getPlayers()).forEach(function (player) {
        if (!player || player.isDisposed() || player.el()?.closest('#modalContent')) return;
        if (!player.paused()) mediaViewerState.players.push(player);
        player.pause();
    });
}

function restoreBackgroundMedia() {
    const viewer = mediaViewerState.viewerPlayer;
    if (viewer && !viewer.isDisposed()) mediaViewerState.resumeTime = viewer.currentTime() || mediaViewerState.resumeTime;

    $('#modalContent .video-js').each(function () {
        const player = videojs.getPlayer(this.id);
        if (player && !player.isDisposed()) player.dispose();
    });
    $('#modalContent').empty();

    const source = mediaViewerState.sourcePlayer;
    if (source && !source.isDisposed()) {
        if (mediaViewerState.resumeTime > 0) source.currentTime(mediaViewerState.resumeTime);
        if (mediaViewerState.sourceWasPlaying) {
            const playPromise = source.play();
            if (playPromise?.catch) playPromise.catch(function () {});
        }
    } else {
        mediaViewerState.players.forEach(function (player) {
            if (!player.isDisposed()) {
                const playPromise = player.play();
                if (playPromise?.catch) playPromise.catch(function () {});
            }
        });
    }

    mediaViewerOpen = false;
    mediaViewerState = {players: [], sourcePlayer: null, viewerPlayer: null, resumeTime: 0, sourceWasPlaying: false};
}

function installManifestQualityMenu(player, masterUrl) {
    if (!masterUrl || player.el().querySelector('.feed-quality-select')) return;

    fetch(masterUrl, {cache: 'no-store'})
        .then(function (response) {
            if (!response.ok) throw new Error('Could not load the video manifest.');
            return response.text();
        })
        .then(function (manifest) {
            const lines = manifest.split(/\r?\n/).map(function (line) { return line.trim(); });
            const variants = [];
            lines.forEach(function (line, index) {
                if (!line.startsWith('#EXT-X-STREAM-INF:')) return;
                const heightMatch = line.match(/RESOLUTION=\d+x(\d+)/i);
                const nameMatch = line.match(/NAME="?([^",]+)"?/i);
                const uri = lines.slice(index + 1).find(function (item) { return item && !item.startsWith('#'); });
                if (uri) variants.push({label: nameMatch?.[1] || (heightMatch ? heightMatch[1] + 'p' : 'Quality'), uri: uri, height: Number(heightMatch?.[1] || 0)});
            });
            if (!variants.length) return;

            variants.sort(function (a, b) { return b.height - a.height; });
            const select = document.createElement('select');
            select.className = 'feed-quality-select';
            select.setAttribute('aria-label', 'Video quality');
            select.title = 'Video quality';
            select.add(new Option('Auto', masterUrl));
            variants.forEach(function (variant) { select.add(new Option(variant.label, new URL(variant.uri, masterUrl).href)); });

            select.addEventListener('change', function () {
                const wasPaused = player.paused();
                const currentTime = player.currentTime() || 0;
                player.src({src: this.value, type: 'application/x-mpegURL'});
                player.one('loadedmetadata', function () {
                    if (currentTime > 0 && currentTime < player.duration()) player.currentTime(currentTime);
                    if (!wasPaused) {
                        const playPromise = player.play();
                        if (playPromise?.catch) playPromise.catch(function () {});
                    }
                });
            });
            player.el().appendChild(select);
        })
        .catch(function (error) { console.error('Quality menu error:', error); });
}

initializeFeedVideos(document);

function initPlayer(videoElement) {
        if (!videoElement || videoElement.dataset.feedPlayerReady === '1') return;
        videoElement.dataset.feedPlayerReady = '1';
        videoElement.muted = true;
        videoElement.defaultMuted = true;
        videoElement.setAttribute('muted', '');
        videoElement.setAttribute('autoplay', '');
        videoElement.setAttribute('playsinline', '');

        const player = videojs(videoElement, {
            html5: {vhs: {overrideNative: true}},
            plugins: {
                hlsQualitySelector: {displayCurrentQuality: true}
            },
            autoplay: 'muted',
            muted: true,
            controls: true,
            preload: 'auto',
            playsinline: true,
            playbackRates: [0.5, 1, 1.5, 2], 
            language: 'ar', 
    });

    player.ready(function () {
        this.muted(true);
        this.autoplay(true);
        this.addClass('feed-quality-ready');
        const source = videoElement.querySelector('source');
        installManifestQualityMenu(this, source ? source.src : this.currentSrc());
        const rect = this.el().getBoundingClientRect();
        if (rect.bottom > 0 && rect.top < window.innerHeight) {
            const playPromise = this.play();
            if (playPromise?.catch) playPromise.catch(function () {});
        }
    });

    player.el().addEventListener('pointerdown', function () {
        videoElement.dataset.viewerWasPlaying = player.paused() ? '0' : '1';
    }, true);

    player.on('click', function (event) {
        if (player.el().closest('#modalContent')) return;
        if ($(event.target).closest('.vjs-control-bar, .feed-quality-select').length) return;
        event.preventDefault();
        event.stopPropagation();
        const videoId = String(videoElement.id || '').replace('video_', '').replace('_html5_api', '');
        if (videoId) showvideo(videoId, player, videoElement.dataset.viewerWasPlaying === '1');
    });

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting && entry.intersectionRatio >= 0.6 && (!mediaViewerOpen || player.el().closest('#modalContent'))) {
                    player.muted(true);
                    const playPromise = player.play();
                    if (playPromise && typeof playPromise.catch === 'function') playPromise.catch(function () {});
                } else if (!entry.isIntersecting || entry.intersectionRatio < 0.25) {
                    player.pause();
                }
            });
        }, {threshold: [0.25, 0.6]});
        observer.observe(player.el());
        player.on('dispose', function () { observer.disconnect(); });
    }

    player.on('error', function () {
        console.error('Video.js Error:', player.error());
    });
    player.on('error', function (e) {
        console.error('حدث خطأ أثناء تشغيل الفيديو:', e);
    });
}

function showphoto(urlphoto) {
    pauseBackgroundMedia(null);
    var url = ASSETS + "photo/" + urlphoto;
    $('#modalContent').html("جاري التحميل...");
    $.get(url, function (response) {
        var wrapper = $('<div>').html(response);
        var content = wrapper.find('#fullspace').html();
        $('#modalContent').html(content);
        $('#showphoto').modal('show');
    }).fail(function () {
        restoreBackgroundMedia();
        alert('تعذر فتح الصورة.');
    });
}


  function showvideo(urlvideo, sourcePlayer, sourceWasPlaying){
    if ($('#showphoto').hasClass('show')) return;
    pauseBackgroundMedia(sourcePlayer || null, sourceWasPlaying);
    var url = ASSETS + "video/" + urlvideo;
        $('#modalContent').html("جاري التحميل...");
        $.get(url, function (response) {
            var $vid = $('<div>').html(response).find('#fullspace');
            var $viewerVideo = $vid.find('.VDlol').first();
            $viewerVideo.attr('id', 'viewer_video_' + urlvideo).removeAttr('data-setup');
            $('#modalContent').html($vid.html());
            $('#showphoto').modal('show');
            const viewerElement = document.getElementById('viewer_video_' + urlvideo);
            if (viewerElement) {
                initPlayer(viewerElement);
                const viewerPlayer = videojs.getPlayer(viewerElement.id);
                mediaViewerState.viewerPlayer = viewerPlayer;
                viewerPlayer.ready(function () {
                    if (mediaViewerState.resumeTime > 0) this.currentTime(mediaViewerState.resumeTime);
                    this.muted(sourcePlayer ? sourcePlayer.muted() : true);
                    const playPromise = this.play();
                    if (playPromise?.catch) playPromise.catch(function () {});
                });
            }
        }).fail(function () {
            restoreBackgroundMedia();
            alert('تعذر فتح الفيديو.');
        });
    }

$(document).off('hidden.bs.modal.mediaViewer', '#showphoto').on('hidden.bs.modal.mediaViewer', '#showphoto', restoreBackgroundMedia);

// ===== Notifications System =====
function renderNotifications(data) {
    const $badge = $('#notification-badge');
    const $list = $('#notifications-list');
    const $markAllBtn = $('#mark-all-notifications-read');

    if (data && data.unread_count > 0) {
        $badge.text(data.unread_count > 99 ? '99+' : data.unread_count).show();
        $markAllBtn.show();
    } else {
        $badge.hide();
        $markAllBtn.hide();
    }

    if (!data || !data.notifications || data.notifications.length === 0) {
        $list.html('<div class="popup-item text-muted"><span class="popup-item-icon"><i class="far fa-bell"></i></span><span>لا توجد إشعارات جديدة</span></div>');
        return;
    }

    let html = '';
    data.notifications.forEach(function(n) {
        const itemData = n.data || {};
        const isRead = n.is_read;
        const bgStyle = isRead ? '' : 'background-color: rgba(13, 110, 253, 0.08);';
        const avatar = itemData.actor_avatar || '{{ asset("img/Default_avatar_profile.jpg") }}';
        const message = itemData.message || 'إشعار جديد';
        const actorName = itemData.actor_name || '';
        const url = itemData.url || '#';
        const timeAgo = n.time_ago || '';

        html += `
            <a href="${url}" class="popup-item text-decoration-none d-flex align-items-center gap-2 notification-entry ${isRead ? '' : 'fw-bold'}" data-id="${n.id}" style="${bgStyle} padding: 8px 12px; border-bottom: 1px solid rgba(0,0,0,0.05);">
                <img src="${avatar}" width="38" height="38" class="rounded-circle object-fit-cover" alt="">
                <div class="flex-grow-1" style="line-height: 1.3;">
                    <div class="text-dark fs-13px"><strong>${actorName}</strong> ${message}</div>
                    <small class="text-muted fs-11px">${timeAgo}</small>
                </div>
                ${isRead ? '' : '<span class="badge bg-primary rounded-circle p-1" style="width:8px;height:8px;"></span>'}
            </a>
        `;
    });
    $list.html(html);
}

function fetchNotifications() {
    @auth
    $.ajax({
        url: '/notifications',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            renderNotifications(res);
        }
    });
    @endauth
}

$(document).on('click', '.notification-entry', function(e) {
    const notifId = $(this).data('id');
    if (notifId) {
        $.ajax({
            url: '/notifications/' + notifId + '/read',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
    }
});

$(document).on('click', '#mark-all-notifications-read', function(e) {
    e.stopPropagation();
    $.ajax({
        url: '/notifications/read-all',
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function() {
            fetchNotifications();
        }
    });
});

// ==================== Phase 3: Stories, Share, and Blocking Scripts ====================
let allStoriesData = [];
let currentGroupIndex = 0;
let currentStoryGroup = null;
let currentStoryIndex = 0;
let storyProgressInterval = null;
let isStoryPaused = false;

function loadStories() {
    if (!$('#storiesItems').length) return;
    $.ajax({
        url: '/stories',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status && res.data) {
                allStoriesData = res.data;
                renderStoriesTray(res.data);
            }
        }
    });
}

function updateStoryScrollButtons() {
    let tray = document.getElementById('storiesTray');
    if (!tray) return;
    let maxScroll = tray.scrollWidth - tray.clientWidth;
    if (maxScroll <= 10) {
        $('#btnScrollStoriesLeft, #btnScrollStoriesRight').addClass('d-none');
        return;
    }
    let scrollPos = Math.abs(tray.scrollLeft);
    if (scrollPos <= 15) {
        $('#btnScrollStoriesLeft').addClass('d-none');
    } else {
        $('#btnScrollStoriesLeft').removeClass('d-none');
    }
    if (scrollPos >= maxScroll - 15) {
        $('#btnScrollStoriesRight').addClass('d-none');
    } else {
        $('#btnScrollStoriesRight').removeClass('d-none');
    }
}

$(document).on('click', '#btnScrollStoriesLeft', function() {
    let tray = document.getElementById('storiesTray');
    if (tray) {
        tray.scrollBy({ left: -240, behavior: 'smooth' });
        setTimeout(updateStoryScrollButtons, 320);
    }
});

$(document).on('click', '#btnScrollStoriesRight', function() {
    let tray = document.getElementById('storiesTray');
    if (tray) {
        tray.scrollBy({ left: 240, behavior: 'smooth' });
        setTimeout(updateStoryScrollButtons, 320);
    }
});

$('#storiesTray').on('scroll', updateStoryScrollButtons);

function renderStoriesTray(groups) {
    let html = '';
    groups.forEach((group, index) => {
        let firstStory = group.stories[0];
        let bgStyle = '';
        let innerPreview = '';
        if (firstStory.type === 'text') {
            bgStyle = `background: ${firstStory.background || 'linear-gradient(135deg, #4f46e5, #06b6d4)'};`;
            let snippet = firstStory.content ? (firstStory.content.length > 40 ? firstStory.content.substring(0, 40) + '...' : firstStory.content) : '';
            innerPreview = `<div class="p-2 text-center text-white fw-bold d-flex align-items-center justify-content-center" style="position: absolute; inset: 0; font-size: 11px; z-index: 1; overflow: hidden; word-break: break-word;">${snippet}</div>`;
        } else if (firstStory.type === 'image' && firstStory.media_url) {
            bgStyle = `background-image: url('${firstStory.media_url}');`;
        } else if (firstStory.type === 'video' && firstStory.media_url) {
            bgStyle = `background: #000;`;
            innerPreview = `<video src="${firstStory.media_url}" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;"></video>`;
        }

        html += `
            <div class="card story-card story-user-card shadow-sm"
                 style="${bgStyle}"
                 data-group-index="${index}">
                ${innerPreview}
                <div class="story-author-badge">
                    <img src="${group.user_avatar}" alt="${group.user_name}">
                </div>
                <div class="story-card-author-name">
                    ${group.user_name}
                </div>
            </div>
        `;
    });
    $('#storiesItems').html(html);
    setTimeout(updateStoryScrollButtons, 100);
}

// Story background selection
$(document).on('click', '.story-bg-option', function() {
    let bg = $(this).data('bg');
    $('#storyTextPreview').css('background', bg).data('selected-bg', bg);
});

// Story text live preview
$(document).on('input', '#storyTextInput', function() {
    let text = $(this).val();
    $('#storyTextPreview').text(text.trim() || 'اكتب شيئاً...');
});

// Story media live preview
$(document).on('change', '#storyMediaInput', function() {
    let file = this.files[0];
    if (file) {
        let previewUrl = URL.createObjectURL(file);
        let preview = file.type.startsWith('video/')
            ? '<video class="img-fluid rounded-3" style="max-height:250px" controls muted><source src="' + previewUrl + '" type="' + file.type + '"></video>'
            : '<img src="' + previewUrl + '" class="img-fluid rounded-3" style="max-height:250px" alt="معاينة القصة">';
        $('#storyMediaPreview').html(preview).removeClass('d-none');
    }
});

// Publish Story
$(document).on('click', '#btnPublishStory', function() {
    let isText = $('#text-story-tab').hasClass('active') || $('#text-story-pane').hasClass('active') || $('#text-story-pane').hasClass('show');
    let formData = new FormData();
    let token = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
    formData.append('_token', token);

    if (isText) {
        let content = $('#storyTextInput').val().trim();
        if (!content) {
            alert('يرجى كتابة نص القصة.');
            return;
        }
        let bg = $('#storyTextPreview').data('selected-bg') || 'linear-gradient(135deg, #4f46e5, #06b6d4)';
        formData.append('type', 'text');
        formData.append('content', content);
        formData.append('background', bg);
    } else {
        let file = document.getElementById('storyMediaInput').files[0];
        if (!file) {
            alert('يرجى اختيار صورة أو فيديو.');
            return;
        }
        let isVideo = file.type.startsWith('video/');
        formData.append('type', isVideo ? 'video' : 'image');
        formData.append('media', file);
        let caption = $('#storyMediaCaption').val().trim();
        if (caption) formData.append('content', caption);
    }

    let $btn = $(this);
    $btn.prop('disabled', true).text('جاري النشر...');

    $.ajax({
        url: '/stories',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        dataType: 'json',
        success: function(res) {
            const storyModalElement = document.getElementById('createStoryModal');
            if (window.bootstrap && storyModalElement) {
                bootstrap.Modal.getOrCreateInstance(storyModalElement).hide();
            } else {
                $('#createStoryModal').modal('hide');
            }
            $('#storyTextInput').val('');
            $('#storyMediaInput').val('');
            $('#storyMediaPreview').addClass('d-none');
            $btn.prop('disabled', false).text('نشر القصة');
            loadStories();
        },
        error: function(xhr) {
            $btn.prop('disabled', false).text('نشر القصة');
            let errMsg = 'حدث خطأ أثناء نشر القصة.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errMsg = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                errMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
            } else if (xhr.status === 419) {
                errMsg = 'انتهت صلاحية الجلسة، يرجى تحديث الصفحة (Ctrl + F5).';
            }
            alert(errMsg);
        }
    });
});

// Open Story Lightbox
$(document).on('click', '.story-user-card', function() {
    let groupIndex = parseInt($(this).data('group-index'));
    openStoryGroup(groupIndex, 0);
});

function openStoryGroup(groupIndex, storyIndex = 0) {
    if (groupIndex < 0 || groupIndex >= allStoriesData.length) return;
    currentGroupIndex = groupIndex;
    currentStoryGroup = allStoriesData[currentGroupIndex];
    if (!currentStoryGroup || !currentStoryGroup.stories.length) return;

    currentStoryIndex = storyIndex;
    renderStorySegments();
    renderCurrentStory();
    $('#viewStoryModal').modal('show');
}

function renderStorySegments() {
    if (!currentStoryGroup) return;
    let segHtml = '';
    currentStoryGroup.stories.forEach((s, idx) => {
        segHtml += `
            <div class="story-progress-bar-bg">
                <div class="story-progress-bar-fill" id="storyProgress_${idx}"></div>
            </div>
        `;
    });
    $('#storyProgressSegments').html(segHtml);
}

function renderCurrentStory() {
    clearInterval(storyProgressInterval);
    isStoryPaused = false;
    if (!currentStoryGroup) return;

    let stories = currentStoryGroup.stories;
    if (currentStoryIndex < 0 || currentStoryIndex >= stories.length) return;

    let story = stories[currentStoryIndex];
    $('#modalStoryAuthorAvatar').attr('src', currentStoryGroup.user_avatar);
    $('#modalStoryAuthorName').text(currentStoryGroup.user_name);
    $('#modalStoryTime').text(story.time_ago || '');

    const currentAuthId = {{ auth()->id() ?? 0 }};
    if (currentStoryGroup.user_id === currentAuthId) {
        $('#btnDeleteCurrentStory').removeClass('d-none').data('story-id', story.id);
        $('#storyReactionsTray').addClass('d-none');
    } else {
        $('#btnDeleteCurrentStory').addClass('d-none');
        $('#storyReactionsTray').removeClass('d-none');
    }

    // Update progress bars state
    for (let i = 0; i < stories.length; i++) {
        let $bar = $(`#storyProgress_${i}`);
        if (i < currentStoryIndex) {
            $bar.css('width', '100%');
        } else if (i > currentStoryIndex) {
            $bar.css('width', '0%');
        } else {
            $bar.css('width', '0%');
        }
    }

    let contentHtml = '';
    if (story.type === 'text') {
        contentHtml = `
            <div class="w-100 h-100 d-flex align-items-center justify-content-center p-4 text-center fw-bold fs-3 text-white"
                 style="background: ${story.background || 'linear-gradient(135deg, #4f46e5, #06b6d4)'}; min-height: 100%; word-break: break-word; user-select: none;">
                ${story.content}
            </div>
        `;
    } else if (story.type === 'image') {
        contentHtml = `
            <div class="d-flex flex-column align-items-center justify-content-center w-100 h-100 position-relative">
                <img src="${story.media_url}" style="max-height: 520px; max-width: 100%; object-fit: contain;" alt="">
                ${story.content ? `<div class="position-absolute bottom-0 start-0 end-0 text-white bg-dark bg-opacity-50 p-2 text-center small">${story.content}</div>` : ''}
            </div>
        `;
    } else if (story.type === 'video') {
        contentHtml = `
            <div class="d-flex flex-column align-items-center justify-content-center w-100 h-100 position-relative">
                <video id="currentStoryVideo" src="${story.media_url}" style="max-height: 520px; max-width: 100%; object-fit: contain;" autoplay playsinline></video>
                ${story.content ? `<div class="position-absolute bottom-0 start-0 end-0 text-white bg-dark bg-opacity-50 p-2 text-center small">${story.content}</div>` : ''}
            </div>
        `;
    }
    $('#modalStoryContentArea').html(contentHtml);

    // Progress bar auto-advance animation
    let duration = 5000; // 5 seconds
    let elapsed = 0;
    const step = 40;

    let videoElem = document.getElementById('currentStoryVideo');
    if (videoElem) {
        videoElem.onloadedmetadata = function() {
            if (videoElem.duration && !isNaN(videoElem.duration)) {
                duration = videoElem.duration * 1000;
            }
        };
    }

    storyProgressInterval = setInterval(() => {
        if (!isStoryPaused) {
            elapsed += step;
            let pct = Math.min(100, (elapsed / duration) * 100);
            $(`#storyProgress_${currentStoryIndex}`).css('width', pct + '%');

            if (elapsed >= duration) {
                clearInterval(storyProgressInterval);
                advanceStory(1);
            }
        }
    }, step);
}

function advanceStory(direction) {
    clearInterval(storyProgressInterval);
    if (!currentStoryGroup) return;

    let nextIndex = currentStoryIndex + direction;
    if (nextIndex >= 0 && nextIndex < currentStoryGroup.stories.length) {
        currentStoryIndex = nextIndex;
        renderCurrentStory();
    } else if (direction > 0 && nextIndex >= currentStoryGroup.stories.length) {
        // Move to next user story group
        let nextGroup = currentGroupIndex + 1;
        if (nextGroup < allStoriesData.length) {
            openStoryGroup(nextGroup, 0);
        } else {
            $('#viewStoryModal').modal('hide');
        }
    } else if (direction < 0 && nextIndex < 0) {
        // Move to previous user story group
        let prevGroup = currentGroupIndex - 1;
        if (prevGroup >= 0) {
            openStoryGroup(prevGroup, allStoriesData[prevGroup].stories.length - 1);
        } else {
            renderCurrentStory();
        }
    }
}

// Navigation tap zones & buttons
$(document).on('click', '#storyTapPrev, #btnStoryPrev', function(e) {
    e.stopPropagation();
    advanceStory(-1);
});

$(document).on('click', '#storyTapNext, #btnStoryNext', function(e) {
    e.stopPropagation();
    advanceStory(1);
});

// Press and hold to pause story
$(document).on('mousedown touchstart', '#modalStoryContentArea', function() {
    isStoryPaused = true;
    let vid = document.getElementById('currentStoryVideo');
    if (vid) vid.pause();
});

$(document).on('mouseup mouseleave touchend', '#modalStoryContentArea', function() {
    isStoryPaused = false;
    let vid = document.getElementById('currentStoryVideo');
    if (vid) vid.play();
});

// Quick feedback popup
function showStoryFeedback(msg) {
    let $fb = $('#storyReactionFeedback');
    $fb.text(msg).removeClass('d-none').stop(true, true).fadeIn(150);
    setTimeout(() => {
        $fb.fadeOut(250, function() { $(this).addClass('d-none'); });
    }, 2000);
}

// Quick reactions
$(document).on('click', '.story-quick-react-btn', function() {
    let emoji = $(this).data('emoji');
    let authorId = currentStoryGroup ? currentStoryGroup.user_id : null;
    if (!authorId) return;

    $.ajax({
        url: '/messanger/' + authorId,
        type: 'POST',
        data: {
            text: 'تفاعل مع قصتك: ' + emoji,
            _token: '{{ csrf_token() }}'
        },
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function() {
            showStoryFeedback('تم إرسال التفاعل ' + emoji);
        }
    });
});

// Reply to story via Enter
$(document).on('keypress', '#storyReplyInput', function(e) {
    if (e.which === 13) {
        e.preventDefault();
        let text = $(this).val().trim();
        if (!text) return;
        let authorId = currentStoryGroup ? currentStoryGroup.user_id : null;
        if (!authorId) return;

        let $input = $(this);
        $input.val('').prop('disabled', true);

        $.ajax({
            url: '/messanger/' + authorId,
            type: 'POST',
            data: {
                text: 'رد على قصتك: ' + text,
                _token: '{{ csrf_token() }}'
            },
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function() {
                $input.prop('disabled', false).focus();
                showStoryFeedback('تم إرسال الرد بنجاح');
            },
            error: function() {
                $input.prop('disabled', false);
            }
        });
    }
});

// Delete Story
$('#btnDeleteCurrentStory').on('click', function() {
    let storyId = $(this).data('story-id');
    if (!storyId || !confirm('هل أنت متأكد من حذف هذه القصة؟')) return;

    $.ajax({
        url: '/stories/' + storyId,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function() {
            $('#viewStoryModal').modal('hide');
            loadStories();
        }
    });
});

$('#viewStoryModal').on('hidden.bs.modal', function() {
    clearInterval(storyProgressInterval);
    isStoryPaused = false;
    $('#modalStoryContentArea').empty();
    $('#storyProgressSegments').empty();
});

// ==================== Share Post Script ====================
$(document).on('click', '.btn-share-post', function(e) {
    e.preventDefault();
    let postId = $(this).data('post-id');
    let author = $(this).data('author');
    let snippet = $(this).data('snippet');

    $('#shareTargetPostId').val(postId);
    $('#sharePostAuthorPreview').text(author);
    $('#sharePostSnippetPreview').text(snippet || '');
    $('#sharePostCaption').val('');
    $('#sharePostModal').modal('show');
});

$(document).on('click', '#btnConfirmSharePost', function() {
    let postId = $('#shareTargetPostId').val();
    let caption = $('#sharePostCaption').val();
    let visibility = $('#sharePostVisibility').val();

    if (!postId) return;

    let $btn = $(this);
    $btn.prop('disabled', true).text('جاري المشاركة...');

    $.ajax({
        url: '/post/' + postId + '/share',
        type: 'POST',
        data: {
            post_text: caption,
            visibility: visibility,
            _token: '{{ csrf_token() }}'
        },
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(res) {
            $('#sharePostModal').modal('hide');
            $btn.prop('disabled', false).html('<i class="fa fa-share me-1"></i> مشاركة الآن');
            alert('تمت مشاركة المنشور بنجاح على صفحتك!');
            window.location.reload();
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fa fa-share me-1"></i> مشاركة الآن');
            alert(xhr.responseJSON?.message || 'تعذر مشاركة هذا المنشور.');
        }
    });
});

// ==================== Edit & Delete Post Script ====================
$(document).on('click', '.select-edit-audience', function() {
    let val = $(this).data('val');
    let icon = $(this).data('icon');
    let text = $(this).text().trim();
    $('#editPostVisibilityInput').val(val);
    $('#editAudienceIcon').attr('class', 'fa ' + icon + ' me-1');
    $('#editAudienceLabel').text(text);
});

$(document).on('click', '.btn-edit-post', function(e) {
    e.preventDefault();
    let postId = $(this).data('post-id');
    let text = $(this).data('post-text') || '';
    let visibility = $(this).data('visibility') || 'public';

    $('#editPostId').val(postId);
    $('#editPostTextInput').val(text);
    $('#editPostVisibilityInput').val(visibility);

    let visIcons = {
        'public': { icon: 'fa-globe-americas', label: 'العامة' },
        'friends': { icon: 'fa-user-friends', label: 'الأصدقاء فقط' },
        'only_me': { icon: 'fa-lock', label: 'أنا فقط' }
    };
    let currentVis = visIcons[visibility] || visIcons['public'];
    $('#editAudienceIcon').attr('class', 'fa ' + currentVis.icon + ' me-1');
    $('#editAudienceLabel').text(currentVis.label);

    $('#editPostModal').modal('show');
});

$(document).on('click', '#btnSaveEditedPost', function() {
    let postId = $('#editPostId').val();
    let text = $('#editPostTextInput').val().trim();
    let visibility = $('#editPostVisibilityInput').val();

    if (!postId) return;

    let $btn = $(this);
    $btn.prop('disabled', true).text('جاري الحفظ...');

    $.ajax({
        url: '/post/' + postId + '/update',
        type: 'POST',
        data: {
            post_text: text,
            visibility: visibility,
            _token: '{{ csrf_token() }}'
        },
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(res) {
            $btn.prop('disabled', false).text('حفظ التعديلات');
            $('#editPostModal').modal('hide');

            // Update post in DOM
            $('.post-text-content-' + postId).text(text);

            let visIcons = {
                'public': 'fa-globe-americas',
                'friends': 'fa-user-friends',
                'only_me': 'fa-lock'
            };
            $('.post-visibility-icon-' + postId).attr('class', 'fa ' + (visIcons[visibility] || 'fa-globe-americas') + ' opacity-5 ms-1 post-visibility-icon-' + postId);

            // Update button data attributes
            $('.btn-edit-post[data-post-id="' + postId + '"]').data('post-text', text).data('visibility', visibility);
        },
        error: function(xhr) {
            $btn.prop('disabled', false).text('حفظ التعديلات');
            alert(xhr.responseJSON?.message || 'حدث خطأ أثناء تعديل المنشور.');
        }
    });
});

$(document).on('click', '.btn-delete-post', function(e) {
    e.preventDefault();
    let postId = $(this).data('post-id');
    if (!postId || !confirm('هل أنت متأكد من حذف هذا المنشور؟ لا يمكن التراجع عن هذا الإجراء.')) return;

    $.ajax({
        url: '/postdelete',
        type: 'POST',
        data: {
            id: postId,
            _token: '{{ csrf_token() }}'
        },
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function() {
            $('.post-card-container-' + postId).fadeOut(350, function() {
                $(this).remove();
            });
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.error || 'حدث خطأ أثناء حذف المنشور.');
        }
    });
});

// ==================== Block User Script ====================
$(document).on('click', '.btn-block-user', function(e) {
    e.preventDefault();
    let userId = $(this).data('user-id');
    let userName = $(this).data('user-name') || 'هذا المستخدم';

    if (!confirm('هل أنت متأكد من رغبتك في حظر ' + userName + '؟ لن تتمكن من رؤية منشوراته أو مراسلته.')) {
        return;
    }

    $.ajax({
        url: '/users/' + userId + '/block',
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(res) {
            alert('تم حظر المستخدم بنجاح.');
            $('a[href="profile/' + userId + '"]').closest('.postes').fadeOut(300, function() {
                $(this).remove();
            });
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'حدث خطأ أثناء حظر المستخدم.');
        }
    });
});

// ==================== Dark Mode Global Functions ====================
function toggleDarkMode(enable) {
    if (enable) {
        $('body').addClass('dark-theme');
        localStorage.setItem('fb_dark_mode', 'true');
        $('#nav-dark-mode-status').text('مفعّل').removeClass('bg-secondary').addClass('bg-success');
        $('#settingsDarkModeSwitch').prop('checked', true);
    } else {
        $('body').removeClass('dark-theme');
        localStorage.setItem('fb_dark_mode', 'false');
        $('#nav-dark-mode-status').text('إيقاف').removeClass('bg-success').addClass('bg-secondary');
        $('#settingsDarkModeSwitch').prop('checked', false);
    }
}

function initDarkMode() {
    let saved = localStorage.getItem('fb_dark_mode');
    @auth
    let dbPref = {{ auth()->user()->dark_mode ? 'true' : 'false' }};
    if (saved === null) {
        saved = dbPref ? 'true' : 'false';
    }
    @endauth
    toggleDarkMode(saved === 'true');
}

$(document).on('click', '#nav-toggle-dark-mode', function() {
    let isCurrentlyDark = $('body').hasClass('dark-theme');
    let newState = !isCurrentlyDark;
    toggleDarkMode(newState);

    @auth
    $.ajax({
        url: '/settings/preferences',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            dark_mode: newState ? 1 : 0
        },
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    @endauth
});

$(document).ready(function() {
    initDarkMode();
    @auth
    fetchNotifications();
    setInterval(fetchNotifications, 30000);
    loadStories();
    @endauth
});

</script>
