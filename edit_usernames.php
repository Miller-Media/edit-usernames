<?php
/**
* Plugin Name: Username Editor
* Description: Change a user's username within the admin screen.
* Version: 0.1.0
* Author: Michael Robinson (Miller Media)
* License: GPL12
*/

// Current version disallows editing of your own username, and those of superadmins
// All references to previous username are changed after editing, including comment author

include( 'inc/helpers.php' );

if ( is_admin() ){

    function username_admin_notice__error() {
        $class = 'notice notice-error';
        $message = __( 'ERROR: Username already exists. Please choose another.', 'edit-usernames' );

        printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
    }

    // Display WordPress error message if username already exists
    if ( isset($_GET['exists'])){
        if ( $_GET['exists'] == 'yes') {
            add_action( 'admin_notices', 'username_admin_notice__error' );
        }
    }

    function update_username($user_id){

        // Only allow admins to edit usernames
        /*
         * 10/27/16
         * Added support for WooCommerce Managers
         */
        if( (current_user_can('administrator') || current_user_can( 'manage_woocommerce' ) || current_user_can('edit_users')) && !(is_super_admin($user_id))){

            global $wpdb;

            // If someone has submitted a new user login
            if ( isset($_POST['user_login']) && !empty($_POST['user_login'])){

                $current_username = get_user_by('id', $user_id)->user_login;
                $current_username = esc_sql($current_username);
//                $new_username = sanitize_user($_POST['user_login']);
                $new_username = $_POST['user_login'];
                $new_username = esc_sql($new_username);

                // Nothing has changed, so we exit
                if ( $current_username == $new_username ) {
                    return;
                }

                elseif ( username_exists($new_username)){
                    $return_url = wp_get_referer();
                    $redirect = add_query_arg( 'exists', 'yes', $return_url );
                    wp_safe_redirect( $redirect );
                    exit();
                }

                else {
                    // Update username and related values
                    $sql_userlogin = $wpdb->prepare( "UPDATE $wpdb->users SET user_login = %s WHERE user_login = %s", $new_username, $current_username );

                    // Is display name the same as username?
                    $sql_user = $wpdb->prepare( "SELECT * from $wpdb->users WHERE user_login = %s", $current_username );
                    $user_info = $wpdb->get_row( $sql_user );
                    $display_name = $user_info->display_name;

                    // Is nickname the same as username?
                    $sql_usermeta = $wpdb->prepare( "SELECT meta_value from $wpdb->usermeta WHERE user_id = %s AND meta_key = 'nickname'", $user_id );
                    $usermeta_info = $wpdb->get_row( $sql_usermeta);
                    $nickname = $usermeta_info->meta_value;

                    // If display name == username, update both
                    if( $current_username == $display_name ) {
                        $sql_display_name = $wpdb->prepare( "UPDATE $wpdb->users SET display_name = %s WHERE user_login = %s", $new_username, $new_username );
                    }

                    // If nickname == username, update both
                    if( $current_username == $nickname ) {
                        $sql_nickname = $wpdb->prepare( "UPDATE $wpdb->usermeta SET meta_value = %s WHERE user_id = %s AND meta_key = 'nickname'", $new_username, $user_id );
                    }

                    if( false !== $wpdb->query( $sql_userlogin ) ) {

                        if( isset( $sql_display_name ) ) {
                            $wpdb->query( $sql_display_name );
                        }

                        if( isset( $sql_nickname ) ) {
                            $wpdb->query( $sql_nickname );
                        }

                    } else {
                        // Display database error
                        echo '<div id="message" class="error"><p><strong>An error occurred:</strong></p></div>';
                        var_dump( $wpdb->last_error );
                        die();
                    }
                }
            }
        }
    }
    add_action( 'edit_user_profile_update', 'update_username', 0 );
//    add_action( 'personal_options_update', 'update_username', 0 ); // uncomment to allow self-editing

    // Check whether nicename is sanitized version of login; update if not
    function update_user_nicename($user_id){
        global $wpdb;

        $user_login = $user_id->user_login;
        $user_nicename = $user_id->user_nicename;

        if ( $user_nicename != strtolower( str_replace( ' ', '-', $user_login ) )){
            $sql_nicename = $wpdb->prepare( "UPDATE $wpdb->users SET user_nicename = %s WHERE user_login = %s", strtolower( str_replace( ' ', '-', $user_login ) ), $user_login );
            $wpdb->query($sql_nicename);
        }

    }
    // Update comment author
    function update_comment_author($user_id){
        global $wpdb;

        $id = $user_id->ID;
        $display_name = $user_id->display_name;

        $sql_comment_author = $wpdb->prepare("UPDATE $wpdb->comments SET comment_author = %s WHERE user_id = %s", $display_name, $id);
        $wpdb->query( $sql_comment_author );
    }
    add_action('edit_user_profile', 'update_user_nicename');
//    add_action('show_user_profile', 'update_user_nicename'); // uncomment to allow self-editing
    add_action('edit_user_profile', 'update_comment_author');
//    add_action('show_user_profile', 'update_user_nicename'); // uncomment to allow self-editing
}



