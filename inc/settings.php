<?php

function eun_register_settings() {
    if (false == get_option(EUN_SETTINGS_PREFIX.'options')) {
        add_option( EUN_SETTINGS_PREFIX.'options');
    }

    register_setting( EUN_SETTINGS_PREFIX.'options', EUN_SETTINGS_PREFIX.'options');
}
add_action( 'admin_init', 'eun_register_settings' );

function eun_register_options_page() {
    add_options_page('General Settings', EUN_PLUGIN_NAME, 'manage_options', 'eun_settings', 'eun_settings_options_page');
}
add_action('admin_menu', 'eun_register_options_page');

function eun_settings_options_page()
{
    $option_array_name = EUN_SETTINGS_PREFIX.'options';
    $options = get_option(EUN_SETTINGS_PREFIX.'options');
?>
  <div>
      <h1><?php echo get_option(EUN_SETTINGS_PREFIX.'edit_admin'); ?></h1>
  <h2>Edit Usernames Settings</h2>
  <form method="post" action="options.php">
  <?php settings_fields( EUN_SETTINGS_PREFIX.'options' ); ?>
  <table>
  <tr>
  <th scope="row"><label for="<?php echo $option_array_name.'['.EUN_SETTINGS_PREFIX.'edit_admin]'; ?>">Allow admin username editing?</label></th>
  <td>
      <input type="checkbox" id="<?php echo EUN_SETTINGS_PREFIX.'edit_admin'?>" name="<?php echo $option_array_name.'['.EUN_SETTINGS_PREFIX.'edit_admin]'?>" <?php if(is_array($options) && array_key_exists(EUN_SETTINGS_PREFIX.'edit_admin', $options) && $options[EUN_SETTINGS_PREFIX.'edit_admin']=='on'){echo 'checked=checked';} ?>.'/
  </td>
  </tr>
  </table>
  <?php submit_button(); ?>
  </form>
  </div>
<?php
}
