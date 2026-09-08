
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
    const ASSETS = "{{ asset('') }}";
    
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
            console.log(error,status,xhr);
            console.error('Error:', error);
        }
    });
            
        }

$(document).on('click', '.contact-chat', function (e) {
    e.preventDefault();
    const id = $(this).data('chat-id');
    $('#mini-chat').removeClass('d-none').data('chat-id', id);
    $('#mini-chat-name').text($(this).data('chat-name'));
    $('#mini-chat-messages').html('<div class="text-muted small text-center">جاري التحميل...</div>');
    $.get('{{ url('/messanger') }}/' + id, function (messages) {
        const box = $('#mini-chat-messages').empty();
        if (!messages.length) box.html('<div class="text-muted small text-center">ابدأ المحادثة</div>');
        messages.forEach(function (m) { box.append('<div class="mini-chat-message ' + (m.my_id == {{ Auth::id() ?? 0 }} ? 'mine' : '') + '">' + $('<div>').text(m.message).html() + '</div>'); });
        box.scrollTop(box[0].scrollHeight);
    });
});
$(document).on('click', '#mini-chat-close', function () { $('#mini-chat').addClass('d-none'); });
$(document).on('click', '.reaction-count', function (e) {
    e.preventDefault();
    $.get('{{ url('/post') }}/' + $(this).data('post-id') + '/reactions', function (items) {
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
    $.post('{{ url('/saved') }}/' + button.data('post-id'), {_token:'{{ csrf_token() }}'}, function (data) {
        button.find('div > div:first').text(data.saved ? 'Saved' : 'Save Post');
        button.find('small').text(data.saved ? 'This post is in your saved items' : 'Add this to your saved items');
    });
});
$(document).on('change', '.reaction-type', function () { $(this).closest('.like').attr('data-type_id', $(this).val()); });
$(document).on('submit', '#mini-chat-form', function (e) {
    e.preventDefault();
    const input = $('#mini-chat-input'), message = input.val().trim(), id = $('#mini-chat').data('chat-id');
    if (!message || !id) return;
    $.post('{{ url('/messanger') }}/' + id, {_token: '{{ csrf_token() }}', text: message}, function () {
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


    
$("#PostSubmit2").on('click',function(){

        $("#modal-dialog").modal('hide');
        $("#modal-dialog2").modal('show');
});
$("#PostSubmit").on('click',function(){
        var formData = new  FormData($("#myForm")[0]);

        AjaxReqForAll(ASSETS+"posts","POST",formData,"Post");
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
$(".dropzoneDiv").each(function() {
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
        var fileInput = document.querySelector("#fileInput");
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
          var  imgsarray=[];
            const dataTransfer = new DataTransfer();
            // إضافة الملفات من Dropzone إلى DataTransfer
            dropzoneInstance.files.forEach(file => {
                dataTransfer.items.add(file);
                imgsarray.push(file)   ;

            });
       
            fileInput2.files = dataTransfer.files;
         
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
        var user_id = $(this).data('user-id');
        var type_id = $(this).data('type_id');
        var liked = $(this).data("liked");
        
        var csrfToken = "{{ csrf_token() }}";
        var form = new FormData();
        
        if (!liked) {
            $(this).data('liked', true);
            $(this).find('#like').addClass('text-blue-400').removeClass('text-gray-400');
        } else {
            $(this).data('liked', false);
            $(this).find('#like').addClass('text-gray-400').removeClass('text-blue-400');
        }
        var assets = "{{ asset('') }}";
        form.append('post_id', post_id);
        form.append('user_id', user_id);
        form.append('type_id', type_id);
        form.append('liked', liked);
        form.append('_token', csrfToken);
        form.append('photo_id', photo_id);
        //console.log(post_id,liked,user_id,type_id,photo_id);
        if(post_id==null||post_id==undefined){
            AjaxReqForAll(assets+"photo/like","POST",form,"ReactionPost");
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
            var assets = "{{ asset('') }}";
        if ((e.key === 'Enter' || e.keyCode === 13 )&&comment.length>0) {
            e.preventDefault();
            form.append('_token', csrfToken);
            form.append('post_id', post_id);
            form.append('comment', comment);
            form.append('photo_id', photo_id);
            if(post_id==null||post_id==undefined){
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
        $('#loading').show();
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
            //console.log(response);
            if (data.success=="ok") {
             // console.log(data.data);    
                $("#modal-dialog").modal('hide');
                $("#modal-dialog2").modal('hide');
                AjaxReqForAll("post/"+data.data.id,"GET",false,"PostRes");
            } else if(data.success=="failed"){
           // console.log(data.data.id);    
        }else {
            console.error('خطأ:', data.error);
            console.error('التفاصيل:', data.details);
            alert('فشل العملية: ' + data.details); // إظهار رسالة الخطأ للمستخدم
        }
           //console.log(data.id);
            break;
        case "PostRes":
            var post= $(data).find("#Post_ONE");
         var  lol= $(post).removeAttr("#Post_ONE");
            $("#allpost").prepend(`<div class='postes' >${post.html()}</div>`);
            break;
        case "Postreload":
            var postes= $(data).find("#allpost");
            var lol=postes.children();
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

var videos = $('.VDlol');
 var click2ice=0;
videos.each(function(index, element) {
    initPlayer(element);
});

function initPlayer(videoElement) {
   
    
        const player = videojs(videoElement, {
            plugins: {
                hlsQualitySelector: {}
            },
            autoplay: false,
            controls: true,
            preload: true,
            playbackRates: [0.5, 1, 1.5, 2], 
            language: 'ar', 
    });

    player.ready(function () {
        this.hlsQualitySelector({
            displayCurrentQuality: true
        });
    });

    player.on('error', function () {
        console.error('Video.js Error:', player.error());
    });
    var    click2ice=true;
    player.on('play', function () {
        click2ice=false;
        $(videoElement).on('click',function (event){
        click2ice=true;
        if(click2ice==true){
        var videoid=  event.currentTarget.id.replace('video_','');
           // console.log(videoid.replace('_html5_api',''))
         showvideo(videoid.replace('_html5_api',''));
        }
            });
        
    });
   // click2ice=1;
   click2ice=true;

    player.on('error', function (e) {
        console.error('حدث خطأ أثناء تشغيل الفيديو:', e);
    });
}

function showphoto(urlphoto) {
    var url = ASSETS + "photo/" + urlphoto;
    $('#modalContent').html("جاري التحميل...");
    $.get(url, function (response) {
        var wrapper = $('<div>').html(response);
        var content = wrapper.find('#fullspace').html();
        $('#modalContent').html(content);
        $('#showphoto').modal('show');
        $('#showphoto').on('shown.bs.modal', function () {
           console.log(wrapper.find('#modal-message').html());
            var message = wrapper.find('#modal-message').html();
            if (message) {
                message.init({
                    autoClose: 5000,
                    type: 'info',
                    position: 'top-right',
                    closeButton: true,
                    showCloseButtonOnHover: true,
                    showIcon: true,
                    iconColor: '#fff',
                    backgroundColor: '#007bff',
                });
            }
        });
    });
}


  function  showvideo(urlvideo){
    var url = ASSETS + "video/" + urlvideo;
        $('#modalContent').html("جاري التحميل...");
        $.get(url, function (response) {
            var vid = $('<div>').html(response).find('#fullspace').html();
            $('#modalContent').html(vid);
            $('#showphoto').modal('show');

            var videos = $('.VDlol');
            videos.each(function(index, element) {
                initPlayer(element);
            });
        });
    }

 



</script>
