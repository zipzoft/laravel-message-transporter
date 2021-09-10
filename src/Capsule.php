<?php

namespace Zipzoft\MessageTransporter;

use Illuminate\Queue\SerializesModels;
use ReflectionClass;

class Capsule
{
    use SerializesModels;

    /**
     * Event name
     *
     * @var string
     */
    public $name;

    /**
     * @var string[]
     */
    public $channels;

    /**
     * @var array|null
     */
    public $data;

    /**
     * @param array $initial
     * @return static
     * @throws \ReflectionException
     */
    public static function fromArray(array $initial)
    {
        return tap(new static, function ($instance) use ($initial) {
            $reflector = new ReflectionClass($instance);

            foreach ($reflector->getDefaultProperties() as $name => $val) {
                $instance->$name = $initial[$name];
            }
        });
    }
}
