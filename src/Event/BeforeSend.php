<?php

namespace Zipzoft\MessageTransporter\Event;

class BeforeSend
{
    /**
     * Event name
     *
     * @var string
     */
    public string $event;

    /**
     * @var array
     */
    public array $channels;

    /**
     * @var mixed|null
     */
    public $data;

    /**
     * @param string $event
     * @param array $channels
     * @param null $data
     */
    public function __construct(string $event, array $channels, $data = null)
    {
        $this->event = $event;
        $this->channels = $channels;
        $this->data = $data;
    }
}