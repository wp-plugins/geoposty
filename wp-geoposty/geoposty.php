<?php
/*
	Plugin Name: GeoPosty 
	Plugin URI: http://geoposty.com/
	Author URI: http://geoposty.com/
	Description: Provide users a more geographically rich experience with GeoPosty.  Leveraging IP geo location data from the Quova platform, you can provide users with maps, weather, business, and text not only relevant to your topic, but relevant to your user's location.  Widgets and shortcodes are preloaded to make implementation a snap.
	Version: 0.8
	Author: GeoPosty Team 
*/

// debug
error_reporting (E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);
// debug
require(dirname(__FILE__)  . '/functions.php');

$geoposty_api_key = get_option('geoposty_api_key');
$posty_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__));
$geoMD5 = md5(getGeoIpAddress());

// plugin won't work with PHP4
if (!version_compare(phpversion(), "5.0", ">=")) {

	function geoposty_php_warning() {
		echo "
			<div id='geoposty-warning' class='updated fade'><p><strong>".__('<a href="http://geoposty.com/">GeoPosty</a> needs PHP5!')."</strong> ". __('PHP4 was released in 2000, before Internet Explorer 6! You should contact your webhost and ask them to upgradde (with 2 Ds for a double dose of GeoPosty awesome) your software. You will receive this message until you deactive the plugin or update to at least PHP5. Sorry.') ."</p></div>
		";
	}
	add_action('admin_notices', 'geoposty_php_warning');	

} elseif (empty($geoposty_api_key)) {

	function geoposty_warning() {
		echo "
		<div id='geoposty-warning' class='updated fade'><p><strong>".__('GeoPosty needs your attention.')."</strong> ".sprintf(__('You must <a href="%1$s">enter your API credentials</a> for it to work.'), "plugins.php?page=geoposty-key-config")."</p></div>
			";
	}
	add_action('admin_notices', 'geoposty_warning');
	if (is_admin()) {
		require(dirname(__FILE__)  . '/admin.php');
		wp_enqueue_script('geopostyadminjs', $posty_plugin_url . "/js/geoposty-admin.js", array('jquery'));
	}

} else {

	require(dirname(__FILE__)  . '/curl.php');
	//if (is_admin()) 

	// get our quova data
	// $geoPostyXML = getGeoPostyXML($geoposty_api_key);

	require(dirname(__FILE__)  . '/widgets.php');
	require(dirname(__FILE__)  . '/shortcodes.php');
	require(dirname(__FILE__)  . '/reporting.php');

	if (!is_admin()) {
		// make sure we have jquery
		wp_enqueue_script('jquery');

		$geoGoogleKey = get_option('geoposty_google_api_key');

		// we need javascript for the google widgets!
		wp_register_script('googlejs', "http://www.google.com/jsapi");
		wp_enqueue_script('googlejs');

		// now the javascript that is fun
		wp_register_script('geopostyjs', $posty_plugin_url . "/js/geoposty.js");
		wp_enqueue_script('geopostyjs');
	} else {
		require(dirname(__FILE__)  . '/admin.php');
		wp_enqueue_script('geopostyadminjs', $posty_plugin_url . "/js/geoposty-admin.js", array('jquery'));
	}
}
?>
