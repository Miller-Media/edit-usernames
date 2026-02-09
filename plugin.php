<?php
/**
Plugin Name: Edit Usernames
Description: Change a user's username within the admin screen.
Author: Miller Media
Author URI: http://www.millermedia.io
Version: 1.2.1
Requires PHP: 8.1
Tested up to: 6.9
License: GPLv2
Text Domain: edit-usernames
*/

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

    new EUN_Helpers();
    new EUN_EditUsernames();    
}