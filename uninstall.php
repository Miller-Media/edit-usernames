<?php
/**
 * Uninstall handler for Edit Usernames.
 *
 * @package Edit_Usernames
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$options = get_option( 'eun_setting_options' );
$delete_data = is_array( $options ) && ! empty( $options['eun_setting_delete_data_on_uninstall'] );

if ( ! $delete_data ) {
	return;
}

// Delete plugin options.
delete_option( 'eun_setting_options' );
delete_option( 'eun_activated_on' );

// Clean up user meta for review notice dismissals.
global $wpdb;
$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => 'edit-usernames_review_dismissed' ) );
