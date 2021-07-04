<?php

$regex = get_shortcode_regex( ['espo'] );
if( preg_match( '/'.$regex.'/s', $field ) ) {

    preg_match_all( '/'.$regex.'/s', $field, $matches );
    $siteinfo = get_option( $matches[5][0] );
    $field = preg_replace( '/'.$regex.'/s', $siteinfo, $field );
}