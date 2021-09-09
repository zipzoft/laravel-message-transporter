<?php namespace Zipzoft\MessageTransporter\Broadcasters;


class NoneBroadcaster extends AbstractBroadcaster
{
    /**
     * @param $event
     * @param array $channels
     * @param null $data
     * @return mixed
     */
    public function onBroadcast($event, array $channels, $data = null)
    {
        //
    }

    /**
     * @param array $channels
     * @return mixed
     */
    protected function onSubscribeChannels(array $channels)
    {
        //
    }
}
