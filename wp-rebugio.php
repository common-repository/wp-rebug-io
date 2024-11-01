<?php defined('ABSPATH') OR exit;
/**
 * Plugin Name: WP rebug.io
 * Description: Detecting and recording errors on websites.
 * Author:      rebug.io
 * Author URI:  https://rebug.io/
 * Version:     0.6
 * License:     GPLv2
 */

define('WPREBUG_PLUGIN_VERSION', '0.6');

/**
 * DB Table name definitions
 */
define('WPREBUG_VERSION_DB', 'wprebugio_version');
define('WPREBUG_PROJECT_KEY_DB', 'wprebugio_project_key');
define('WPREBUG_DAY_TO_UPDATE_SCRIPT_DB', 'wprebugio_day_to_update_script');
define('WPREBUG_LAST_UPDATE_SCRIPT_DB', 'wprebugio_last_update_script');
define('WPREBUG_URL_SCRIPT_DB', 'wprebugio_url_script');
define('WPREBUG_SCRIPT_CONTENT', 'wprebugio_script_content');

/**
 * Other definitions
 */
define('WPREBUG_SCRIPT_TEMPLATE_URL', 'https://backend.rebug.io/plugin/code');
define('WPREBUG_DAY_TO_UPDATE_SCRIPT', '7');
register_activation_hook(__FILE__, 'wprebugio_activate');
register_uninstall_hook(__FILE__, 'wprebugio_uninstall');

function wprebugio_activate()
{
    if (version_compare(PHP_VERSION, '5.3', '<')) {
        deactivate_plugins(basename(__FILE__));

        $message = 'This plugin can not be activated because it requires a PHP version greater than <b>5.3.0</b>.';
        $message .= '<br>You are currently using PHP <b>%1$s</b>.';
        $message .= '<br><br>Your PHP version can be updated by your hosting company.';

        wp_die(
            '<p>'
            . sprintf($message, PHP_VERSION)
            . '</p><a href="' . admin_url('plugins.php') . '">Go back</a>'
        );
    } else {
        add_option(WPREBUG_VERSION_DB, WPREBUG_PLUGIN_VERSION);
        add_option(WPREBUG_PROJECT_KEY_DB, '');
        add_option(WPREBUG_URL_SCRIPT_DB, WPREBUG_SCRIPT_TEMPLATE_URL);
        add_option(WPREBUG_DAY_TO_UPDATE_SCRIPT_DB, WPREBUG_DAY_TO_UPDATE_SCRIPT);
        add_option(WPREBUG_LAST_UPDATE_SCRIPT_DB, '');
        add_option(WPREBUG_SCRIPT_CONTENT, '');
    }
}

function wprebugio_uninstall()
{
    delete_option(WPREBUG_VERSION_DB);
    delete_option(WPREBUG_PROJECT_KEY_DB);
    delete_option(WPREBUG_DAY_TO_UPDATE_SCRIPT_DB);
    delete_option(WPREBUG_LAST_UPDATE_SCRIPT_DB);
    delete_option(WPREBUG_URL_SCRIPT_DB);
    delete_option(WPREBUG_SCRIPT_CONTENT);
}

include_once(__DIR__ . '/functions.php');
