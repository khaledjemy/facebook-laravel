@extends('layout.masterhome')
@section('content')
<div id="content" class="  ">
    <div class="messenger" id="messenger">
        <div class="messenger-menu">
            <div class="messenger-menu-item my-2">
                <a href="#" class="messenger-menu-link">
                    <div class="m-n1">
                        <img alt="" src="assets/img/user/user-13.jpg" class="w-100 d-block rounded-circle">
                    </div>
                </a>
            </div>
            <div class="messenger-menu-item active">
                <a href="#" class="messenger-menu-link">
                    <span class="iconify fs-30px" data-icon="solar:dialog-2-bold-duotone"></span>
                </a>
            </div>
            <div class="messenger-menu-item">
                <a href="#" class="messenger-menu-link">
                    <span class="iconify fs-30px" data-icon="solar:notebook-bold-duotone"></span>
                </a>
            </div>
            <div class="messenger-menu-item">
                <a href="#" class="messenger-menu-link">
                    <span class="iconify fs-30px" data-icon="solar:box-minimalistic-bold-duotone"></span>
                </a>
            </div>
            <div class="messenger-menu-item">
                <a href="#" class="messenger-menu-link">
                    <span class="iconify fs-30px" data-icon="solar:folder-with-files-bold-duotone"></span>
                </a>
            </div>
            <div class="messenger-menu-item">
                <a href="#" class="messenger-menu-link">
                    <span class="iconify fs-30px" data-icon="solar:clapperboard-play-bold-duotone"></span>
                </a>
            </div>
            <div class="messenger-menu-item">
                <a href="#" class="messenger-menu-link">
                    <span class="iconify fs-30px" data-icon="solar:settings-bold-duotone"></span>
                </a>
            </div>
        </div>
        <div class="messenger-chat">
            <div class="messenger-chat-header d-flex">
                <div class="flex-1 position-relative">
                    <input type="text" class="form-control border-0 bg-light ps-30px" placeholder="Search">
                    <i class="fa fa-search position-absolute start-0 top-0 h-100 ps-2 ms-3px d-flex align-items-center justify-content-center"></i>
                </div>
                <div class="ps-2">
                    <a href="#" class="btn border-0 bg-light shadow-none">
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
            </div>
            <div class="messenger-chat-body">
                <div data-scrollbar="true" data-height="100%" class="ps ps--active-y" style="height: 100%;">
                    <div class="messenger-chat-list">
                        @if (isset($users))
                        @foreach($users as $contact)
                        <div class="messenger-chat-item {{$contact->id == request()->route('id') ? 'active' : ''}}" data-id="{{$contact->id}}">
                            <a href="{{ url('/messanger/'.$contact->id) }}" class="messenger-chat-link">
                                <div class="messenger-chat-media">
                                    @if($contact->photopro)
                                        <img alt="" src="{{ asset($contact->photopro->path.$contact->photopro->id.$contact->photopro->type) }}">
                                    @else
                                        <img alt="" src="{{ asset('img/Default_avatar_profile.jpg') }}">
                                    @endif
                                </div>
                                <div class="messenger-chat-content">
                                    <div class="messenger-chat-title">
                                        <div class="messenger-chat-name"> {{$contact->first_name}} {{$contact->last_name}}</div>
                                    </div>
                                    <div class="messenger-chat-desc">{{ __('ui.chat') }}</div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="messenger-content">
            <div class="widget-chat">
                <!-- BEGIN widget-chat-header -->
                <div class="widget-chat-header">
                    <div class="d-block d-lg-none">
                        <button type="button" class="mobile-chat-back btn border-0 shadow-none">
                            <i class="fa fa-chevron-left fa-lg"></i>
                        </button>
                    </div>
                    <div class="widget-chat-header-content">
                        <div class="fs-6 fw-bold">{{$user->first_name}} {{$user->last_name}}</div>
                    </div>
                    <div class="">
                        <a  href="#" id="callAudio" class="widget-chat-toolbar-link ms-auto">
                            <span class="iconify fs-26px mx-2" data-icon="solar:phone-calling-outline"></span>
                        </a>
                        <a  href="#" id="callVideo" class="widget-chat-toolbar-link">
                            <span class="iconify fs-26px" data-icon="solar:videocamera-record-outline"></span>
                        </a>
                        <button type="button" class="btn border-0 shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis fa-lg"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                            <li>
                                <a class="dropdown-item" href="#">Action</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">Another action</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">Something else here</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- END widget-chat-header -->
                <!-- BEGIN widget-chat-body -->

                <div id="chat" class="widget-chat-body ps ps--active-y overflow-auto" data-scrollbar="true" data-height="100%" style="height: 100%;">
                    @php
                        $reservarr = collect();
                        if (isset($messages) && $messages->count()) {
                            $reservarr = $messages->reverse();
                        }
                    @endphp
                    
                    @foreach($reservarr as $message)
                    <div class="widget-chat-item with-media {{$message->my_id == auth()->id() ? 'end' : 'start'}} msg-{{$message->id}}" >
                        <div class="widget-chat-media">
                            <img alt="" src="{{ $message->sender && $message->sender->photopro ? asset($message->sender->photopro->path.$message->sender->photopro->id.$message->sender->photopro->type) : asset('img/Default_avatar_profile.jpg') }}">
                        </div>
                        <div class="widget-chat-info message_{{$message->id}}" >
                            <div class="widget-chat-info-container">
                                <div  class="widget-chat-name text-indigo">{{$message->sender->first_name}} {{$message->sender->last_name}}</div>
                                <div class="widget-chat-message"> {{$message->message}} </div>
                                @if($message->my_id == auth()->id())
                                    <div class="seen-status">
                                        {{ $message->read ? '✔✔' : '✔' }}
                                    </div>
                                @endif
                                <div class="widget-chat-time">{{ $message->created_at?->format('H:i') }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <!-- END widget-chat-body -->
                <!-- BEGIN widget-input -->
                 
                <div class="widget-chat-input">
                    <div class="widget-chat-toolbar ">
                        <button type="button" id="emojiToggle" class="widget-chat-toolbar-link" aria-label="Emoji">
                            <span class="iconify fs-26px" data-icon="solar:smile-circle-outline"></span>
                        </button>
                        <div id="emojiPicker" class="messenger-emoji-picker" hidden>
                            @foreach(['😀','😂','😍','🥰','😢','😮','😡','👍','❤️','🎉'] as $emoji)
                                <button type="button" class="messenger-emoji">{{$emoji}}</button>
                            @endforeach
                        </div>
                    </div>
                    
                    <textarea id="messageInput"  name="text" class="form-control"></textarea>
                    <input type="hidden" value="{{Auth::id()}}" name="me">
                    <input type="hidden" id="you" value="{{$user->id}}" name="you">

                    <button type="button" id="sendButton" class="btn btn-primary rounded-circle" aria-label="Send"><i class="fa fa-paper-plane"></i></button>
                </div>
                <!-- END widget-input -->
            </div>
        </div>
    </div>
</div>

{{-- نافذة المكالمة --}}
<div id="callModal">
  <div style="
      width:100%;
      height:100%;
      display:flex;
      align-items:center;
      justify-content:center;">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-0">
        <div class="d-flex align-items-center gap-2">
          <img src="assets/img/user/user-2.jpg"
               class="rounded-circle"
               width="40" height="40">
          <div>
            <div class="fw-bold">جاري الاتصال...</div>
            <small class="text-muted">Voice / Video Call</small>
          </div>
        </div>
        <button type="button"  class="btn-close btn-close-white endCall2"
                data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-0 position-relative">
        <video id="remoteVideo" autoplay playsinline
               class="w-100 bg-black" style="height:400px;object-fit:cover;"></video>

        <video id="localVideo" autoplay muted playsinline
               class="position-absolute rounded"
               style="width:120px;height:160px;top:10px;right:10px;object-fit:cover;border:2px solid #fff;"></video>

        <audio id="remoteAudio" autoplay playsinline style="display:none;"></audio>

        <div id="devicePanel"
             class="bg-dark text-white p-3"
             style="display:none;position:absolute;bottom:0;width:100%;border-top:1px solid rgba(255,255,255,.1);">
            <div class="mb-2 fw-bold">إعدادات الأجهزة</div>
            <select id="audioInputSelect" class="form-select mb-2"></select>
            <select id="audioOutputSelect" class="form-select mb-2"></select>
            <select id="videoSelect" class="form-select"></select>
        </div>
      </div>

      <div class="modal-footer border-0 d-flex justify-content-center gap-3">
        <div class="row" >
            <div class="col">
                <button id="toggleMute" class="btn btn-secondary rounded-circle">
                    🎤
                </button>
            </div>
            <div class="col">
                <button id="openAudioPanel" class="btn btn-secondary rounded-circle">
                    🎧
                </button>
            </div>
            <div class="col">
                <button id="switchCamera" class="btn btn-secondary rounded-circle">
                    🔄
                </button>
            </div>
            <div class="col">
                <button id="toggleVideo" class="btn btn-secondary rounded-circle">
                    📹
                </button>
            </div>
      </div>
      <div class="row" >
            <div class="col">
                <button class="btn btn-danger rounded-circle endCall2">
                    📞
                </button>
            </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
let userId = "{{ Auth::id() }}";
let socket;
let localStream;
let peer;
let pendingCandidates = [];
let isRemoteReady = false;

$(document).ready(function(){
    // نقل المودال إلى نهاية body مباشرة لتفادي مشاكل الـ overflow/hidden على الهاتف
    $('#callModal').appendTo('body');
    if (window.matchMedia('(max-width: 991.98px)').matches) {
        $('#messenger').addClass('messenger-chat-content-mobile-toggled');
    }

    $('.mobile-chat-back').on('click', function () {
        $('#messenger').removeClass('messenger-chat-content-mobile-toggled');
    });

    $('#emojiToggle').on('click', function () {
        $('#emojiPicker').prop('hidden', function (_, hidden) { return !hidden; });
    });

    $(document).on('click', '.messenger-emoji', function () {
        $('#messageInput').val($('#messageInput').val() + $(this).text()).trigger('input').focus();
        $('#emojiPicker').prop('hidden', true);
    });

    connectSocket();
    var page = 0;
    var isLoading = false;

    $('#chat').on('scroll', function() {
        var scrollTop = $(this).scrollTop();
        if (scrollTop <= 5 && !isLoading) {
            isLoading = true;
            var csrfToken = "{{ csrf_token() }}";
            page++;
            var user_Id = $('#you').val();
            $.ajax({
                url:"/messanger/"+user_Id,
                type:"GET",
                data:{
                    page: page,
                    userId: user_Id,
                    _token: csrfToken,
                },
                success: function(response) {
                    var $newMessages = $(response).find('#chat .widget-chat-item');
                    $newMessages.each(function() {
                        var msgId = $(this).attr('class').match(/msg-(\d+)/);
                        if (msgId && !$('#chat .msg-' + msgId[1]).length) {
                            $('#chat').prepend($(this));
                        }
                    });
                    let oldHeight = $('#chat')[0].scrollHeight;
                    let newHeight = $('#chat')[0].scrollHeight;
                    $('#chat').scrollTop(newHeight - oldHeight + 20);
                    isLoading = false;
                },
                error: function(xhr, status, error) {
                    console.log("حدث خطأ أثناء معالجة الطلب");
                    isLoading = false;
                }
            });
        }
    });

    $('#endCall').on('click', function(e){
        e.preventDefault();
        socket.send(JSON.stringify({
            type: 'end-call',
            to: $('#you').val(),
            from: userId
        }));
        endCall();
    });

    $('#sendButton').on('click', function(){
        console.log("clicked");
        let text = $('#messageInput').val();
        let to = $('#you').val();
        if(!text) return;

        $.post("/messanger/" + to, {
            text: text,
            me: userId,
            you: to,
            _token: "{{ csrf_token() }}"
        }, function(res){
            socket.send(JSON.stringify({
                type: 'message',
                message: text,
                from: userId,
                to: to,
                id: res.id
            }));
        });

        $('#messageInput').val('');
    });

    let typingTimer;
    let isTyping = false;

    $('#messageInput').on('input', function(){
        if(!isTyping){
            socket.send(JSON.stringify({
                type: 'typing',
                from: userId,
                to: $('#you').val()
            }));
            isTyping = true;
        }
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            isTyping = false;
        }, 1000);
    });

    $('#callAudio').on('click', function(e){
        e.preventDefault();
        socket.send(JSON.stringify({
            type: 'call-request',
            to: $('#you').val(),
            from: userId,
            isVideo: false
        }));
    });

    $('#callVideo').on('click', function(e){
        e.preventDefault();
        socket.send(JSON.stringify({
            type: 'call-request',
            to: $('#you').val(),
            from: userId,
            isVideo: true
        }));
    });

    $('#openAudioPanel').on('click touchstart', function (e) {
        e.preventDefault();
        $('#devicePanel').toggle();
    });

    let isMuted = false;
    $('#toggleMute').on('click', function () {
        isMuted = !isMuted;
        if (localStream) {
            localStream.getAudioTracks().forEach(track => {
                track.enabled = !isMuted;
            });
        }
        $(this).text(isMuted ? '🔇' : '🎤');
    });

    let currentFacing = "user";
    $('#switchCamera').on('click', async function () {
        currentFacing = currentFacing === "user" ? "environment" : "user";
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: currentFacing },
            audio: false
        });
        let newVideoTrack = stream.getVideoTracks()[0];
        let sender = peer.getSenders().find(s => s.track && s.track.kind === 'video');
        if (sender) {
            await sender.replaceTrack(newVideoTrack);
        }
        localStream.getVideoTracks().forEach(t => t.stop());
        localStream.addTrack(newVideoTrack);
        $('#localVideo')[0].srcObject = localStream;
    });

    let videoEnabled = false;
    $('#toggleVideo').on('click', async function () {
        videoEnabled = !videoEnabled;
        if (videoEnabled) {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: false
            });
            let videoTrack = stream.getVideoTracks()[0];
            let sender = peer.getSenders().find(s => s.track && s.track.kind === 'video');
            if (sender) {
                await sender.replaceTrack(videoTrack);
            } else {
                peer.addTrack(videoTrack, localStream);
            }
            localStream.addTrack(videoTrack);
            $('#localVideo')[0].srcObject = localStream;
            $(this).text('📵');
        } else {
            localStream.getVideoTracks().forEach(t => t.stop());
            $(this).text('📹');
        }
    });

    $('#enableVideo').on('click', async function(e){
        e.preventDefault();
        socket.send(JSON.stringify({
            type: 'video-upgrade-request',
            to: $('#you').val(),
            from: userId
        }));
    });
});

