<?php

namespace Zipzoft\Tests;

use Zipzoft\MessageTransporter\Broadcasters\NoneBroadcastAdapter;
use Zipzoft\MessageTransporter\Broadcasters\RedisBroadcastAdapter;
use Zipzoft\MessageTransporter\Broadcasters\ServiceBroadcaster;
use Zipzoft\MessageTransporter\Factory;
use Zipzoft\MessageTransporter\Manager;

class InstanceTest extends TestCase
{

    public function testCreateDriver()
    {
        $this->assertInstanceOf(Factory::class, new Manager($this->app));

        $this->assertInstanceOf(
            ServiceBroadcaster::class,
            $this->app->make(Factory::class)->connection()
        );
    }

    public function testDefaultDriver()
    {
        $this->assertInstanceOf(
            NoneBroadcastAdapter::class,
            $this->app->make(ServiceBroadcaster::class)
        );
    }


    public function testRedisInstance()
    {
        config([
            'message-transporter.default' => 'redis'
        ]);

        $this->assertInstanceOf(RedisBroadcastAdapter::class, $this->app->make(ServiceBroadcaster::class));
    }

}