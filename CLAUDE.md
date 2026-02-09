# Edit Usernames

## Overview
Allows WordPress administrators to change a user's username from the user edit screen. Handles all related updates (nicename, display name, nickname, comment authors).

## Architecture

```
plugin.php              # Entry point, defines constants, loads files (admin-only)
inc/
├── edit-usernames.php  # EUN_EditUsernames - core username update logic
├── helpers.php         # EUN_Helpers - admin script enqueuing
└── settings.php        # Settings page registration
js/                     # Frontend JavaScript
```

## Key Classes

### EUN_EditUsernames (inc/edit-usernames.php)
Core logic for updating usernames.

- `update_username($user_id)` - Main handler: validates new username, updates `wp_users` via `$wpdb->prepare()`. Also syncs display_name and nickname if they matched the old username.
- `update_user_nicename($user_id)` - Syncs nicename to be lowercase/hyphenated version of login
- `update_comment_author($user_id)` - Updates all comment authors for the user
- `username_admin_notice__error()` - Displays validation error notices

### Hooks
- `edit_user_profile_update` - Triggers `update_username` when editing another user
- `edit_user_profile` - Triggers nicename/comment sync
- `personal_options_update` - Self-edit (when `EUN_SELFEDIT` is true)

## Constants
- `EUN_SELFEDIT` - `false` by default. Controls whether users can edit their own username.
- `EUN_SETTINGS_PREFIX` - `eun_setting_`

## Permissions
- Requires `administrator`, `manage_woocommerce`, or `edit_users` capability
- Super admin editing controlled by `eun_setting_edit_admin` option

## Settings (wp_options)
- `eun_setting_options` - Contains `eun_setting_edit_admin` toggle

## Testing
Tests are in `../tests/unit/edit-usernames/`. Run with:
```bash
make test-plugin PLUGIN=edit-usernames
```

## Important Notes
- Plugin only loads in admin context (`is_admin()` check in plugin.php)
- Direct `$wpdb` queries are used for username updates (WordPress doesn't provide a function for this)
- Username validation uses WordPress's built-in `validate_username()` and `username_exists()`
