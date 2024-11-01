<?php defined('ABSPATH') OR exit;

/**
 * Load Classes
 */
include_once 'inc/RebugioGetScript.php';

/**
 * Add white list settings page
 */
function wp_rebugio_whitelist($options)
{
    $added = array(
        'wp-rebugio-options' => array(
            WPREBUG_PROJECT_KEY_DB
        )
    );

    $options = add_option_whitelist($added, $options);

    return $options;
}

add_filter('whitelist_options', 'wp_rebugio_whitelist');

/**
 * Add menu option
 */
add_action('admin_menu', 'wp_rebugio_menu');

function wp_rebugio_menu()
{
    add_options_page(
        'rebug.io',
        'rebug.io',
        'manage_options',
        'rebug-settings',
        'wp_rebugio_admin_menu_projectkey'
    );

    option_update_filter(WPREBUG_PROJECT_KEY_DB);
}

function wp_rebugio_admin_menu_projectkey()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include_once (dirname(__FILE__)) . '/tpl/wp-rebugio-admin-menu-projectkey.php';
}

/**
 * Register settings
 */
add_action('admin_init', 'wp_rebugio_admin_init');

function wp_rebugio_admin_init()
{
    add_settings_section(
        'wp-rebugio-options',
        '',
        '',
        'wp-rebugio-menu-projectkey'
    );

    add_settings_field(
        WPREBUG_PROJECT_KEY_DB,
        'Project Key',
        'wp_rebugio_setting_project_key_field_callback',
        'wp-rebugio-menu-projectkey',
        'wp-rebugio-options'
    );

    register_setting(
    	'wp-rebugio-options',
	    WPREBUG_PROJECT_KEY_DB,
	    'wp_rebugio_validate_project_key'
    );
}

function wp_rebugio_validate_project_key($input)
{
	$allow = preg_match(
		'/^([a-f0-9]{8})\-([a-f0-9]{4})\-([a-f0-9]{4})\-([a-f0-9]{4})\-([a-f0-9]{12})$/',
		trim($input)
	);

	if (!$allow) {
		add_settings_error(
			'wp-rebugio-options',
			'wp-rebug-projectkey-error',
			'Project key that you\'ve entered is invalid',
			'error'
		);

		return false;
	}

	return $input;
}

function wp_rebugio_setting_project_key_field_callback()
{
    $projectKey = esc_attr(get_option(WPREBUG_PROJECT_KEY_DB));

    $str = '<input type="text" name="' . WPREBUG_PROJECT_KEY_DB . '" value="' . $projectKey . '" placeholder="Project Key" aria-describedby="project-key-description"/>';

    if (empty($projectKey)) {
	    $str .= '<p class="description" id="project-key-description">Set your project key.</p>';
    } else {
	    $str .= '<p class="description" id="project-key-description">Your project key is ' . $projectKey . '</p>';
    }

    echo $str;
}

/**
 * Add fixed style in admin page
 */
add_action('admin_enqueue_scripts', 'wp_rebugio_admin_fix_style');

function wp_rebugio_admin_fix_style() {
    wp_enqueue_style(
        'style',
        plugins_url('assets/style/style.css', __FILE__),
        false,
        WPREBUG_PLUGIN_VERSION
    );
}

/**
 * Add RebugIo script to any page
 */
add_action('wp_head', 'wp_rebugio_add_script', 100);

function wp_rebugio_add_script()
{
    if (is_admin()) {
        return;
    }

    $rebugioGetScript = new RebugioGetScript();

	echo $rebugioGetScript->getScript();
}