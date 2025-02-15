<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebsocketDemoEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $sender_name;
    public $sender_id;
    public $receiver_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($sender_id, $receiver_id, $data, $sender_name)
    {
        $this->sender_id = $sender_id;
        $this->receiver_id = $receiver_id;
        $this->data = $data;
        $this->sender_name = $sender_name;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new Channel('DemoChannel');
        return new Channel('channel_'.$this->sender_id.'_'.$this->receiver_id);
    }
}