function userOnline(id){
    $(`.messenger-chat-item[data-id="${id}"]`).addClass('online');
}

function userOffline(id){
    $(`.messenger-chat-item[data-id="${id}"]`).removeClass('online');
}

function addIceCandidateSafe(candidate) {
    if (peer && peer.remoteDescription) {
        peer.addIceCandidate(new RTCIceCandidate(candidate))
            .catch(err => console.log("ICE error:", err));
    } else {
        pendingCandidates.push(candidate);
    }
}

async function handleOffer(data){
    resetConnection();

    if(!peer){
        peer = new RTCPeerConnection({
            iceServers: [
                { urls: "stun:stun.l.google.com:19302" },
                {
                    urls: "turn:openrelay.metered.ca:80",
                    username: "openrelayproject",
                    credential: "openrelayproject"
                }
            ]
        });
    }

    try {
        localStream = await navigator.mediaDevices.getUserMedia({
            audio: {
                echoCancellation: true,
                noiseSuppression: true,
                autoGainControl: true,
                channelCount: 2,
                sampleRate: 48000
            },
            video: data.isVideo
        });
        await loadDevices();
        if(window.earpieceDeviceId) {
            $('#audioOutputSelect').val(window.earpieceDeviceId).trigger('change');
        }
    } catch(e){
        console.log("getUserMedia error", e);
        return;
    }

    $('#localVideo')[0].srcObject = localStream;
    $('#callModal').fadeIn(150);
    $('body').css('overflow','hidden');

    peer.onicecandidate = e => {
        if(e.candidate){
            socket.send(JSON.stringify({
                type: 'ice',
                candidate: e.candidate,
                to: data.from,
                from: userId
            }));
        }
    };

    localStream.getTracks().forEach(track => {
        peer.addTrack(track, localStream);
    });

    peer.ontrack = (event) => {
        if (event.track.kind === "audio") {
            let audio = document.getElementById("remoteAudio");
            audio.srcObject = event.streams[0];
            audio.muted = false;
            audio.volume = 1;
            audio.play().catch(e => console.log("audio error", e));
        }
        if (event.track.kind === "video") {
            let video = document.getElementById("remoteVideo");
            video.srcObject =  event.streams[0];
            video.play().catch(e => console.log("video error", e));
        }
    };

    await peer.setRemoteDescription(new RTCSessionDescription(data.offer));
    isRemoteReady = true;

    pendingCandidates.forEach(c => {
        peer.addIceCandidate(new RTCIceCandidate(c))
            .catch(err => console.log(err));
    });
    pendingCandidates = [];

    let answer = await peer.createAnswer();
    await peer.setLocalDescription(answer);

    socket.send(JSON.stringify({
        type: 'answer',
        answer: answer,
        to: data.from,
        from: userId
    }));
}

