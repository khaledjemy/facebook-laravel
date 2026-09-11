@extends('layout.masterhome')
@section('content')
@php
    $isOwner = $stream->user_id === auth()->id();
    $ownerAvatar = $stream->user->photopro ? asset($stream->user->photopro->path.$stream->user->photopro->id.$stream->user->photopro->type) : asset('img/Default_avatar_profile.jpg');
@endphp
<main class="live-room" data-owner="{{ $isOwner ? 1 : 0 }}">
    <section class="live-stage">
        <video id="live-video" autoplay playsinline {{ $isOwner ? 'muted' : '' }}></video>
        <div id="live-waiting" class="live-waiting">
            <div class="live-camera-icon"><i class="fa fa-video"></i></div>
            <h2>{{ $isOwner ? 'معاينة البث' : 'في انتظار بدء البث' }}</h2>
            <p>{{ $isOwner ? 'اسمح باستخدام الكاميرا والميكروفون ثم راجع الصورة قبل البدء.' : 'سيبدأ الفيديو تلقائيًا عندما يصبح المذيع مباشرًا.' }}</p>
            @if($isOwner)<button id="enable-camera" type="button"><i class="fa fa-camera me-2"></i>تشغيل الكاميرا والميكروفون</button>@endif
        </div>
        <div class="live-topbar">
            <a href="{{ url('/watch') }}" class="live-close"><i class="fa fa-arrow-right"></i></a>
            <span id="live-badge" class="live-badge {{ $stream->status === 'live' ? '' : 'off' }}"><i class="fa fa-circle"></i> مباشر</span>
            <span class="live-viewers"><i class="fa fa-eye"></i> <b id="viewer-count">{{ $stream->viewer_count }}</b></span>
        </div>
        <div class="live-info-overlay">
            <img src="{{ $ownerAvatar }}" alt="">
            <span><b>{{ $stream->user->first_name }} {{ $stream->user->last_name }}</b><small>{{ $stream->title }}</small></span>
        </div>
        @if($isOwner)
        <div class="live-controls">
            <button id="toggle-mic" type="button" title="الميكروفون"><i class="fa fa-microphone"></i></button>
            <button id="toggle-camera" type="button" title="الكاميرا"><i class="fa fa-video"></i></button>
            <button id="start-live" type="button" disabled>بدء البث المباشر</button>
            <button id="finish-live" type="button" class="d-none">إنهاء البث</button>
        </div>
        @endif
        <div id="live-error" class="live-error d-none"></div>
    </section>
    <aside class="live-sidebar">
        <header><div><h3>{{ $stream->title }}</h3><small id="status-text">{{ $stream->status === 'live' ? 'البث مباشر الآن' : ($isOwner ? 'لم يبدأ البث بعد' : 'جاري الاتصال...') }}</small></div><span class="live-privacy"><i class="fa {{ $stream->visibility === 'public' ? 'fa-globe' : ($stream->visibility === 'friends' ? 'fa-user-friends' : 'fa-lock') }}"></i></span></header>
        @if($stream->description)<p class="live-description">{{ $stream->description }}</p>@endif
        <div id="live-comments" class="live-comments"><div class="text-muted text-center py-4">لا توجد تعليقات بعد</div></div>
        <form id="live-comment-form" class="live-comment-form">
            <img src="{{ $profile->photopro ? asset($profile->photopro->path.$profile->photopro->id.$profile->photopro->type) : asset('img/Default_avatar_profile.jpg') }}" alt="">
            <input id="live-comment-input" maxlength="500" placeholder="اكتب تعليقًا..." autocomplete="off">
            <button type="submit" aria-label="إرسال"><i class="fa fa-paper-plane"></i></button>
        </form>
    </aside>
</main>

