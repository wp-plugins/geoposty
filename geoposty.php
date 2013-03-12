<?php
/*
	Plugin Name: Geoposty GeoLocation Widgets, Posts and Redirects

	Plugin URI: http://geoposty.com/

	Description: Provide users a more geographically rich experience with GeoPosty.  Leveraging IP geo location data from the Quova platform, you can provide users with maps, weather, business, and text not only relevant to your topic, but relevant to your user's location.  Widgets and shortcodes are preloaded to make implementation a snap.
	Version: 1.0.1

	Author: GeoPosty Team
	Author URI: http://geoposty.com/
*/

define('GDEBUG',false);
define('GEOSERVER', 'http://api.quova.com/');
define('GEOSERVER_VERSION', 'v1/');
define('GEOSERVER_METHOD','ipinfo/');
 
$timestamp = gmdate('U'); // 1200603038
$sig = md5($apikey . $secret . $timestamp);
$service = 'http://api.quova.com/';


require(dirname(__FILE__)  . '/functions.php');          

// need to compress all databae entries into single array 
$geoposty_neustar_api_key = get_option('geoposty_neustar_api_key');
$geoposty_neustar_api_secret = get_option('geoposty_neustar_api_secret');
$posty_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__));
$geoMD5 = md5(getGeoIpAddress());
if(GDEBUG) { error_log("geoposty: api_key=$geoposty_neustar_api_key"); }

if (isset($_GET['geokeysaved'])) {

	function geoposty_keysaved_note() {
		echo "
            <div id='geoposty-warning' class='updated fade'><p><strong>".__('Your Neustar IP Intelligence API key has been saved.')."</strong></p></div>
			";
	}
	add_action('admin_notices', 'geoposty_keysaved_note');

}

if (empty($geoposty_neustar_api_key) || empty($geoposty_neustar_api_secret)) {

	function geoposty_warning() {
		echo "
		<div id='geoposty-warning' class='updated fade'><p><strong>".__('GeoPosty needs your attention.')."</strong> ".sprintf(__('You must <a href="%1$s">enter your API credentials</a> for it to work.'), "plugins.php?page=geoposty-options")."</p></div>
			";
	}
	add_action('admin_notices', 'geoposty_warning');
	if (is_admin()) {
		require(dirname(__FILE__)  . '/admin.php');
		wp_enqueue_script('geopostyadminjs', $posty_plugin_url . "/js/geoposty-admin.js", array('jquery'));
		wp_enqueue_style('geopostyadmincss', $posty_plugin_url . "/css/geoposty-admin.css");
	}

} else {

	require(dirname(__FILE__)  . '/curl.php');
    	require(dirname(__FILE__)  . '/widgets.php');
	require(dirname(__FILE__)  . '/shortcodes.php');
	require(dirname(__FILE__)  . '/reporting.php');

	if (!is_admin()) {
		wp_enqueue_script('jquery'); // make sure we have jquery

		// we need javascript for the google widgets!
		wp_register_script('googlejs', "http://www.google.com/jsapi");
		wp_enqueue_script('googlejs');

		// now the javascript that is fun
		wp_register_script('geopostyjs', $posty_plugin_url . "/js/geoposty.js");
		wp_enqueue_script('geopostyjs');
	} else {
		require(dirname(__FILE__)  . '/admin.php');
		wp_enqueue_script('geopostyadminjs', $posty_plugin_url . "/js/geoposty-admin.js", array('jquery'));
		wp_enqueue_style('geopostyadmincss', $posty_plugin_url . "/css/geoposty-admin.css");

		// reserved for checking updates and sending users messages
		// add_action('after_plugin_row_' . plugin_basename(__FILE__),  'geoposty_plugin_row');
	}
}


register_uninstall_hook( __FILE__, 'geoposty_uninstall');

function geoposty_uninstall() {
	// remove the options we put into the database
	
	delete_option('geoposty_api_key');
	delete_option('geoposty_neustar_api_key');
	delete_option('geoposty_neustar_api_secret');
	delete_option('geoposty_tests');
	delete_option('geoposty_redirects');
	delete_option('geoLogging');
	delete_option('geoHundred');
	delete_option('geoStats');
	delete_option('geoAddressLocation'); 
	 
	// transient stuff will leave on its own, but what the heck
	if ( function_exists('reset_geo_cache') ) reset_geo_cache();   		
}

?>