function resetConnection(){
    if(peer){
        peer.ontrack = null;
        peer.onicecandidate = null;
        peer.close();
        peer = null;
    }
    if(localStream){
        localStream.getTracks().forEach(track => track.stop());
    }
    pendingCandidates = [];
}

function markSeen(data){
    let currentChat = $('#you').val();
    if(data.to == userId && data.from == currentChat){
        $.post('/messanger/' + data.from + '/seen', {
            _token: "{{ csrf_token() }}"
        });
        if(data.message_id){
            let msg = $(`.msg-${data.message_id} .seen-status`);
            if(msg.length){
                msg.text('✔✔');
            } else {
                console.log("message not in DOM yet");
            }
        }
    }
}

function appendMessage(data){
    let currentChat = $('#you').val();
    if (
        !(data.from == userId && data.to == currentChat) &&
        !(data.from == currentChat && data.to == userId)
    ) return;

    if($('.msg-'+data.id).length) return;

    let pos = data.from == userId ? 'end' : 'start';
    var name= data.from == userId ? "{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}" : $('.messenger-chat-item.active .messenger-chat-title .messenger-chat-name').text();

    let msg = $(`
        <div class="widget-chat-item with-media ${pos} msg-${data.id}" >
            <div class="widget-chat-media">
                <img alt="" src="assets/img/user/user-2.jpg">
            </div>
            <div class="widget-chat-info message_${data.id}" >
                <div class="widget-chat-info-container">
                    <div  class="widget-chat-name text-indigo">${name}</div>
                    <div class="widget-chat-message"> ${data.message} </div>
                    ${data.from == userId ? `<div class="seen-status">✔</div>` : ''}
                    <div class="widget-chat-time">09:20AM</div>
                </div>
            </div>
        </div>
    `);

    $('#chat').append(msg);
    msg.fadeIn(200);
    $('#chat').animate({
        scrollTop: $('#chat')[0].scrollHeight
    }, 200);
}

