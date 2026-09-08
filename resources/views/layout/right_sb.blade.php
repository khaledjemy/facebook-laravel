

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

        /* Sponsored */
        .sidebar_title {
            color: #525355;
            font-size: 17px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .sponsored_item {
            display: flex;
            gap: 10px;
            text-decoration: none;
            padding: 8px;
            border-radius: 10px;
            margin-bottom: 10px;
            transition: .2s;
        }

        .sponsored_item:hover {
            background: #f1f1f1;
        }

        .sponsored_item img {
            width: 120px;
            height: 70px;
            border-radius: 10px;
            object-fit: cover;
        }

        .sponsored_item div {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .sponsored_title {
            color: #606164;
            font-size: 14px;
            font-weight: bold;
        }

        .sponsored_item small {
            color: #4a4b4d;
        }

        hr {
            border-color: #3a3b3c;
        }

        /* Contacts */
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

        .contact_icons {
            display: flex;
            gap: 15px;
        }

        .contact_icons i {
            color: #29292b;
            cursor: pointer;
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

        .online {
            width: 10px;
            height: 10px;
            background: #31a24c;
            border-radius: 50%;
            position: absolute;
            bottom: 1px;
            right: 1px;
            border: 2px solid #18191a;
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

            <!-- Sponsored -->
            <div class="sidebar_section">

                <h6 class="sidebar_title">Sponsored</h6>

                <a href="#" class="sponsored_item">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=600&auto=format&fit=crop">

                    <div>
                        <span class="sponsored_title">
                            Learn Programming
                        </span>

                        <small>www.learn.com</small>
                    </div>
                </a>

                <a href="#" class="sponsored_item">
                    <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=600&auto=format&fit=crop">

                    <div>
                        <span class="sponsored_title">
                            Web Templates
                        </span>

                        <small>www.templates.com</small>
                    </div>
                </a>

            </div>

            <hr>

            <!-- Contacts -->
            <div class="contacts_section">

                <div class="contacts_header">

                    <h6>Contacts</h6>

                    <div class="contact_icons">
                        <i class="fa-solid fa-video"></i>
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <i class="fa-solid fa-ellipsis"></i>
                    </div>

                </div>

                <ul class="contacts_list">

                    <li>
                        <a href="#" class="contact-chat" data-chat-id="2" data-chat-name="Mohamed Hassan">

                            <div class="contact_img">
                                <img src="{{ asset('images/avatars/2.svg') }}" alt="Mohamed Hassan">
                                <span class="online"></span>
                            </div>

                            <span>Mohamed Hassan</span>

                        </a>
                    </li>

                    <li>
                        <a href="#" class="contact-chat" data-chat-id="3" data-chat-name="Sarah Ahmed">

                            <div class="contact_img">
                                <img src="{{ asset('images/avatars/3.svg') }}" alt="Sarah Ahmed">
                                <span class="online"></span>
                            </div>

                            <span>Sarah Ahmed</span>

                        </a>
                    </li>

                    <li>
                        <a href="#" class="contact-chat" data-chat-id="4" data-chat-name="Omar Gamal">

                            <div class="contact_img">
                                <img src="{{ asset('images/avatars/4.svg') }}" alt="Omar Gamal">
                                <span class="online"></span>
                            </div>

                            <span>Omar Gamal</span>

                        </a>
                    </li>

                    <li>
                        <a href="#" class="contact-chat" data-chat-id="5" data-chat-name="Mahmoud Ali">

                            <div class="contact_img">
                                <img src="{{ asset('images/avatars/5.svg') }}" alt="Mahmoud Ali">
                                <span class="online"></span>
                            </div>

                            <span>Mahmoud Ali</span>

                        </a>
                    </li>

                    <li>
                        <a href="#" class="contact-chat" data-chat-id="6" data-chat-name="Nour Mohamed">

                            <div class="contact_img">
                                <img src="{{ asset('images/avatars/6.svg') }}" alt="Nour Mohamed">
                                <span class="online"></span>
                            </div>

                            <span>Nour Mohamed</span>

                        </a>
                    </li>

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

