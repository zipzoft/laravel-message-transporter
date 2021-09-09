<?php

namespace Zipzoft\MessageTransporter;

use Illuminate\Support\Manager as LaravelManager;
use Zipzoft\MessageTransporter\Broadcasters\NoneBroadcaster;
use Zipzoft\MessageTransporter\Broadcasters\RedisBroadcaster;
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
     * @return RedisBroadcaster
     */
    protected function createRedisDriver()
    {
        return $this->container->make(RedisBroadcaster::class);
    }

    /**
     * @return NoneBroadcaster
     */
    protected function createNoneDriver()
    {
        return new NoneBroadcaster();
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