async function startCall(isVideo){
    resetConnection();
    
    if(localStream){
        localStream.getTracks().forEach(track => track.stop());
    }

    localStream = await navigator.mediaDevices.getUserMedia({
        audio: {
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true,
            channelCount: 2,
            sampleRate: 48000
        },
        video: isVideo 
    });
    localStream.getAudioTracks()[0].enabled = true;
    $('#localVideo')[0].muted = true;
    $('#remoteVideo')[0].muted = false;

    $('#localVideo')[0].srcObject = localStream;
    await loadDevices(); 
    if(window.earpieceDeviceId) {
        $('#audioOutputSelect').val(window.earpieceDeviceId).trigger('change');
    }
    $('#callModal').fadeIn(150);
    $('body').css('overflow','hidden');

    peer = new RTCPeerConnection({
        iceServers: [
            { urls: "stun:stun.l.google.com:19302" },
            {
                urls: "turn:openrelay.metered.ca:80",
                username: "openrelayproject",
                credential: "openrelayproject"
            }
        ]
    });

    localStream.getTracks().forEach(track => {
        peer.addTrack(track, localStream);
    });

    peer.onicecandidate = e => {
        if(e.candidate){
            socket.send(JSON.stringify({
                type: 'ice',
                candidate: e.candidate,
                to: $('#you').val(),
                from: userId
            }));
        }
    };

    peer.ontrack = (event) => {
        const stream = event.streams[0];
        let video = document.getElementById("remoteVideo");
        video.srcObject = stream;
        video.play().catch(()=>{});
        let audio = document.getElementById("remoteAudio");
        audio.srcObject = stream;
        audio.volume = 1;
        audio.play().catch(()=>{});
    };

    let offer = await peer.createOffer();
    await peer.setLocalDescription(offer);

    socket.send(JSON.stringify({
        type: 'offer',
        offer: offer,
        to: $('#you').val(),
        from: userId,
        isVideo: isVideo
    }));
}

