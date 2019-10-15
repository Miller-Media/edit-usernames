<?php

if (is_admin()) {

    function edit_usernames_enqueue_scripts($hook)
    {
        // if ( !($hook == 'user-edit.php' || $hook == 'profile.php')){
        if (!($hook == 'user-edit.php')) { // enable above line instead to allow editing of own username
            return;
        }

        // Check whether user being edited is a Super Admin; if so, do not enqueue scripts (disallows editing)
        $path = $_SERVER['REQUEST_URI'];
        $full_id_string = explode('user_id=', $path)[1];
        $id_string = explode('&', $full_id_string)[0];
        if (is_super_admin($id_string)) {
            return;
        }

        wp_register_script('edit-user', plugins_url('../js/edit_user.js', __FILE__), array('jquery'));
        wp_enqueue_script('edit-user');
    }

    function enqueue_scripts_for_admin()
    {
        if (current_user_can('administrator') || current_user_can('manage_woocommerce') || current_user_can('edit_users')) {
            // Enqueue scripts on user-edit and profile pages only (profile temporarily disabled)
            add_action('admin_enqueue_scripts', 'edit_usernames_enqueue_scripts');
        }
    }

    add_action('admin_init', 'enqueue_scripts_for_admin');


}