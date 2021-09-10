<?php

namespace Zipzoft\MessageTransporter;

use Illuminate\Contracts\Support\Arrayable;
use ReflectionClass;
use ReflectionProperty;
use Zipzoft\MessageTransporter\Broadcasters\ServiceBroadcaster;
use Zipzoft\MessageTransporter\Jobs\SendMessage;

class BroadcastAppServicesListener
{
    /**
     * @var ServiceBroadcaster
     */
    private ServiceBroadcaster $broadcaster;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ServiceBroadcaster $broadcaster)
    {
        $this->broadcaster = $broadcaster;
    }

    /**
     * Handle the event.
     *
     * @param string $eventName
     * @parent array $data
     * @return void
     */
    public function handle($eventName, array $data)
    {
        if (! is_array($data) || empty($data)) {
            return;
        }

        $event = $data[0];

        if ($this->shouldServiceBroadcast($event)) {
            $channels = $this->getChannels($event);

            if (empty($channels)) {
                return;
            }

            $capsule = Capsule::fromArray([
                'name' => $this->getEventName($event),
                'channels' => $channels,
                'data' => $this->getData($event),
            ]);

            if ($this->shouldBeQueue($event)) {
                dispatch(new SendMessage($capsule))
                    ->onQueue($this->getQueueName($event))
                    ->onConnection($this->getQueueConnectionName($event));
            } else {
                $this->broadcaster->broadcast(
                    $capsule->name, $capsule->channels, $capsule->data
                );
            }
        }
    }

    /**
     * @param $event
     * @return mixed|string
     */
    protected function getEventName($event)
    {
        if (method_exists($event, 'broadcastServicesAs')) {
            return $event->broadcastServicesAs();
        } else if (method_exists($event, 'broadcastAs')) {
            return $event->broadcastAs();
        }

        return get_class($event);
    }

    /**
     * @param $event
     * @return string|null
     */
    protected function getQueueName($event)
    {
        if (property_exists($event, 'queue') && $event->queue) {
            return $event->queue;
        }

        return config('message-transporter.queue.queue');
    }

    /**
     * @param $event
     * @return string|null
     */
    protected function getQueueConnectionName($event)
    {
        if (property_exists($event, 'connection') && $event->connection) {
            return $event->connection;
        }

        return config('message-transporter.queue.connection') ?: config('queue.default');
    }

    /**
     * @param $event
     * @return bool
     */
    protected function shouldBeQueue($event)
    {
        if ($event instanceof ShouldBroadcastAppServicesNow) {
            return false;
        }

        if (config('message-transporter.queue') === false) {
            return false;
        }

        return true;
    }


    /**
     * @param $event
     * @return \Illuminate\Broadcasting\Channel[]
     */
    protected function getChannels($event)
    {
        $channels = [];

        if (method_exists($event, 'broadcastServicesOn')) {
            $channels = $event->broadcastOn();
        } else if (method_exists($event, 'broadcastOn')) {
            $channels = $event->broadcastOn();
        }

        if (! is_array($channels)) {
            $channels = [$channels];
        }

        return $channels;
    }


    /**
     * @param $event
     * @return array|mixed
     * @throws \ReflectionException
     */
    protected function getData($event)
    {
        if (method_exists($event, 'broadcastServicesWith')) {
            return $event->toAppServices();
        } else if (method_exists($event, 'broadcastWith')) {
            return $event->broadcastWith();
        }

        $payload = [];

        foreach ((new ReflectionClass($event))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $payload[$property->getName()] = $this->formatProperty($property->getValue($event));
        }

        unset($payload['broadcastQueue']);

        return $payload;
    }

    /**
     * @param object $event
     * @return bool
     */
    protected function shouldServiceBroadcast($event)
    {
        if ($event instanceof ShouldBroadcastAppServices) {
            if (method_exists($event, 'broadcastServicesWhen')) {
                return $event->broadcastServicesWhen() !== false;
            } else if (method_exists($event, 'broadcastWhen')) {
                return $event->broadcastWhen() !== false;
            }

            return true;
        }

        return false;
    }

    /**
     * Format the given value for a property.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function formatProperty($value)
    {
        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        return $value;
    }
}
