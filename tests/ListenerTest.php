<?php

namespace Zipzoft\Tests;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Zipzoft\MessageTransporter\BroadcastAppServicesListener;
use Zipzoft\MessageTransporter\Jobs\SendMessage;
use Zipzoft\Tests\EventClass\EventExample;
use Zipzoft\MessageTransporter\Event\BeforeSend;
use Zipzoft\Tests\EventClass\EventWithoutChannel;

class ListenerTest extends TestCase
{

    public function testListener()
    {
        $message = "This is message";

        config([
            'queue.default' => 'sync',
        ]);

        Queue::fake();

        $this->mock(BroadcastAppServicesListener::class, function (MockInterface $mock) use ($message) {
            $mock->makePartial()
                ->shouldAllowMockingProtectedMethods();

            $mock->shouldReceive('shouldServiceBroadcast')->once()->andReturnTrue();

            $mock->shouldReceive('getChannels')->once()->andReturn([
                new PrivateChannel("test"),
            ]);

            $mock->shouldReceive('shouldBeQueue')->once()->andReturnTrue();

            $mock->shouldReceive('getEventName')->once()->andReturn(EventExample::class);

            $mock->shouldReceive('getData')->once()->andReturn([
                'message' => $message,
            ]);
        });

        Event::dispatch(new EventExample($message));

        Queue::assertPushed(SendMessage::class, function (SendMessage $job) use ($message) {
            return $job->capsule->name === EventExample::class
                && $job->capsule->data['message'] === $message;
        });
    }


    public function testDispatchEvent()
    {
        config(['message-transporter.queue' => false]);

        Queue::fake();
        Event::fake([
            BeforeSend::class,
        ]);

        Event::dispatch(new EventExample(
            $message = "This is message"
        ));

        Event::assertDispatched(BeforeSend::class, function (BeforeSend $event) use ($message) {
            return $event->event === EventExample::class
                && $event->data['message'] === $message
                && in_array('private-test', array_map(fn ($channel) => (string)$channel, $event->channels));
        });

        Queue::assertNotPushed(SendMessage::class);
    }


    public function testShouldDontDispatchIfNoChannels()
    {
        config(['message-transporter.queue' => false]);

        Queue::fake();
        Event::fake([
            BeforeSend::class,
        ]);

        Event::dispatch(new EventWithoutChannel());

        Event::assertNotDispatched(BeforeSend::class);
        Queue::assertNotPushed(SendMessage::class);
    }
}