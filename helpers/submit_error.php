<?php

if( $settings['form_error_message'] ) {

add_filter('wpcf7_display_message', function($message, $status) {

    $current_form = wpcf7_get_current_contact_form();
    $settings = get_option('cf7toespo-' . $current_form->id);
    $message = $settings['form_error_message'];

return $message;   
}, 10 ,2 );
}

trigger_error('EspoCRM at ' . $url . 'does not respond 200');
return;