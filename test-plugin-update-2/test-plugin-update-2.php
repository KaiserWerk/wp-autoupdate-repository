<?php
/*
Plugin Name: Test Update Plugin 2
Plugin URI: https://kaiserrobin.eu
Description: Test Update Plugin 2
Version: 0.7.3
Author: Robin Kaiser
Author URI: https://kaiserrobin.eu
*/

/**
 * @TODO functionen/variablen richtig benennen
 */

define('PLUGIN_CURRENT_VERSION', '0.7.3');
define('PLUGIN_SLUG', 'test-plugin-update-2');

function my_plugin_plugins_api( $false, $action, $args ) {
    
    // Check if this plugins API is about this plugin
    if( $args->slug !== PLUGIN_SLUG )
        return $false;
    
    // POST data to send to your API
    $args = array(
        'action' 	=> 'get-plugin-information'
    );
    
    // Send request for detailed information
    return my_plugin_api_request( $args );
    
}
add_filter( 'plugins_api', 'my_plugin_plugins_api', 10, 3 );

function my_plugin_update_plugins( $transient ) {
    
    // Check if the transient contains the 'checked' information
    // If no, just return its value without hacking it
    if ( empty( $transient->checked ) )
        return $transient;
    
    // The transient contains the 'checked' information
    // Now append to it information form your own API
    $plugin_path = plugin_basename( __FILE__ );
    
    // POST data to send to your API
    $args = array(
        'action' 	=> 'check-latest-version'
    );
    
    // Send request checking for an update
    $response = my_plugin_api_request( $args );
    
    // If there is a new version, modify the transient
    if( version_compare( $response->new_version, $transient->checked[$plugin_path], '>' ) )
        $transient->response[$plugin_path] = $response;
    
    return $transient;
    
}
add_filter( 'pre_set_site_transient_update_plugins', 'my_plugin_update_plugins' );

function my_plugin_api_request( $args ) {
    $args['slug'] = PLUGIN_SLUG;
    // Send request
    $request = wp_remote_post( 'http://localhost/projects/wp-autoupdate-repository/api.php', array( 'body' => $args ) );
    
    if ( is_wp_error( $request ) || 200 != wp_remote_retrieve_response_code( $request ) )
        return false;
    
    $response = unserialize( wp_remote_retrieve_body( $request ) , ['allowed_classes' => true]);
    
    if ( is_object( $response ) )
        return $response;
    else
        return false;
    
}