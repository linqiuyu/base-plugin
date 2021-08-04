<?php

namespace YOU_PLUGIN\I18n;

class I18n
{
    public function load_textdomain()
    {
        load_plugin_textdomain('YOU_PLUGIN', false, plugin_basename(YOU_PLUGIN_DIR) . '/languages');
    }
}