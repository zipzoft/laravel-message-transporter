<?php

namespace Zipzoft\MessageTransporter\Broadcasters;

use Illuminate\Support\Facades\Event;
use Zipzoft\MessageTransporter\Event\BeforeSend;
use Zipzoft\MessageTransporter\Event\OnMessage;
use Zipzoft\MessageTransporter\Factory;
use Zipzoft\MessageTransporter\Manager;

abstract class AbstractBroadcastAdapter implements ServiceBroadcaster
{
    /**
     * @param string $event
     * @param array $channels
     * @param null $data
     * @return mixed
     */
    abstract protected function onBroadcast(string $event, array $channels, $data = null);

    /**
     * @param array $channels
     * @return mixed
     */
    abstract protected function onSubscribeChannels(array $channels);

    /**
     * @param null $name
     * @return ServiceBroadcaster
     */
    public function connection($name = null): ServiceBroadcaster
    {
        return app(Factory::class)->connection($name);
    }

    /**
     * @param string $event
     * @param array $channels
     * @param null $data
     * @return mixed
     */
    public function broadcast(string $event, array $channels, $data = null)
    {
        Event::dispatch(new BeforeSend($event, $channels, $data));

        return $this->onBroadcast($event, $channels, $data);
    }

    /**
     * @param $channels
     * @param null $listener
     * @return mixed
     */
    public function subscribe($channels, $listener = null)
    {
        $channels = is_array($channels) ? $channels : [$channels];

        if ($listener) {
            Event::listen(OnMessage::class, $listener);
        }

        $this->onSubscribeChannels($channels);
    }
}