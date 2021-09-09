<?php

namespace Zipzoft\MessageTransporter;

use Zipzoft\MessageTransporter\Broadcasters\ServiceBroadcaster;

interface Factory
{
    /**
     * @param null $name
     * @return ServiceBroadcaster
     */
    public function connection($name = null): ServiceBroadcaster;
}