<?php

namespace Zipzoft\MessageTransporter\Event;

class OnMessage
{
    /**
     * Channel name
     *
     * @var string
     */
    public string $channel;

    /**
     * @var string|null
     */
    public $event;

    /**
     * @var string
     */
    public $message;

    /**
     * @param string $channel
     * @param string $message
     */
    public function __construct(string $channel, string $message)
    {
        $this->channel = $channel;
        $this->message = $message;
    }
}
