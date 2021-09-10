<?php namespace Zipzoft\MessageTransporter\Broadcasters;

use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Support\Facades\Event;
use Zipzoft\MessageTransporter\Event\OnMessage;

class RedisBroadcastAdapter extends AbstractBroadcastAdapter
{
    /**
     * @var RedisFactory
     */
    private $factory;

    /**
     * @var \Illuminate\Redis\Connections\Connection
     */
    private $producer;

    /**
     * @var \Illuminate\Redis\Connections\Connection
     */
    private $consumer;

    /**
     * @param RedisFactory $factory
     */
    public function __construct(RedisFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string $event
     * @param array $channels
     * @param null $data
     * @return mixed|void
     */
    public function onBroadcast(string $event, array $channels, $data = null)
    {
        if (! $this->producer) {
            $this->producer = $this->factory->connection($this->getConnectionProducerName());
        }

        $payload = [
            'event' => $event,
            'data' => $data,
            'sent' => now()->toAtomString(),
        ];

        foreach ($channels as $channel) {
            $this->producer->publish((string)$channel, json_encode($payload));
        }
    }

    /**
     * @param array $channels
     * @return mixed
     */
    protected function onSubscribeChannels(array $channels)
    {
        if (! $this->consumer) {
            $this->consumer = $this->factory->connection($this->getConnectionConsumerName());
        }

        $this->consumer->subscribe($channels, function ($message, $channel) {
            Event::dispatch(new OnMessage($channel, $message));
        });
    }

    /**
     * @return string
     */
    protected function getConnectionProducerName()
    {
        return config('message-transporter.connection_prefix') . 'producer';
    }

    /**
     * @return string
     */
    protected function getConnectionConsumerName()
    {
        return config('message-transporter.connection_prefix') . 'consumer';
    }
}
