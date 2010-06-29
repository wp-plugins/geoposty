<?php

function getGeoPosty() {
	global $geoMD5, $geoposty_api_key;

	$geoPosty = get_transient('geo-' . $geoMD5);

	// only query quova if needed
	if (!is_array($geoPosty)) {
		// should replace the _SERVER variables (or at least do a bunch of checking on them).
		// also add some better error checking
		$data = wp_remote_retrieve_body(wp_remote_get('http://api.geoposty.com/geo.php?domain='. $_SERVER['HTTP_HOST'] .'&ip='. getGeoIpAddress() .'&domainkey=' . $geoposty_api_key));

		$geoPostyXML = @simplexml_load_string($data);

		if (!$geoPostyXML) {
			// check what was returned in $data
			// log that data into a geoposty option
			// report that in admin?
			$geoLogging = get_option('geoLogging');
			if (!is_array($geoLogging)) $geoLogging = array();

			$geoLogCount = count($geoLogging)+1;
			
			$geoLogging[$geoLogCount]['time'] = time();
			$geoLogging[$geoLogCount]['message'] = trim($data);

			update_option('geoLogging', $geoLogging);

			return false;
		}

		// convert the simpleXML object to an array
		// can't serialize PHP built-in objects :(
		// fix case
		$geoPosty = array(
			'IPAddress' => (string)$geoPostyXML->{IPAddress},
			'Carrier' => ucwords($geoPostyXML->{Network}->{Carrier}),
			'Continent' => ucwords($geoPostyXML->{Location}->{Continent}->{Name}),
			'Country' => strtoupper($geoPostyXML->{Location}->{Country}->{Name}),
			'Region' => ucwords($geoPostyXML->{Location}->{Region}->{Name}),
			'State' => strtoupper($geoPostyXML->{Location}->{State}->{Name}),
			'City' => ucwords($geoPostyXML->{Location}->{City}->{Name}),
			'PostalCode' => (string)$geoPostyXML->{Location}->{City}->{PostalCode},
			'AreaCode' => (string)$geoPostyXML->{Location}->{City}->{AreaCode},
			'Latitude' => (string)$geoPostyXML->{Location}->{City}->{Coordinates}->{Latitude},
			'Longitude' => (string)$geoPostyXML->{Location}->{City}->{Coordinates}->{Longitude}
		);

		// cache quova info for 24 hours per IP
		set_transient('geo-' . $geoMD5, $geoPosty, 60*60*24);

		// save out last 100 visitors for fun & profit
		$geoLastHundred = get_option('geoHundred');
		
		if (!is_array($geoLastHundred)) $geoLastHundred = array();

		@array_unshift($geoLastHundred, $geoPosty);
		unset($geoLastHundred[100]);
		update_option('geoHundred', $geoLastHundred);
	} 

	return $geoPosty;
}

// update this to cache the object once we figure out the above bug.
function getGeoPostyWeather() {
	global $geoMD5;

	$geoPosty = getGeoPosty();

	$data = get_transient('geoWeather-' . $geoMD5);

	if ($data === false) {
		$data = wp_remote_retrieve_body(wp_remote_get('http://www.google.com/ig/api?hl=en&weather=' . $geoPosty['PostalCode']));

		$geoPostyWeatherXML = @simplexml_load_string($data);

		if (!$geoPostyWeatherXML) return false;

		// cache weather info for 2 hours per IP
		set_transient('geoWeather-' . $geoMD5, $data, 60*60*2);
	} 

	if (empty($geoPostyWeatherXML)) $geoPostyWeatherXML = @simplexml_load_string($data);

	return $geoPostyWeatherXML;
}

function geoGetAddressLocation($address) {

	$addressMD5 = md5($address);
	$getAddressLocation = get_option('geoAddressLocation');

	if ($getAddressLocation[$addressMD5]) return $getAddressLocation[$addressMD5];
	else {
		// this is an attempt to stay off of google's blacklist.
		// if you request information too quickly, they'll block you.
		if (get_transient('geoGoogleThrottle') != 'true') {

			set_transient('geoGoogleThrottle', 'true', 30);

			$data = wp_remote_retrieve_body(wp_remote_get('http://maps.google.com/maps/geo?q='.urlencode($address).'&output=xml'));

			$geoPostyXML = @simplexml_load_string($data);
			if (!$geoPostyXML) return false;
			if ($geoPostyXML->{Response}->{Status}->{code} != '200') return false;

			list($longitude, $latitude, $elevation) = explode(',',$geoPostyXML->{Response}->{Placemark}->{Point}->{coordinates});
	
			$getAddressLocation[$addressMD5]['longitude'] = $longitude;
			$getAddressLocation[$addressMD5]['latitude'] = $latitude;

			update_option('geoAddressLocation', $getAddressLocation);

			return $getAddressLocation[$addressMD5];
		}
	}
}

function geoAdminStats($type) {
	global $geoposty_api_key;

	$data = wp_remote_retrieve_body(wp_remote_get('http://api.geoposty.com/geo.php?domain='. $_SERVER['HTTP_HOST'] .'&domainkey=' . $geoposty_api_key . '&stats=' . $type));

	return json_decode($data);
}

/*
add_action('wp_footer', 'doFoot');

function doFoot() {
	echo '<pre>';
	$geoLogging = get_option('geoLogging');
	print_r($geoLogging);
	echo '</pre>';
}
*/

?>
