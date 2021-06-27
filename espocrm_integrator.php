<?php
/*
Plugin Name: CF7 to EspoCRM
Plugin URI: https://github.com/Dafnie/espocrm_integrator
Description: Send data from Contact Form 7 form to your EspoCRM installation
Version: 1.0
Author: Carsten Gjedde
Author URI: https://github.com/Dafnie
Text Domain: WPtoEspo
Domain Path: /lang
*/

if( !defined( 'ABSPATH' ) ) exit;

define('CF7_ESPO_IGNORE_fIELD', [
    'id',
    'deleted',
    'accountId',
    'campaignId',
    'createdById',
    'modifiedById',
    'parentId',
    'parentType',
    'parentName',
    'assignedUserName',
    'teamsNames',
    'accountName',
    'createdByName',
    'modifiedByName'
    ]);


register_activation_hook( __FILE__, function() {
    if ( !is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( __( 'This plugin require the <a href="https://da.wordpress.org/plugins/contact-form-7">Contact Form 7 plugin</a> to be active
        </br> Go <a href="' . get_site_url(null, '/wp-admin/plugins.php') . '">back</a> and aktivate the required plugin', 'WPtoEspo' ) );
    }
} );

add_action( 'admin_enqueue_scripts', function() {
    wp_enqueue_style( 'csscf7espo', plugin_dir_url( __FILE__ ) . 'theme/admin/css/style.css', false, '1.0' );
    wp_enqueue_script( 'jscf7espo', plugin_dir_url( __FILE__ ) . 'theme/admin/js/script.js', ['jquery'], '1.0' );
} );


// The setting panel in the CF7 form-setting
include_once('theme/admin/espo_settings.php');
// Code for sending data to EspoCRM
include_once('remote.php');