$(document).on('click', '.messenger-chat-link', function(e) {
    e.preventDefault();
    var parent = $(this).closest('.messenger-chat-item');
    $('.messenger-chat-item').removeClass('active');
    parent.addClass('active');
    if (window.matchMedia('(max-width: 991.98px)').matches) {
        $('#messenger').addClass('messenger-chat-content-mobile-toggled');
    }
    $('#chat').animate({
        scrollTop: $('#chat')[0].scrollHeight
    }, 200);
    changechatbox(parent.data("id"));
});

$(document).on('click', '.endCall2', function (e) {
    e.preventDefault();
    if(socket && socket.readyState === WebSocket.OPEN){
        socket.send(JSON.stringify({
            type: 'end-call',
            to: $('#you').val(),
            from: userId
        }));
    }
    endCall();
});

function changechatbox(id){
    window.history.pushState({}, '', '/messanger/' + id);
    $('#you').val(id);
    var csrfToken = "{{ csrf_token() }}";
    $.ajax({
        url:'/messanger/' + id,
        type:'GET',
        data:{
            userId: id,
            _token: csrfToken,
        },
        success: function(response) {
            var $response = $(response).find('#chat').html();
            $('#chat').html($response);
        },
        error: function(xhr, status, error) {
            console.log("حدث خطأ أثناء معالجة الطلب");
        }
    })
}

