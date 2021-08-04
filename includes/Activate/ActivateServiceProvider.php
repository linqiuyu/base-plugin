<?php

namespace YOU_PLUGIN\Activate;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ActivateServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['activate'] = function () {
            return new Activate();
        };
    }
}