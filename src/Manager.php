<?php

namespace Zipzoft\MessageTransporter;

use Illuminate\Support\Manager as LaravelManager;
use Zipzoft\MessageTransporter\Broadcasters\NoneBroadcastAdapter;
use Zipzoft\MessageTransporter\Broadcasters\RedisBroadcastAdapter;
use Zipzoft\MessageTransporter\Broadcasters\ServiceBroadcaster;

class Manager extends LaravelManager implements Factory
{
    /**
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config->get('message-transporter.default') ?: 'none';
    }

    /**
     * @return RedisBroadcastAdapter
     */
    protected function createRedisDriver()
    {
        return $this->container->make(RedisBroadcastAdapter::class);
    }

    /**
     * @return NoneBroadcastAdapter
     */
    protected function createNoneDriver()
    {
        return new NoneBroadcastAdapter();
    }

    /**
     * @param null $name
     * @return ServiceBroadcaster
     */
    public function connection($name = null): ServiceBroadcaster
    {
        return $this->driver($name);
    }
}
