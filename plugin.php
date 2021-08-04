<?php
/**
 * Plugin Name: YOU PLUGIN NAME
 * Description: Use a mobile app to manage the store
 * Version: 0.0.1
 * Requires PHP: 7.4
 * Author: author
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: YOU_PLUGIN
 * Domain Path: languages
 */

namespace YOU_PLUGIN;

if ( ! defined( 'YOU_PLUGIN_FILE' ) ) {
    define( 'YOU_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'YOU_PLUGIN_DIR' ) ) {
    define( 'YOU_PLUGIN_DIR', plugin_dir_path( YOU_PLUGIN_FILE ) );
}

require_once YOU_PLUGIN_DIR . 'autoload.php';

function app() {
    static $app;
    if ( is_null( $app ) ) {
        $app = new Application();
        $app->bootstrap();
    }

    return $app;
}

app();