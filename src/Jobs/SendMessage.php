<?php

namespace Zipzoft\MessageTransporter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Zipzoft\MessageTransporter\Broadcasters\ServiceBroadcaster;
use Zipzoft\MessageTransporter\Capsule;

class SendMessage implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Capsule
     */
    public $capsule;

    /**
     * @param Capsule $capsule
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
    public function handle()
    {
        /** @var ServiceBroadcaster $broadcaster */
        $broadcaster = app(ServiceBroadcaster::class);

        $broadcaster->broadcast(
            $this->capsule->name,
            $this->capsule->channels,
            $this->capsule->data
        );
    }
}
