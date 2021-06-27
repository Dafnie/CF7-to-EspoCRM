<?php // exit if uninstall constant is not defined
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

// delete plugin options
global $wpdb;

$row = $wpdb->get_results( "SELECT * FROM `wp_options` WHERE `option_name` LIKE '%cf7toespo%'");

    foreach ( $row as $row ) 
    { 
        delete_option($row->option_name);
    }
