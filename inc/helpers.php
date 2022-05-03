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
                wp_register_script( 'self-admin-user', plugins_url( '../js/self_user.js', __FILE__ ), array( 'jquery' ) );
                wp_enqueue_script( 'self-admin-user' );
                return;
            }    
        }

        // Check whether user being edited is a Super Admin; if so, do not enqueue scripts (disallows editing)
        $path = $_SERVER['REQUEST_URI'];
        $full_id_string = explode( 'user_id=', $path )[1];
        $id_string = explode( '&', $full_id_string )[0];

        $option_values = get_option(EUN_SETTINGS_PREFIX.'options');
        $allow_admin_editing = false;

        if(is_array($option_values) && array_key_exists(EUN_SETTINGS_PREFIX.'edit_admin', $option_values) && $option_values[EUN_SETTINGS_PREFIX.'edit_admin']=='on'){
            $allow_admin_editing = true;
        }

        if ( is_super_admin( $id_string ) && !$allow_admin_editing ) {
            wp_register_script( 'super-admin-user', plugins_url( '../js/super_admin_user.js', __FILE__ ), array( 'jquery' ) );
            wp_enqueue_script( 'super-admin-user' );

            $script_params = array(
                'settings_url' => admin_url('/options-general.php?page=eun_settings')
            );

            wp_localize_script( 'super-admin-user', 'scriptParams', $script_params );

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