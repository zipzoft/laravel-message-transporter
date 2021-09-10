<?php

namespace Zipzoft\Tests\EventClass;

use Illuminate\Broadcasting\PrivateChannel;
use Zipzoft\MessageTransporter\ShouldBroadcastAppServices;

class EventWithoutChannel implements ShouldBroadcastAppServices
{


    public function broadcastOn()
    {
        return [];
    }
}