<style>
.live-room{height:calc(100vh - 60px);min-height:620px;background:#18191a;display:grid;grid-template-columns:minmax(0,1fr) 390px;direction:ltr}.live-stage{position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#050505}.live-stage video{width:100%;height:100%;object-fit:contain;background:#050505}.live-waiting{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;color:#fff;padding:30px;background:radial-gradient(circle,#242526,#080808)}.live-waiting p{color:#b0b3b8;max-width:520px}.live-camera-icon{width:84px;height:84px;border-radius:50%;background:#3a3b3c;display:grid;place-items:center;font-size:34px;margin-bottom:18px}.live-waiting button{border:0;border-radius:9px;background:#1877f2;color:#fff;padding:12px 20px;font-weight:700}.live-topbar{position:absolute;top:18px;inset-inline:18px;display:flex;align-items:center;gap:10px;direction:rtl}.live-close{margin-inline-end:auto;background:#0009;color:#fff;width:42px;height:42px;border-radius:50%;display:grid;place-items:center}.live-badge,.live-viewers{background:#e41e3f;color:#fff;padding:7px 11px;border-radius:7px;font-weight:800}.live-badge.off{background:#65676b}.live-badge i{font-size:8px;margin-inline-end:5px}.live-viewers{background:#000a}.live-info-overlay{position:absolute;left:22px;bottom:85px;color:#fff;display:flex;align-items:center;gap:11px;text-shadow:0 1px 3px #000}.live-info-overlay img{width:48px;height:48px;border:2px solid #fff;border-radius:50%;object-fit:cover}.live-info-overlay span{display:flex;flex-direction:column}.live-info-overlay small{color:#ddd}.live-controls{position:absolute;bottom:20px;left:50%;transform:translateX(-50%);display:flex;align-items:center;gap:10px;background:#000b;padding:10px;border-radius:16px}.live-controls button{border:0;color:#fff;background:#3a3b3c;border-radius:50%;height:44px;min-width:44px;font-weight:800}.live-controls button.off{background:#e41e3f}.live-controls #start-live,.live-controls #finish-live{border-radius:9px;padding:0 20px;background:#e41e3f}.live-controls button:disabled{opacity:.45}.live-error{position:absolute;top:78px;left:50%;transform:translateX(-50%);background:#e41e3f;color:#fff;padding:10px 16px;border-radius:8px}.live-sidebar{direction:rtl;background:#fff;display:flex;flex-direction:column;min-width:0}.live-sidebar header{padding:20px;border-bottom:1px solid #e4e6eb;display:flex;justify-content:space-between;gap:12px}.live-sidebar h3{font-size:19px;margin:0 0 4px;font-weight:800}.live-sidebar header small{color:#65676b}.live-privacy{width:36px;height:36px;background:#e7f3ff;color:#1877f2;border-radius:50%;display:grid;place-items:center}.live-description{padding:12px 20px;margin:0;border-bottom:1px solid #eee;color:#4b4f56}.live-comments{flex:1;overflow:auto;padding:16px;direction:rtl}.live-comment{display:flex;gap:9px;margin-bottom:14px}.live-comment img{width:36px;height:36px;border-radius:50%;object-fit:cover;flex:0 0 auto}.live-comment div{background:#f0f2f5;border-radius:16px;padding:8px 12px;max-width:85%;word-break:break-word}.live-comment b{display:block;font-size:13px}.live-comment small{display:block;color:#8a8d91;font-size:11px;margin-top:3px}.live-comment-form{display:flex;align-items:center;gap:8px;padding:12px;border-top:1px solid #e4e6eb}.live-comment-form img{width:36px;height:36px;border-radius:50%;object-fit:cover}.live-comment-form input{flex:1;min-width:0;background:#f0f2f5;border:0;border-radius:20px;padding:10px 14px;outline:none}.live-comment-form button{border:0;background:transparent;color:#1877f2;font-size:18px}.live-comment-form:has(input:placeholder-shown) button{opacity:.45}.live-ended-link{display:block;background:#e7f3ff;color:#1877f2;border-radius:9px;text-align:center;padding:11px;margin-top:10px;font-weight:700}@media(max-width:900px){.live-room{height:auto;min-height:calc(100vh - 60px);grid-template-columns:1fr;grid-template-rows:minmax(420px,65vh) 480px}.live-sidebar{min-height:480px}.live-info-overlay{bottom:82px}}
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const STREAM_ID = {{ $stream->id }}, OWNER_ID = {{ $stream->user_id }}, USER_ID = {{ auth()->id() }};
    const IS_OWNER = {{ $isOwner ? 'true' : 'false' }}, INITIAL_STATUS = @json($stream->status);
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const video = document.getElementById('live-video'), waiting = document.getElementById('live-waiting');
    const rtcConfig = {iceServers:[{urls:'stun:stun.l.google.com:19302'}]};
    let socket, localStream, recorder, chunks = [], joined = false, ending = false;
    const peers = new Map();

    const post = (url, body) => fetch(url, {method:'POST', headers:{'X-CSRF-TOKEN':token,'Accept':'application/json'}, body:body || new URLSearchParams()});
    const send = payload => socket && socket.readyState === WebSocket.OPEN && socket.send(JSON.stringify({...payload, live_id:STREAM_ID}));
    const showError = text => { const box=document.getElementById('live-error'); box.textContent=text; box.classList.remove('d-none'); };
    const setStatus = (status, viewers) => {
        document.getElementById('viewer-count').textContent = viewers ?? 0;
        const badge=document.getElementById('live-badge'), label=document.getElementById('status-text');
        badge.classList.toggle('off', status !== 'live');
        label.textContent = status === 'live' ? 'البث مباشر الآن' : status === 'processing' ? 'جاري حفظ تسجيل البث...' : status === 'ended' ? 'انتهى البث' : 'لم يبدأ البث بعد';
    };

    async function connectSocket() {
        const response = await fetch('/websocket-ticket', {headers:{'Accept':'application/json'}});
        const ticket = await response.json();
        socket = new WebSocket(@json(config('services.websocket_url')) + '?' + new URLSearchParams(ticket));
        socket.onopen = () => { if (!IS_OWNER && INITIAL_STATUS === 'live') joinBroadcast(); };
        socket.onmessage = async event => {
            const data = JSON.parse(event.data);
            if (Number(data.live_id) !== STREAM_ID || Number(data.from) === USER_ID) return;
            try {
                if (IS_OWNER && data.type === 'live-viewer-join') await offerTo(Number(data.from));
                if (IS_OWNER && data.type === 'live-answer' && peers.has(Number(data.from))) await peers.get(Number(data.from)).setRemoteDescription(data.answer);
                if (!IS_OWNER && data.type === 'live-offer') await answerOffer(data.offer);
                if (data.type === 'live-ice') {
                    const peer = IS_OWNER ? peers.get(Number(data.from)) : peers.get(OWNER_ID);
                    if (peer && data.candidate) await peer.addIceCandidate(data.candidate);
                }
                if (!IS_OWNER && data.type === 'live-ended') endViewer();
            } catch (e) { showError('تعذر استكمال اتصال البث. حاول تحديث الصفحة.'); }
        };
    }
    function makePeer(remoteId) {
        const peer = new RTCPeerConnection(rtcConfig);
        peer.onicecandidate = e => e.candidate && send({type:'live-ice',to:remoteId,candidate:e.candidate});
        peer.onconnectionstatechange = () => ['failed','closed','disconnected'].includes(peer.connectionState) && peers.delete(remoteId);
        if (!IS_OWNER) peer.ontrack = e => { video.srcObject=e.streams[0]; waiting.style.display='none'; video.play().catch(()=>{}); };
        peers.set(remoteId, peer); return peer;
    }
    async function offerTo(viewerId) {
        if (!localStream) return;
        if (peers.has(viewerId)) peers.get(viewerId).close();
        const peer=makePeer(viewerId); localStream.getTracks().forEach(track=>peer.addTrack(track,localStream));
        const offer=await peer.createOffer(); await peer.setLocalDescription(offer);
        send({type:'live-offer',to:viewerId,offer});
    }
    async function answerOffer(offer) {
        if (peers.has(OWNER_ID)) peers.get(OWNER_ID).close();
        const peer=makePeer(OWNER_ID); await peer.setRemoteDescription(offer);
        const answer=await peer.createAnswer(); await peer.setLocalDescription(answer);
        send({type:'live-answer',to:OWNER_ID,answer});
    }
    async function joinBroadcast() {
        if (joined) return; joined=true;
        const response=await post(`/live/${STREAM_ID}/join`);
        if (!response.ok) { joined=false; return; }
        setStatus('live',(await response.json()).viewer_count);
        send({type:'live-viewer-join',to:OWNER_ID});
    }
    function endViewer(){ peers.forEach(p=>p.close()); peers.clear(); waiting.style.display='flex'; waiting.querySelector('h2').textContent='انتهى البث'; waiting.querySelector('p').textContent='يمكنك مشاهدة التسجيل من صفحة الفيديوهات بعد انتهاء معالجته.'; setStatus('ended',0); }

    if (IS_OWNER) {
        const enable=document.getElementById('enable-camera'), start=document.getElementById('start-live'), finish=document.getElementById('finish-live');
        enable.addEventListener('click', async () => {
            try { localStream=await navigator.mediaDevices.getUserMedia({video:{width:{ideal:1280},height:{ideal:720}},audio:true}); video.srcObject=localStream; waiting.style.display='none'; start.disabled=false; }
            catch(e){ showError('يلزم السماح بالوصول إلى الكاميرا والميكروفون لبدء البث.'); }
        });
        document.getElementById('toggle-mic').onclick=function(){ if(!localStream)return; const t=localStream.getAudioTracks()[0]; t.enabled=!t.enabled; this.classList.toggle('off',!t.enabled); this.querySelector('i').className=t.enabled?'fa fa-microphone':'fa fa-microphone-slash'; };
        document.getElementById('toggle-camera').onclick=function(){ if(!localStream)return; const t=localStream.getVideoTracks()[0]; t.enabled=!t.enabled; this.classList.toggle('off',!t.enabled); this.querySelector('i').className=t.enabled?'fa fa-video':'fa fa-video-slash'; };
        start.onclick=async()=>{
            const response=await post(`/live/${STREAM_ID}/start`); if(!response.ok)return showError('تعذر بدء البث.');
            chunks=[]; const mime=['video/webm;codecs=vp9,opus','video/webm;codecs=vp8,opus','video/webm'].find(MediaRecorder.isTypeSupported);
            recorder=new MediaRecorder(localStream,mime?{mimeType:mime}:{}); recorder.ondataavailable=e=>e.data.size&&chunks.push(e.data); recorder.start(1000);
            start.classList.add('d-none'); finish.classList.remove('d-none'); setStatus('live',0); window.liveBroadcastActive=true;
        };
        finish.onclick=async()=>{
            if(ending)return; ending=true; finish.disabled=true; finish.textContent='جاري حفظ التسجيل...';
            peers.forEach((peer,id)=>{send({type:'live-ended',to:id});peer.close();}); peers.clear();
            if(recorder&&recorder.state!=='inactive'){ await new Promise(resolve=>{recorder.onstop=resolve;recorder.stop();}); }
            const form=new FormData(); if(chunks.length)form.append('recording',new Blob(chunks,{type:recorder?.mimeType||'video/webm'}),`live-${STREAM_ID}.webm`);
            try { const response=await post(`/live/${STREAM_ID}/finish`,form); const data=await response.json(); window.liveBroadcastActive=false; localStream?.getTracks().forEach(t=>t.stop()); setStatus('ended',0); finish.textContent='تم إنهاء البث'; if(data.video_id){const a=document.createElement('a');a.href='/video/'+data.video_id;a.className='live-ended-link';a.textContent='مشاهدة تسجيل البث';document.querySelector('.live-sidebar header').after(a);} }
            catch(e){ending=false;finish.disabled=false;finish.textContent='إعادة محاولة إنهاء البث';showError('تعذر حفظ التسجيل. لا تغلق الصفحة وحاول مرة أخرى.');}
        };
    } else if (INITIAL_STATUS !== 'live') endViewer();

    async function loadComments(){ const r=await fetch(`/live/${STREAM_ID}/comments`); if(!r.ok)return; const rows=await r.json(), box=document.getElementById('live-comments'); if(!rows.length)return; box.innerHTML=rows.map(c=>{const p=c.user.photopro,a=p?'/'+p.path+p.id+p.type:'/img/Default_avatar_profile.jpg',name=(c.user.first_name+' '+c.user.last_name).replace(/[<>&]/g,'');return `<div class="live-comment"><img src="${a}" alt=""><div><b>${name}</b><span>${$('<div>').text(c.body).html()}</span><small>${new Date(c.created_at).toLocaleTimeString('ar-EG',{hour:'2-digit',minute:'2-digit'})}</small></div></div>`}).join('');box.scrollTop=box.scrollHeight; }
    document.getElementById('live-comment-form').onsubmit=async e=>{e.preventDefault();const input=document.getElementById('live-comment-input'),body=input.value.trim();if(!body)return;const r=await post(`/live/${STREAM_ID}/comments`,new URLSearchParams({body}));if(r.ok){input.value='';loadComments();}};
    connectSocket().catch(()=>showError('تعذر الاتصال بخدمة البث اللحظي.'));
    loadComments(); setInterval(loadComments,3000);
    setInterval(async()=>{const r=await fetch(`/live/${STREAM_ID}/status`);if(!r.ok)return;const data=await r.json();setStatus(data.status,data.viewer_count);if(!IS_OWNER&&data.status==='live'&&!joined)joinBroadcast();if(!IS_OWNER&&data.status==='ended')endViewer();},5000);
    window.addEventListener('beforeunload',e=>{if(IS_OWNER&&window.liveBroadcastActive){e.preventDefault();e.returnValue='البث ما زال مباشرًا. أنهِ البث أولًا حتى يتم حفظ التسجيل.';}if(!IS_OWNER&&joined)navigator.sendBeacon(`/live/${STREAM_ID}/leave`,new Blob([`_token=${encodeURIComponent(token)}`],{type:'application/x-www-form-urlencoded'}));});
});
</script>
@endsection
