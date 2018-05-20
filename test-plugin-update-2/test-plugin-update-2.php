<?php
/*
Plugin Name: Test Update Plugin 2
Plugin URI: https://kaiserrobin.eu
Description: Test Update Plugin 2
Version: 0.7.1
Author: Robin Kaiser
Author URI: https://kaiserrobin.eu
*/

add_filter('plugins_api', array( 'test_plugin_update_2', 'plugins_api'), 10, 3);
add_filter('pre_set_site_transient_update_plugins', array( 'test_plugin_update_2', 'update_plugins'));

class test_plugin_update_2 {
    
    private static $plugin_version = '0.7.1';
    private static $plugin_slug = 'test-plugin-update-2';
    private static $endpoint = 'http://localhost/projects_github/wp-autoupdate-repository/api.php';
    
    public static function plugins_api($false, $action, $args)
    {
        // Check if this plugins API is about this plugin
        if ($args->slug !== self::$plugin_slug) {
            return $false;
        }
        
        // POST data to send to your API
        $args = array(
            'action' => 'get-plugin-information',
        );
        
        // Send request for detailed information
        return self::api_request($args);
    }
    
    public static function update_plugins($transient)
    {
        // Check if the transient contains the 'checked' information
        // If no, just return its value without hacking it
        if (empty( $transient->checked)) {
            return $transient;
        }
        
        // The transient contains the 'checked' information
        // Now append to it information form your own API
        $plugin_path = plugin_basename(__FILE__);
        
        // POST data to send to your API
        $args = array(
            'action' => 'check-latest-version',
        );
        
        // Send request checking for an update
        $response = self::api_request($args);
        
        // If there is a new version, modify the transient
        if (version_compare( $response->new_version, $transient->checked[ $plugin_path ], '>')) {
            $transient->response[ $plugin_path ] = $response;
        }
        
        return $transient;
    }
    
    public static function api_request($args)
    {
        $args['slug'] = self::$plugin_slug;
        
        // Send request
        $request = wp_remote_post(self::$endpoint, array('body' => $args));
        
        if (is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200) {
            return false;
        }
        
        $response = unserialize(wp_remote_retrieve_body($request), ['allowed_classes' => true]);
        
        if (is_object($response)) {
            return $response;
        }
        
        return false;
    }
}