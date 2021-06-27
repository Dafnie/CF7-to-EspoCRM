<?php
if( !defined( 'ABSPATH' ) ) exit;

//Filter for adding the setting panel
add_filter( "wpcf7_editor_panels" ,function($panels) {
    
    $panels["espo_panel"] = [
        'title'    => __( 'EspoCRM Integration' , 'wptoespo' ),
        'callback' => function () {           
            require_once( plugin_dir_path(__DIR__) . '/admin/admin.php');
        }
    ];

    return $panels;
}, 1, 1);


// Saves settings after save
add_action( 'wpcf7_after_save', function( $instance ) {

    $error = new WP_Error();
    
    $response = _fetch_espokeys( $_POST['parent'] );
    if( is_wp_error($response) ) {
        $error->add( 'bad_url', $response->get_error_messages()[0] );
    } elseif ( $response['response']['code'] == 401 ) {
        $error->add( 'unauthorized', 'You are not authorized. Bad API key' );
    }

    if ( !$error->has_errors() ) {
        $parent_body = json_decode( $response['body'], true );
    }
    
    $response = _fetch_espokeys( $_POST['child'] );
    if ( !$error->has_errors() ) {
        $child_body = json_decode( $response['body'], true );
    }
    

    //Build array from fieldmapping
    $fields = array_filter($_POST, function($key) {
        $arg = [];
        $arg = strpos($key, 'parent_') === 0;
        $arg .= strpos($key, 'child_') === 0;
        return $arg;  //prefix added to identifi the form fields
    }, ARRAY_FILTER_USE_KEY);

    //Remove input if empty
    $fields = array_filter($fields, function($value) {
        $arg = [];
        $arg = $value != '';
        return $arg;  //prefix added to identifi the form fields
    } );

    $data = [
        'espo_enable' => isset($_POST['espo_enable']),
        'espourl' => esc_url( $_POST['espourl'], ['http', 'https'] ),
        'espo_key' => sanitize_user( $_POST['espo_key'] ) ,
        'parent' => sanitize_text_field( $_POST['parent'] ),
        'child' => sanitize_text_field( $_POST['child'] ),
        'parent_espofilds' => $parent_body['list'],
        'child_espofilds' => $child_body['list'],
        'mapping' => array_map( 'esc_html', $fields ),
        'duplicate' => sanitize_text_field( $_POST['duplicate'] ),
        'error' => ( is_wp_error($error) ) ? $error->get_error_messages() : ''
    ];

    update_option( 'cf7toespo-' . $instance->id, $data );
   
}, 10, 1 );

// When deleting a form. Delete option in DB 
add_action('before_delete_post', function($postid, $postobject) {
    if ( $postobject->post_type == 'wpcf7_contact_form' ) {
        delete_option( 'cf7toespo-' . $postid) ;
    }
}, 10, 2 );


// Admin Notices
add_action( 'wpcf7_admin_notices', function() {
    $option = get_option( 'cf7toespo-' . esc_html( $_GET['post'] ) );

    if ($option['error']) {
        echo '<div class="error"> <p>' . __( 'Ops, something went wrong.', 'wptoespo' ) . '</p>';
	echo '<ul>';
		echo '<li> -> ' . implode( '</li><li>', $option['error'] ) . '</li>';
	echo '</ul> </div>';
    }
    
}, 10, 2 );


function _fetch_espokeys( $entity ) {

    $url = esc_url( $_POST['espourl'] . '/api/v1/' .  $entity . '?maxSize=1' );
    $response = wp_remote_get( $url, [
        'headers' => [
        'X-Api-Key' => esc_html( $_POST['espo_key'] )
    ]]);

    return $response;
}