let typingTimeout;
function showTyping(data){
    if(data.from != $('#you').val()) return;
    $('#typing').remove();
    let typing = $(`
      <div id="typing" class="widget-chat-item start" style="display:none;">
        <div class="widget-chat-message">جاري الكتابة...</div>
      </div>
    `);
    $('#chat').append(typing);
    typing.fadeIn(150);
    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => {
        $('#typing').fadeOut(150, function(){
            $(this).remove();
        });
    }, 1000);
}

async function connectSocket(){
    if(socket && socket.readyState !== WebSocket.CLOSED){
        socket.close();
    }

    let ticket;
    try {
        const response = await fetch(@json(url('/websocket-ticket')), {headers: {'Accept': 'application/json'}});
        if (!response.ok) throw new Error('Unable to create socket ticket');
        ticket = await response.json();
    } catch (error) {
        console.error('WebSocket authentication failed', error);
        return;
    }

    const socketQuery = new URLSearchParams(ticket).toString();
    socket = new WebSocket(@json(config('services.websocket_url')) + "?" + socketQuery);

    socket.onmessage = async function (event) {
        let data = JSON.parse(event.data);
        switch(data.type){
            case 'message': 
                appendMessage(data); 
                let currentChat = $('#you').val();
                if(data.from == currentChat){
                    socket.send(JSON.stringify({
                        type: 'seen',
                        from: userId,
                        to: data.from,
                        message_id: data.id
                    }));
                }
                break;
            case 'typing': 
                showTyping(data); 
                break;
            case 'seen': 
                markSeen(data); 
                break;
            case 'online': 
                userOnline(data.user_id); 
                break;
            case 'offline': 
                userOffline(data.user_id); 
                break;
            case 'offer':
                handleOffer(data);
                break;
            case 'answer':
                if (peer && peer.signalingState === "have-local-offer") {
                    await peer.setRemoteDescription(
                        new RTCSessionDescription(data.answer)
                    );
                    pendingCandidates.forEach(c => {
                        peer.addIceCandidate(new RTCIceCandidate(c))
                            .catch(err => console.log("ICE Error:", err));
                    });
                    pendingCandidates = [];
                }
                break;
            case 'ice':
                addIceCandidateSafe(data.candidate);
                break;
            case 'end-call':
                endCall();
                break;
            case 'call-request':
                if (data.to == userId) {
                    showIncomingCall(data);
                }
                break;
            case 'call-accepted':
                if(data.to == userId){
                    startCall(data.isVideo);
                }
                break;
            case 'call-rejected':
                alert("تم رفض المكالمة");
                break;
            case 'video-upgrade-request':
                if(data.to == userId){
                    let accept = confirm('يريد تشغيل الكاميرا');
                    if(accept){
                        await enableVideoCall();
                        socket.send(JSON.stringify({
                            type: 'video-upgrade-accepted',
                            to: data.from,
                            from: userId
                        }));
                    }
                }
                break;
            case 'video-upgrade-accepted':
                if(data.to == userId){
                    enableVideoCall();
                }
                break;
        }
    };

    let reconnecting = false;
    socket.onclose = function(){
        if(reconnecting) return;
        reconnecting = true;
        setTimeout(() => {
            reconnecting = false;
            connectSocket();
        }, 2000);
    };
}

