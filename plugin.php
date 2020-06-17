<?php
/**
Plugin Name: Edit Usernames
Description: Change a user's username within the admin screen.
Author: Miller Media
Author URI: http://www.millermedia.io
Version: 1.1.1
Requires PHP: 5.6
License: GPLv2
Text Domain: edit-usernames
*/

if ( is_admin() ){

    // boolean to control whether to allow self editing
    define( 'EUN_SELFEDIT', false );
    define( 'EUN_PLUGIN_PATH', plugin_dir_path(__FILE__) );

    require_once( EUN_PLUGIN_PATH . 'inc/helpers.php' );
    require_once( EUN_PLUGIN_PATH . 'inc/edit-usernames.php' );

    new EUN_Helpers();
    new EUN_EditUsernames();    
}