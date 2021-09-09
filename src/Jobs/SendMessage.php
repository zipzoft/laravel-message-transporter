<?php

namespace Zipzoft\MessageTransporter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Zipzoft\MessageTransporter\Broadcasters\ServiceBroadcaster;
use Zipzoft\MessageTransporter\Capsule;

class SendMessage implements ShouldQueue
{
    use Queueable;

    /**
     * @var Capsule
     */
    public Capsule $capsule;

    /**
     * @param $event
     * @param array $channels
     * @param array $data
     */
    public function __construct(Capsule $capsule)
    {
        $this->capsule = $capsule;
    }

    /**
     * Handle the job.
     *
     * @return void
     */
    public function handle(ServiceBroadcaster $broadcaster)
    {
        $broadcaster->broadcast(
            $this->capsule->name,
            $this->capsule->channels,
            $this->capsule->data
        );
    }
}
