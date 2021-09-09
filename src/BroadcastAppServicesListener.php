<?php

namespace Zipzoft\MessageTransporter;

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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($this->shouldBroadcast($event)) {
            $capsule = Capsule::fromArray([
                'name' => $this->getEventName($event),
                'event' => $event,
                'channels' => $this->getChannels($event),
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
        return $event->queue ?: config('message-transporter.queue.queue');
    }

    /**
     * @param $event
     * @return string|null
     */
    protected function getQueueConnectionName($event)
    {
        return $event->connection
            ?: config('message-transporter.queue.connection')
            ?: config('queue.default');
    }

    /**
     * @return null|bool|array
     */
    private function getQueueConfig()
    {
        return config('message-transporter.queue');
    }


    protected function shouldBeQueue($event)
    {
        if ($event instanceof ShouldBroadcastAppServicesNow) {
            return false;
        }

        $config = $this->getQueueConfig();

        if ($config === false) {
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
     * @return array|null
     */
    protected function getData($event)
    {
        if (method_exists($event, 'broadcastServicesWith')) {
            return $event->toAppServices();
        } else if (method_exists($event, 'broadcastWith')) {
            return $event->broadcastWith();
        }

        return null;
    }

    /**
     * @param object $event
     * @return bool
     */
    protected function shouldBroadcast($event)
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
}
