<?php

$regex = get_shortcode_regex( ['espo'] );
if( preg_match( '/'.$regex.'/s', $field ) ) {

    preg_match_all( '/'.$regex.'/s', $field, $matches );

    if (in_array( $matches[5][0], CF7_ESPO_ALLOWED_SHORTCODE_ATT) ) {
            $siteinfo = get_bloginfo( $matches[5][0] );
            $field = preg_replace( '/'.$regex.'/s', $siteinfo, $field );
        }
    }