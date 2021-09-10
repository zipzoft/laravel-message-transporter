<?php

namespace Zipzoft\Tests;

use Orchestra\Testbench\TestCase as Test;
use Zipzoft\MessageTransporter\MessageTransporterServiceProvider;

abstract class TestCase extends Test
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->register(MessageTransporterServiceProvider::class);
    }
}