<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

class EUN_Helpers {

    public function __construct()
    {
        add_action( 'admin_init', array( $this, 'enqueue_scripts_for_admin' ) );
    }

    /**
     * Determine what page we are on and load scipt if needed
     *
     * @param   $hook  string
     * @return  null | void
     */
    public function edit_usernames_enqueue_scripts( $hook )
    {
        if ( EUN_SELFEDIT ) {
            if ( ! ( $hook == 'user-edit.php' || $hook == 'profile.php' ) ) {
                return;
            }
        } else {
            if ( $hook != 'user-edit.php' ) {
                return;
            }    
        }

        // Check whether user being edited is a Super Admin; if so, do not enqueue scripts (disallows editing)
        $path = $_SERVER['REQUEST_URI'];
        $full_id_string = explode( 'user_id=', $path )[1];
        $id_string = explode( '&', $full_id_string )[0];
        if ( is_super_admin( $id_string ) ) {
            return;
        }

        wp_register_script( 'edit-user', plugins_url( '../js/edit_user.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_script( 'edit-user' );
    }

    /**
     * Enqueue scripts on user-edit and profile pages only (profile temporarily disabled)
     */
    public function enqueue_scripts_for_admin()
    {
        if ( current_user_can( 'administrator' ) || current_user_can( 'manage_woocommerce' ) || current_user_can( 'edit_users' ) ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'edit_usernames_enqueue_scripts' ) );
        }
    }

}