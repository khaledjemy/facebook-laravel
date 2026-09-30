

    <style>


        /* Right Sidebar */
        .right_sidebar {
            width: 320px;
            height: calc(100vh - 56px);
            position: fixed;
            top: 56px;
            right: 0;
            overflow-y: auto;
            padding: 15px;
        }

        .right_sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .right_sidebar::-webkit-scrollbar-thumb {
            border-radius: 10px;
        }

        .sidebar_title {
            color: #525355;
            font-size: 17px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .contacts_header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .contacts_header h6 {
            color: #373738;
            margin: 0;
            font-size: 17px;
            font-weight: bold;
        }

        .contacts_header a {
            color: #65676b;
            font-size: 13px;
            text-decoration: none;
        }

        .contacts_list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .contacts_list li {
            margin-bottom: 5px;
        }

        .contacts_list a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            padding: 8px 10px;
            border-radius: 10px;
            transition: .2s;
        }

        .contacts_list a:hover {
            background: #f1f1f1;
        }

        .contact_img {
            position: relative;
        }

        .contact_img img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
        }

        .contacts_list span {
            color: #393a3d;
            font-size: 15px;
            font-weight: 500;
        }

        /* Responsive */
        @media(max-width:1200px) {

            .right_sidebar {
                display: none;
            }

            .content_area {
                width: 100%;
            }
        }
    </style>


 
    <!-- Layout -->
    <div class="main_layout">

        <!-- Right Sidebar -->
        <div class="right_sidebar">

            <div class="contacts_section">
                <div class="contacts_header">
                    <h6>{{ __('ui.contacts') }}</h6>
                    <a href="{{ url('/friends') }}">{{ __('ui.friends') }}</a>
                </div>

                <ul class="contacts_list">
                    @forelse($sidebarContacts as $contact)
                        <li>
                            <a href="{{ url('/messanger/'.$contact->id) }}"
                               class="contact-chat"
                               data-chat-id="{{ $contact->id }}"
                               data-chat-name="{{ $contact->first_name.' '.$contact->last_name }}"
                               aria-label="{{ __('ui.open_chat_with', ['name' => $contact->first_name.' '.$contact->last_name]) }}">
                                <div class="contact_img">
                                    <img src="{{ $contact->avatar_url }}" alt="" loading="lazy">
                                </div>
                                <span>{{ $contact->first_name }} {{ $contact->last_name }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="text-muted small px-2 py-2">
                            <span>{{ __('ui.no_contacts') }}</span>
                            <a href="{{ url('/friends') }}" class="d-inline-block ms-1">{{ __('ui.find_friends') }}</a>
                        </li>
                    @endforelse
                </ul>
            </div>

        </div>

        <div id="mini-chat" class="mini-chat d-none" dir="auto">
            <div class="mini-chat-header"><strong id="mini-chat-name">Chat</strong><span><button type="button" class="btn btn-sm text-white mini-call" data-kind="audio" title="مكالمة صوتية">☎</button><button type="button" class="btn btn-sm text-white mini-call" data-kind="video" title="مكالمة فيديو">▣</button><button type="button" id="mini-chat-close" class="btn btn-sm text-white">×</button></span></div>
            <div id="mini-chat-messages" class="mini-chat-messages"><div class="text-muted small text-center">ابدأ المحادثة</div></div>
            <form id="mini-chat-form" class="mini-chat-form"><button type="button" id="mini-emoji" class="btn btn-light">😊</button><input id="mini-chat-input" class="form-control" placeholder="اكتب رسالة..." autocomplete="off"><button class="btn btn-primary" type="submit">إرسال</button></form>
        </div>

        <div id="call-panel" class="call-panel d-none"><video id="call-video" autoplay playsinline></video><div class="call-label" id="call-label"></div><button id="end-call" class="btn btn-danger rounded-circle">☎</button></div>

    </div>

