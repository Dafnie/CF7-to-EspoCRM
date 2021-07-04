<?php

// Display error message to user
if( $settings['form_error_message'] ) {

add_filter('wpcf7_display_message', function($message, $status) {

    $current_form = wpcf7_get_current_contact_form();
    $settings = get_option('cf7toespo-' . $current_form->id);
    $message = $settings['form_error_message'];

return $message;   
}, 10 ,2 );
}

//Send faldback email
if( $settings['error_email'] ) {

    $headers = 'Content-type: text/html; charset=iso-8859-1;';
    $message = '<p>The form submission from <strong>' . $_SERVER['HTTP_REFERER'] . '</strong> failed to send data to <strong>' . $settings['espourl'] . '</strong></p></br>';
    $message .= '-- The message --</br></br>';
    foreach ( $posted_data as $key=>$value ) {
        $message .= '<p>' . $key . ' : ' . $value . '</p>';
    }

    error_log( $message, 1, $settings['error_email'], $headers );
}
error_log('EspoCRM at ' . $url . ' does not respond 200');
return;