function showIncomingCall(data){
    let ringtone = new Audio('https://actions.google.com/sounds/v1/alarms/phone_alerts_and_rings.ogg');
    ringtone.loop = true;
    ringtone.play().catch(err=>{ console.log(err); });

    $('#incomingCall').remove();
    $('body').append(`
        <div id="incomingCall"
        style="
            position:fixed;
            inset:0;
            z-index:999999999;
            background:rgba(0,0,0,.75);
            display:flex;
            align-items:center;
            justify-content:center;
            backdrop-filter:blur(8px);
        ">
            <div style="
                width:340px;
                background:#1c1e21;
                border-radius:28px;
                padding:35px 20px;
                text-align:center;
                color:white;
                box-shadow:0 10px 40px rgba(0,0,0,.5);
                animation:ringAnim 1s infinite;
            ">
                <img 
                    src='assets/img/user/user-2.jpg'
                    style='
                        width:110px;
                        height:110px;
                        border-radius:50%;
                        object-fit:cover;
                        border:4px solid rgba(255,255,255,.15);
                    '
                >
                <h3 style='margin-top:20px;font-weight:bold;'>
                    مكالمة واردة
                </h3>
                <div style='color:#b0b3b8;margin-top:10px;'>
                    ${data.isVideo ? 'مكالمة فيديو...' : 'مكالمة صوتية...'}
                </div>
                <div style='
                    display:flex;
                    justify-content:center;
                    gap:25px;
                    margin-top:35px;
                '>
                    <button id='rejectCall'
                    style='
                        width:70px;
                        height:70px;
                        border-radius:50%;
                        border:none;
                        background:#e41e3f;
                        color:white;
                        font-size:24px;
                    '>
                        <i class='fa fa-phone'></i>
                    </button>
                    <button id='acceptCall'
                    style='
                        width:70px;
                        height:70px;
                        border-radius:50%;
                        border:none;
                        background:#31a24c;
                        color:white;
                        font-size:24px;
                    '>
                        <i class='fa fa-phone'></i>
                    </button>
                </div>
            </div>
        </div>
    `);

    if(!$('#callAnimationStyle').length){
        $('head').append(`
            <style id="callAnimationStyle">
                @keyframes ringAnim{
                    0%{transform:scale(1)}
                    50%{transform:scale(1.03)}
                    100%{transform:scale(1)}
                }
            </style>
        `);
    }

    $('#acceptCall').on('click', function(){
        ringtone.pause();
        ringtone.currentTime = 0;
        socket.send(JSON.stringify({
            type: 'call-accepted',
            to: data.from,
            from: userId,
            isVideo: data.isVideo
        }));
        $('#incomingCall').remove();
    });

    $('#rejectCall').on('click', function(){
        ringtone.pause();
        ringtone.currentTime = 0;
        socket.send(JSON.stringify({
            type: 'call-rejected',
            to: data.from,
            from: userId
        }));
        $('#incomingCall').remove();
    });
}

function endCall(){
    if(peer){
        peer.close();
        peer = null;
    }
    if(localStream){
        localStream.getTracks().forEach(track => track.stop());
    }
    $('#localVideo')[0].srcObject = null;
    $('#remoteVideo')[0].srcObject = null;
    $('#remoteAudio')[0].srcObject = null;
    $('#callModal').fadeOut(150);
    $("#callModal").removeClass('show');
    $('#callModal').hide();
    $('body').css('overflow','auto');
}

$('#messageInput').on('keypress', function(e){
    if(e.which == 13 && !e.shiftKey){
        e.preventDefault();
        $('#sendButton').click();
    }
});

// ============ دوال جديدة لتبديل الأجهزة ============
async function switchAudioInput(deviceId) {
    if (!localStream) return;
    try {
        const newStream = await navigator.mediaDevices.getUserMedia({
            audio: { deviceId: { exact: deviceId } }
        });
        const newAudioTrack = newStream.getAudioTracks()[0];
        if (!newAudioTrack) return;

        const oldAudioTrack = localStream.getAudioTracks()[0];
        if (oldAudioTrack) {
            oldAudioTrack.stop();
            localStream.removeTrack(oldAudioTrack);
        }
        localStream.addTrack(newAudioTrack);

        if (peer) {
            const sender = peer.getSenders().find(s => s.track && s.track.kind === 'audio');
            if (sender) {
                await sender.replaceTrack(newAudioTrack);
            }
        }
        console.log('تم تغيير الميكروفون');
    } catch (err) {
        console.error('فشل تغيير الميكروفون:', err);
    }
}

