<?php
/**
Plugin Name: Edit Usernames
Description: Change a user's username within the admin screen.
Author: Miller Media
Author URI: https://mattmiller.ai
Version:           1.3.1
Requires PHP: 8.1
Tested up to: 6.9.1
License: GPL-2.0-or-later
Text Domain: edit-usernames
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action('plugins_loaded', function() {
    load_plugin_textdomain('edit-usernames', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

if ( is_admin() ){

    // boolean to control whether to allow self editing
    define( 'EUN_SELFEDIT', false );
    define( 'EUN_PLUGIN_PATH', plugin_dir_path(__FILE__) );
    define( 'EUN_PLUGIN_NAME', 'Edit Usernames');
    define( 'EUN_SETTINGS_PREFIX', 'eun_setting_');

    require_once( EUN_PLUGIN_PATH . 'inc/settings.php' );
    require_once( EUN_PLUGIN_PATH . 'inc/helpers.php' );
    require_once( EUN_PLUGIN_PATH . 'inc/edit-usernames.php' );
    require_once( EUN_PLUGIN_PATH . 'inc/review-notice.php' );

    register_activation_hook( __FILE__, function() {
        if ( ! get_option( 'eun_activated_on' ) ) {
            update_option( 'eun_activated_on', time() );
        }
    });

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=eun_settings') . '">' . __('Settings', 'edit-usernames') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    });

    new EUN_Helpers();
    new EUN_EditUsernames();
    new EUN_ReviewNotice( 'Edit Usernames', 'edit-usernames', 'eun_activated_on', 'edit-usernames', plugin_dir_url( __FILE__ ) . 'assets/plugin-icon.jpg' );
}