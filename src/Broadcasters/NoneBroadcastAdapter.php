<?php namespace Zipzoft\MessageTransporter\Broadcasters;


class NoneBroadcastAdapter extends AbstractBroadcastAdapter
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
