<?php

namespace Zipzoft\Tests\EventClass;

use Illuminate\Broadcasting\PrivateChannel;
use Zipzoft\MessageTransporter\ShouldBroadcastAppServices;

class EventExample implements ShouldBroadcastAppServices
{

    public $message;

    /**
     * @param $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * @return PrivateChannel[]
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel("test"),
        ];
    }
}