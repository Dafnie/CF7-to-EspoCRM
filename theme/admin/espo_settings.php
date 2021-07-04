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
    
    $type = sanitize_key( $_POST['parent'] );
    $response = cf7espo_fetch_espokeys( $type );

    if ( is_wp_error($response) ) {
        $error->add( 'bad_url', $response->get_error_messages()[0] );
    } elseif ( $response['response']['code'] == 401 ) {
        $error->add( 'unauthorized', __('You are not authorized. Bad API key', 'wptoespo') );
    } elseif ( $response['response']['code'] == 403 ) {
        $error->add( 'no_entity', __('There are no data in Espo-type <strong>' . $type . '</strong>. There has to be at least one entity in your EspoCRM to fetch data', 'wptoespo') );
    }

    if ( !$error->has_errors() ) {
        $parent_body = json_decode( $response['body'], true );
    }

    $type = sanitize_key( $_POST['child'] );
    if ( $type != 'none') {
        $response = cf7espo_fetch_espokeys( $type );
        
        if ( !$error->has_errors() ) {
            $child_body = json_decode( $response['body'], true );
        }
        if ( $child_body['total'] == 0 ) {
            $error->add( 'No_entity', __('There are no data in Espo-type <strong>' . $type . '</strong>. There has to be at least one entity in your EspoCRM to fetch data', 'wptoespo') );
        }
    }

    if ( !is_email( $_POST['error_email'] ) ) {
        $error->add( 'malformattet email', __('The email is not at valid emailadress', 'wptoespo') );
    }
        

    //Build array from fieldmapping
    $fields = array_filter($_POST, function($key) {
        $arg = [];
        $arg = sanitize_key( strpos($key, 'parent_') === 0 );
        $arg .= sanitize_key( strpos($key, 'child_') === 0 );
        return $arg;  //prefix added to identifing the form fields
    }, ARRAY_FILTER_USE_KEY );

    //Remove input if empty
    $fields = array_filter($fields, function($value) {
        $arg = [];
        $arg = $value != '';
        return $arg;
    } );

    $data = [
        'espo_enable' => isset($_POST['espo_enable']),
        'espourl' => esc_url( $_POST['espourl'], ['http', 'https'] ),
        'espo_key' => sanitize_key( $_POST['espo_key'] ) ,
        'parent' => sanitize_text_field( $_POST['parent'] ),
        'child' => sanitize_text_field( $_POST['child'] ),
        'parent_espofilds' => $parent_body['list'],
        'child_espofilds' => $child_body['list'],
        'mapping' => array_map( 'esc_html', $fields ),
        'duplicate' => sanitize_text_field( $_POST['duplicate'] ),
        'error' => ( is_wp_error($error) ) ? $error->get_error_messages() : '',
        'form_error_message' => sanitize_text_field( $_POST['form_error_message'] ),
        'error_email' => sanitize_text_field( $_POST['error_email'] )
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

    $optionId = 'cf7toespo-' . sanitize_key( $_GET['post'] );
    $option = get_option( $optionId );

    if ( $option['error'] ) {
        ?>
        <div class="error">
         <p><?php _e( 'Ops, something went wrong.', 'wptoespo' ); ?></p>
            <ul>
                <li> -> <?php echo implode( '</li><li>', $option['error'] ); ?></li>
            </ul>
            <p><?php _e('"Send to EspoCRM" has been disabled by the plugin', 'wptoespo'); ?></p>
        </div>
        <?php
        $option['espo_enable'] = false;
        update_option( $optionId, $option );
    }  
} );


function cf7espo_fetch_espokeys( $entity ) {

    $url = esc_url( $_POST['espourl'] . '/api/v1/' .  $entity . '?maxSize=1' );
    $response = wp_remote_get( $url, [
        'headers' => [
        'X-Api-Key' => sanitize_key( $_POST['espo_key'] )
    ]]);

    return $response;
}
