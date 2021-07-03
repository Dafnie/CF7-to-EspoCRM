<?php
if( !defined( 'ABSPATH' ) ) exit;

// Send data to EspoCRM 
add_action( 'wpcf7_before_send_mail', function( $contact_form ) {

    $settings = get_option('cf7toespo-' . $contact_form->id);

    if (!$settings['espo_enable']) {
        return;
    }

    $submission = WPCF7_Submission::get_instance();
    $posted_data = $submission->get_posted_data();

    //Search for duplicate if set
    if ($settings['duplicate'] != "off") {
        $form_value = $posted_data[str_replace( 'cf7_', '', $settings['duplicate'] )];
        $espo_field = $settings['mapping'][ 'parent_' . $settings['duplicate'] ];
        $param = [
            'headers' => [
            'Content-Type' => 'application/json',
            'X-Api-Key' => $settings['espo_key']
            ],
            'body' => [
                'where' => [
                    [
                        'type' => 'equals',
                        'attribute' => $espo_field,
                        'value' => $form_value
                    ]
                ]
            ]
        ];
        
        $url = $settings['espourl'] . '/api/v1/' .  $settings['parent'];

        $response = wp_remote_get( $url, $param);    
        $response_body = json_decode( $response['body'] );
        $parentid = $response_body->list[0]->id;
    }
    
    // Send the main entity
    if ( $response_body->total == 0 || $settings['duplicate'] == 'off' ) { // Only create if the data in duplicate is not found

       $fields = cf7espo_fetch_fields( $settings['mapping'], 'parent_', $posted_data );

        $args = [
            'body' => wp_json_encode($fields),
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Api-Key' => $settings['espo_key']
            ]
        ];

        $url = $settings['espourl'] . '/api/v1/' .  $settings['parent'];
        $response = wp_remote_post( $url, $args );
        $parent_body = json_decode( $response['body'] );
        $parentid = $parent_body->id;
    }

    // Cteate the child entity
    if ( $settings['child'] != 'None' ) { // Only create if cild setting is not None
        $fields = cf7espo_fetch_fields( $settings['mapping'], 'child_', $posted_data );
        $body = array_merge([
            'parentId' => $parentid,
            'parentType' => $settings['parent']
        ],
        $fields );

        $args = [
            'body' => wp_json_encode( $body ),
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Api-Key' => $settings['espo_key']
            ]
        ];
    }

    $url = $settings['espourl'] . '/api/v1/' .  $settings['child'];
    $a_response = wp_remote_post( $url, $args );

}, 10, 1 );


function cf7espo_fetch_fields( $settings, $entity, $posted_data ) {

    $fields = [];
        //Build array with field data
        foreach ( $settings as $key=>$field ) {
            
            //Ignore espo
            if (preg_match("/espo$/", $key)) {
                continue;
            }
            //Rearange static field
            if (preg_match("/static$/", $key)) {
                $fields[$settings[$key . '_espo']] = $field;
                continue;
            }
            if ( substr( $key, 0, 4 ) == substr( $entity, 0, 4 ) ) {
                if ( $field != 'none' ) {
                    $key = $posted_data[str_replace( $entity, '', $key )];
                    $fields[$field] = $key;
                }
            }
        }
        return $fields;
}