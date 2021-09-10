<?php

namespace Zipzoft\MessageTransporter\Event;

class OnMessage
{
    /**
     * Channel name
     *
     * @var string
     */
    public $channel;

    /**
     * @var string|null
     */
    public $event;

    /**
     * @var array|string|null
     */
    public $data;

    /**
     * @param string $channel
     * @param string|null $event
     * @param array|string|null $data
     */
    public function __construct(string $channel, $event = null, $data = null)
    {
        $this->event = $event;
        $this->channel = $channel;
        $this->data = $data;
    }
}
