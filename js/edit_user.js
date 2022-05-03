jQuery(document).ready(function(){

    var username = jQuery('#user_login').val();
    var display_name = jQuery('#display_name [selected="selected"]').text();
    var names_are_the_same = false;
    if ( username == display_name ) {
        // console.log('username and display name are the same');
        names_are_the_same = true;
    }


    jQuery('.user-user-login-wrap .description').html('<input type="button" value="&#x270E;" id="edit_username_button" name="edit_username">');
    // jQuery('.user-user-login-wrap .description').html('<span id="edit_username_button">&#x270E;</span>');
    $edit_username_button = jQuery('#edit_username_button');
    $edit_username_button.css({
        'font-size' : '.8rem',
        'cursor' : 'pointer'
    });

    $edit_username_field = jQuery('#user_login');

    $edit_username_button.on('click', function(){
        $edit_username_field.attr('disabled', false);
    });

    // Update display name if username has been edited and display name had previously been the same
    if (names_are_the_same){
        $edit_username_field.change(function(){
            var new_username = jQuery(this).val();
            if ( new_username != username ) {
                jQuery('#display_name [selected="selected"]').text(new_username);
            }
            console.log(jQuery(this).val());
        });
    }
});

