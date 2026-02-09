<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

class EUN_Helpers {

    public function __construct()
    {
        add_action( 'admin_init', array( $this, 'enqueue_scripts_for_admin' ) );
        add_action( 'admin_footer', array( $this, 'enable_username_field' ) );
    }

    /**
     * Enable username field editing without JavaScript
     * Makes the field editable by default (server-side) instead of requiring JS
     */
    public function enable_username_field()
    {
        $screen = get_current_screen();

        // Only on user-edit.php (editing OTHER users, not your own profile)
        if ( ! $screen || $screen->id !== 'user' ) {
            return;
        }

        // Only for admins/users with edit_users capability
        if ( ! current_user_can( 'administrator' ) && ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'edit_users' ) ) {
            return;
        }

        // Check if we're editing a super admin
        if ( isset( $_GET['user_id'] ) ) {
            $user_id = intval( $_GET['user_id'] );
            $option_values = get_option( EUN_SETTINGS_PREFIX . 'options' );
            $allow_admin_editing = false;

            if ( is_array( $option_values ) && array_key_exists( EUN_SETTINGS_PREFIX . 'edit_admin', $option_values ) && $option_values[ EUN_SETTINGS_PREFIX . 'edit_admin' ] == 'on' ) {
                $allow_admin_editing = true;
            }

            // Don't enable for super admins unless explicitly allowed
            if ( is_super_admin( $user_id ) && ! $allow_admin_editing ) {
                return;
            }
        }

        // Remove the disabled attribute and add a helpful description
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var $usernameField = $('#user_login');
                if ($usernameField.length) {
                    // Enable the field
                    $usernameField.prop('disabled', false).prop('readonly', false);

                    // Add helpful styling and description
                    $usernameField.css({
                        'background-color': '#fff',
                        'border-color': '#8c8f94'
                    });

                    // Update or add description
                    var $description = $usernameField.closest('tr').find('.description');
                    if ($description.length === 0) {
                        $usernameField.after('<p class="description"><?php echo esc_js( __( 'You can edit the username by typing in this field.', 'edit-usernames' ) ); ?></p>');
                    } else {
                        $description.html('<?php echo esc_js( __( 'You can edit the username by typing in this field.', 'edit-usernames' ) ); ?>');
                    }
                }
            });
        </script>
        <?php
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
     * NOTE: This is now simplified since we enable the field by default in enable_username_field()
     */
    public function enqueue_scripts_for_admin()
    {
        // Only enqueue the self_user.js for profile pages (when viewing your own profile)
        // The edit functionality is now handled server-side for user-edit.php
        if ( current_user_can( 'administrator' ) || current_user_can( 'manage_woocommerce' ) || current_user_can( 'edit_users' ) ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'edit_usernames_enqueue_scripts' ) );
        }
    }

}