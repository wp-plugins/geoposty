<?php

function getGeoPosty() {
	global $geoMD5, $geoposty_neustar_api_key, $geoposty_neustar_api_secret;

	if (!geoSpiderDetect()) return false;

	$geoPosty = get_transient('geo-' . $geoMD5);

	// only query quova if needed
	if (!is_array($geoPosty)) {
		// should replace the _SERVER variables (or at least do a bunch of checking on them).  also add some better error checking

		$ip = getGeoIpAddress();
		$timestamp = gmdate('U'); // 1200603038
		$sig = md5($geoposty_neustar_api_key  . $geoposty_neustar_api_secret . $timestamp);
		//$server = GEOSERVER .'domain='. $host .'&ip='. $ip .'&domainkey=' . $geoposty_neustar_api_key;
		$server = GEOSERVER . GEOSERVER_VERSION . GEOSERVER_METHOD . $ip .'/?apikey=' . $geoposty_neustar_api_key . '&sig=' . $sig . '&format=xml';
		
		/*$service . $ver. $method. $ipin . '?apikey=' .
             $apikey . '&sig='.$sig . '&format=xml'*/
		if(GDEBUG) { error_log("geoposty:curl:getGeoPosty ip=$ip server=$server"); }
		$data = wp_remote_retrieve_body(wp_remote_get($server));
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
		
		//print_r( $geoPostyXML );
		
		$geoPosty = array(
			'IPAddress' => (string)$geoPostyXML->{ip_address},
			'Carrier' => ucwords($geoPostyXML->{Network}->{carrier}),
			'Continent' => ucwords($geoPostyXML->{Location}->{continent}),
			'Country' => strtoupper($geoPostyXML->{Location}->{CountryData}->{country_code}),  
			'Region' => ucwords($geoPostyXML->{Location}->{region}),
			'State' => strtoupper($geoPostyXML->{Location}->{StateData}->{state_code}),
			'City' => ucwords($geoPostyXML->{Location}->{CityData}->{city}),
			'PostalCode' => (string)$geoPostyXML->{Location}->{CityData}->{postal_code},
			'AreaCode' => (string)$geoPostyXML->{Location}->{CityData}->{area_code},
			'Latitude' => (string)$geoPostyXML->{Location}->{latitude},
			'Longitude' => (string)$geoPostyXML->{Location}->{longitude}
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
		$data = wp_remote_retrieve_body(wp_remote_get('http://www.google.com/ig/api?hl=en&weather=' . urlencode($geoPosty['PostalCode'])));

		$geoPostyWeatherXML = @simplexml_load_string($data);

		if (!$geoPostyWeatherXML) return false;

		// cache weather info for 2 hours per IP
		set_transient('geoWeather-' . $geoMD5, $data, 60*60*2);
	} 

	if (empty($geoPostyWeatherXML)) $geoPostyWeatherXML = @simplexml_load_string($data);

	return $geoPostyWeatherXML;
}

function geoGetAddressLocation($address) {
	global $geoposty_neustar_api_key;

	$addressMD5 = md5($address);
	$getAddressLocation = get_option('geoAddressLocation');

	if ($getAddressLocation[$addressMD5]) return $getAddressLocation[$addressMD5];
	else {
		$server = SERVER . 'geosearch.php?domainkey='.$geoposty_neustar_api_key.'&q='.urlencode($address);
		if(GDEBUG) { error_log("geoposty:curl:geoGetAddressLocation: server=$server "); }
		$data = wp_remote_retrieve_body(wp_remote_get($server));

		if (trim($data) == 'Error: invalid query') return false;

		$addressLatLon = json_decode($data);

		$getAddressLocation[$addressMD5]['longitude'] = $addressLatLon['0']['4'];
		$getAddressLocation[$addressMD5]['latitude'] = $addressLatLon['0']['3'];

		update_option('geoAddressLocation', $getAddressLocation);

		return $getAddressLocation[$addressMD5];
	}
}


?>
