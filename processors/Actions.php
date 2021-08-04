<?php

namespace YOU_PLUGIN\Processors;

use YOU_PLUGIN\Application;
use YOU_PLUGIN\ProcessorInterface;
use YOU_PLUGIN\Facades\Activate;

class Actions implements ProcessorInterface
{
    private Application $app;

    public function process($app)
    {
        $this->app = $app;
        register_activation_hook(YOU_PLUGIN_FILE, [Activate::class, 'activate']);
        $this->actions();
        if (is_admin()) {
            $this->admin_actions();
        }
        add_action('template_redirect', [$this, 'template_actions']);
    }

    public function actions()
    {
        add_action('plugins_loaded', [$this->app['i18n'], 'load_textdomain']);
    }

    public function admin_actions()
    {

    }

    public function template_actions()
    {

    }
}