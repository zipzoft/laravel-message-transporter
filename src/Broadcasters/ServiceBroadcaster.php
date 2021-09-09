<?php namespace Zipzoft\MessageTransporter\Broadcasters;


use Zipzoft\MessageTransporter\Factory;

interface ServiceBroadcaster extends Factory
{
    /**
     * @param string $event
     * @param \Illuminate\Broadcasting\Channel[] $channels
     * @param array|null $data
     * @return mixed
     */
    public function broadcast(string $event, array $channels, $data = null);

    /**
     * @param $channels
     * @param null|string|callable $listener
     * @return mixed
     */
    public function subscribe($channels, $listener = null);
}
