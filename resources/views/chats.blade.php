<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="{{ asset('css/chat.css')}}">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chats') }}
        </h2>
    </x-slot>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />

    <div class="container">
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card chat-app">
                    <div id="plist" class="people-list" style="background-color:lightcyan;">
                        <div class="chat-header clearfix h-10" style="text-align: center;">
                            <div class="row">
                                <div class="col-lg-6">
                                    {{-- <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
                                        <img src="https://bootdey.com/img/Content/avatar/avatar2.png" alt="avatar">
                                    </a> --}}
                                    <div class="chat-about" style="border-bottom:solid;">
                                        <h6 class="m-b-0" style="font-size:20px; font-weight: bold;">Chat</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul class="list-unstyled chat-list mt-2 mb-0">
                            @foreach($users as $user)
                                    <a href="{{ route('user.get-messages', ['id' => $user->id])}}">
                                        <li class="clearfix">
                                            <div class="about">
                                                    <div class="name">{{ucwords($user->name)}}</div>
                                            </div>
                                        </li>
                                    </a>
                            @endforeach       
                        </ul>
                    </div>
                    <div class="chat">
                        @if(isset($receiver_user))
                            <div class="chat-header clearfix" style="background-color:silver;">
                                <div class="row">
                                    <div class="col-lg-6">
                                        {{-- <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
                                            <img src="https://bootdey.com/img/Content/avatar/avatar2.png" alt="avatar">
                                        </a> --}}
                                        <div class="chat-about">
                                            <h6 class="m-b-0">{{ucwords($receiver_user->name ?? '')}}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="chat-history" style="height: 450px; overflow-y: scroll;" id="chat_history">
                            <ul class="m-b-0" id="chats">
                                @if($messages)
                                     @foreach($messages as $message)
                                        @if($message->sender_id == Auth()->user()->id)
                                            <li class="clearfix">
                                                <div class="message-data text-right">
                                                    <span class="message-data-time" style="font-size: 15px;"><b>You</b></span>
                                                </div>
                                                <div class="message other-message float-right" style="background-color:skyblue;">{{ $message->message }}
                                                </div><br><br><br>
                                                <div class="float-right">
                                                    <span class="message-data-time">{{
                                                        $message->created_at->diffForHumans();
                                                    }}</span>
                                                </div>
                                            </li>
                                        @else
                                            <li class="clearfix">
                                                <div class="message-data">
                                                    <span class="message-data-time" style="font-size: 15px;"><b>{{ ucwords($receiver_user->name ?? '') }}</b></span>
                                                </div>
                                                <div class="message my-message" style="background-color:lightcoral;">{{ $message->message }}</div>
                                                <div class="mt-3">
                                                    <span class="message-data-time">{{
                                                        $message->created_at->diffForHumans();
                                                    }}</span>
                                                </div>                                   
                                            </li>  
                                        @endif
                                    @endforeach
                                @else
                                    Start Your Conversation!
                               @endif
                            </ul>
                        </div>
                        @if(isset($receiver_id))
                            <div class="chat-message clearfix">
                                <div class="input-group mb-0">
                                    <div style="float:left;">
                                        <input type="hidden" name="receiver_id" value="{{ $receiver_id ?? '' }}" id="receiver_id">
                                        <input type="text" name="message" placeholder="Enter your message…" id="message" style="width: 900px;">
                                    </div>
                                    <div style="float: left; padding-top: 10px;padding-left: 10px; border-radius: 50px; height:30px;"><a class="btn btn-link" type="submit" id="send_message"><i class="fa fa-paper-plane" aria-hidden="true"></i></a></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="/js/app.js"></script>
    <script type="text/javascript">
        var objDiv = document.getElementById("chat_history");

        var receiver_id = document.getElementById('receiver_id').value;
        var sender_id = {{Auth::user()->id}};
        var sender_name = '{{Auth::user()->name}}';
        Echo.channel('channel_'+receiver_id+'_'+sender_id)
            .listen('WebsocketDemoEvent', (e) => {
                var message = JSON.parse(JSON.stringify(e));
                // document.getElementById("chats").innerHTML += "<li class='clearfix'><div class='message-data'><span class='message-data-time' style='font-size: 15px;'>{{date('d-m-Y  H:i:s')}}</span></div><div class='message my-message' style='background-color:lightcoral;'>"+message.data+"</div></li>";
                document.getElementById("chats").innerHTML += "<li class='clearfix'><div class='message-data'><span class='message-data-time' style='font-size: 15px;''><b>"+message.sender_name+"</b></span></div><div class='message my-message' style='background-color:lightcoral;''>"+message.data+"</div><div class='mt-3'><span class='message-data-time'>{{now()->diffForHumans()}}</span></div></li>  ";
                objDiv.scrollTop = objDiv.scrollHeight;
            });

        $( "#send_message" ).click(function() {
            var message = document.getElementById('message').value;
            document.getElementById('message').value = " ";
            $.post("{{route('user.send-messages', ['id'=>$receiver_id ?? ''])}}",{
                '_token': '{{csrf_token()}}',
                'receiver_id':receiver_id,
                'message':message,
            },function(data){
                    $('#chats').append("<li class='clearfix'><div class='message-data text-right'><span class='message-data-time' style='font-size: 15px;'><b>You</b></span></div><div class='message other-message float-right' style='background-color:skyblue;'>"+message+"</div><br><br><br><div class='float-right'><span class='message-data-time'>{{now()->diffForHumans()}}</span></div></li>")
                    objDiv.scrollTop = objDiv.scrollHeight;
            }).catch(function(error){
                console.log(error);
            });
        });
        
        objDiv.scrollTop = objDiv.scrollHeight;
    </script>
</x-app-layout>