async function switchVideoInput(deviceId) {
    if (!localStream) return;
    try {
        const newStream = await navigator.mediaDevices.getUserMedia({
            video: { deviceId: { exact: deviceId } }
        });
        const newVideoTrack = newStream.getVideoTracks()[0];
        if (!newVideoTrack) return;

        const oldVideoTrack = localStream.getVideoTracks()[0];
        if (oldVideoTrack) {
            oldVideoTrack.stop();
            localStream.removeTrack(oldVideoTrack);
        }
        localStream.addTrack(newVideoTrack);
        $('#localVideo')[0].srcObject = localStream;

        if (peer) {
            const sender = peer.getSenders().find(s => s.track && s.track.kind === 'video');
            if (sender) {
                await sender.replaceTrack(newVideoTrack);
            } else {
                // إذا لم يوجد sender نضيفه (عند بدء المكالمة بدون فيديو)
                peer.addTrack(newVideoTrack, localStream);
            }
        }
        console.log('تم تغيير الكاميرا');
    } catch (err) {
        console.error('فشل تغيير الكاميرا:', err);
    }
}

function setAudioOutput(deviceId) {
    const remoteAudio = document.getElementById('remoteAudio');
    if (remoteAudio && typeof remoteAudio.setSinkId === 'function') {
        remoteAudio.setSinkId(deviceId)
            .then(() => console.log('تم تغيير مخرج الصوت'))
            .catch(err => console.error('فشل تعيين مخرج الصوت:', err));
    } else {
        console.warn('المتصفح لا يدعم setSinkId');
    }
}

// تحديث دالة loadDevices لربط الأحداث
async function loadDevices() {
    const devices = await navigator.mediaDevices.enumerateDevices();
    $('#audioInputSelect').html('');
    $('#audioOutputSelect').html('');
    $('#videoSelect').html('');
    window.earpieceDeviceId = null;

    devices.forEach(device => {
        if(device.kind === 'audioinput'){
            $('#audioInputSelect').append(`
                <option value="${device.deviceId}">
                    ${device.label || 'Microphone'}
                </option>
            `);
        }
        if(device.kind === 'audiooutput'){
            $('#audioOutputSelect').append(`
                <option value="${device.deviceId}">
                    ${device.label || 'Speaker'}
                </option>
            `);
        }
        if(device.kind === 'videoinput'){
            $('#videoSelect').append(`
                <option value="${device.deviceId}">
                    ${device.label || 'Camera'}
                </option>
            `);
        }
    });

    const earpiece = devices.find(d => d.kind === 'audiooutput' && (d.label.toLowerCase().includes('earpiece') || d.label.includes('سماعة')));
    if(earpiece && earpiece.deviceId) {
        window.earpieceDeviceId = earpiece.deviceId;
    }

    // ربط أحداث التغيير لتعمل مباشرة
    $('#audioInputSelect').off('change').on('change', function () {
        const deviceId = $(this).val();
        switchAudioInput(deviceId);
    });

    $('#videoSelect').off('change').on('change', function () {
        const deviceId = $(this).val();
        switchVideoInput(deviceId);
    });

    $('#audioOutputSelect').off('change').on('change', function () {
        const deviceId = $(this).val();
        setAudioOutput(deviceId);
    });
}

async function enableVideoCall(){
    try {
        const videoStream = await navigator.mediaDevices.getUserMedia({
            video: true
        });
        const videoTrack = videoStream.getVideoTracks()[0];

        let oldVideoTrack = localStream.getVideoTracks()[0];
        if (oldVideoTrack) {
            oldVideoTrack.stop();
            localStream.removeTrack(oldVideoTrack);
        }
        localStream.addTrack(videoTrack);
        $('#localVideo')[0].srcObject = localStream;
        $('#callModal').addClass('show').show();
        $('body').css('overflow','hidden');

        let sender = peer.getSenders().find(s => s.track && s.track.kind === 'video');
        if(sender){
            await sender.replaceTrack(videoTrack);
        } else {
            peer.addTrack(videoTrack, localStream);
        }

        const offer = await peer.createOffer();
        await peer.setLocalDescription(offer);

        socket.send(JSON.stringify({
            type: 'offer',
            offer: offer,
            to: $('#you').val(),
            from: userId,
            isVideo: true
        }));
    } catch(e) {
        console.log(e);
    }
}
</script>
@endsection
