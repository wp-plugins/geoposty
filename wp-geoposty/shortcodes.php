<?php
add_shortcode('geoip', 'geoIP');
add_shortcode('geoisp', 'geoISP');
add_shortcode('geocontinent', 'geoContinent');
add_shortcode('geocountry', 'geoCountry');
add_shortcode('georegion', 'geoRegion');
add_shortcode('geostate', 'geoState');
add_shortcode('geocity', 'geoCity');
add_shortcode('geopostalcode', 'geoPostalCode');
add_shortcode('geoareacode', 'geoAreaCode');
add_shortcode('geolatitude', 'geoLatitude');
add_shortcode('geolongitude', 'geoLongitude');
add_shortcode('geostaticmap', 'geoStaticMapShortcode');
add_shortcode('geogooglemap', 'geoGoogleMapShortcode');
add_shortcode('geousermap', 'geoUserMapShortcode');
add_shortcode('geozoomusermap', 'geoZoomUserMapShortcode');
add_shortcode('geoweather', 'geoWeatherShortcode');
add_shortcode('geodistancefrom', 'geoDistanceFromShortcode');
add_shortcode('georredirect', 'geoRRedirect');


function geoRRedirect($attributes) {

	recordGeoStats('georredirect');

	$redirecttype = '';
	$goURL = '';
	
	extract(shortcode_atts(array(
		'redirectpage' => '',
		'redirecttype' => '',
		'redirecturl' => '',
		'miles' => '',
		'distancefrom' => ''
		
	), $attributes));
	//return $redirectpage . $redirecttype . $redirecturl . $miles . $distancefrom;
	//return geoDistanceFrom($distancefrom);
	if (!empty($distancefrom) && $miles > 1) {
		if (geoDistanceFrom($distancefrom) > $miles) return false;
	}
	if ($redirecttype == 'page') {
	
		$goURL = get_bloginfo('url') . '?page_id=' . $redirectpage;
	}
	if ($redirecttype == 'url') {
		$goURL = $redirecturl;
	}
	wp_redirect($goURL);
}

function geoDistanceFromShortcode($attributes) {

	recordGeoStats('distance');

	extract(shortcode_atts(array(
		'address' => '1600 Pennsylvania Ave, Washington, DC 20500'
	), $attributes));

	$geoPosty = getGeoPosty();
	if (!is_array($geoPosty)) return false;

	$addressLatLon = geoGetAddressLocation($address);

	return getGeoDistance($geoPosty['Latitude'], $geoPosty['Longitude'], $addressLatLon['latitude'], $addressLatLon['longitude']);
}

function geoStaticMapShortcode($attributes) {

	recordGeoStats('staticmap');

	extract(shortcode_atts(array(
		'zoom' => '6',
		'width' => '200',
		'height' => '200',
		'maptype' => 'roadmap'
	), $attributes));

	return geoStaticMap($zoom, $width, $height, $maptype);
}

function geoGoogleMapShortcode($attributes) {

	recordGeoStats('googlemap');

	extract(shortcode_atts(array(
		'zoom' => '6',
		'width' => '200',
		'height' => '200',
		'search' => '',
		'results' => '0',
		'miles' => '',
		'distancefrom' => ''
	), $attributes));

	if (!empty($distancefrom) && $miles > 1) {
		if (geoDistanceFrom($distancefrom) > $miles) return false;
	}

	return geoGoogleMap($zoom, $width, $height, $search, $results);
}

function geoUserMapShortcode($attributes) {

	recordGeoStats('usermap');

	extract(shortcode_atts(array(
		'width' => '200',
		'height' => '200'
	), $attributes));

	return geoUserMap($width, $height);
}

function geoZoomUserMapShortcode($attributes) {

	recordGeoStats('zoommap');

	extract(shortcode_atts(array(
		'width' => '200',
		'height' => '200'
	), $attributes));

	return geoZoomUserMap($width, $height);
}

function geoWeatherShortcode($attributes) {

	recordGeoStats('weather');

	extract(shortcode_atts(array(
		'image' => 'on',
		'measurement' => 'Farenheit',
		'humidity' => 'on',
		'wind' => 'on',
		'miles' => '',
		'distancefrom' => ''
	), $attributes));

	if (!empty($distancefrom) && $miles > 1) {
		if (geoDistanceFrom($distancefrom) > $miles) return false;
	}

	return geoWeather($image, $measurement, $humidity, $wind);
}

function geoIP() {
	recordGeoStats('ip');

	$geoPosty = getGeoPosty();
	return $geoPosty['IPAddress'];
}
function geoISP() {
	recordGeoStats('isp');

	$geoPosty = getGeoPosty();
	return $geoPosty['Carrier'];
}
function geoContinent() {
	recordGeoStats('continent');

	$geoPosty = getGeoPosty();
	return $geoPosty['Continent'];
}
function geoCountry() {
	recordGeoStats('country');

	$geoPosty = getGeoPosty();
	return $geoPosty['Country'];
}
function geoRegion() {
	recordGeoStats('region');

	$geoPosty = getGeoPosty();
	return $geoPosty['Region'];
}
function geoState() {
	recordGeoStats('state');

	$geoPosty = getGeoPosty();
	return $geoPosty['State'];
}
function geoCity() {
	recordGeoStats('city');

	$geoPosty = getGeoPosty();
	return $geoPosty['City'];
}
function geoPostalCode() {
	recordGeoStats('postal');

	$geoPosty = getGeoPosty();
	return $geoPosty['PostalCode'];
}
function geoAreaCode() {
	recordGeoStats('areacode');

	$geoPosty = getGeoPosty();
	return $geoPosty['AreaCode'];
}
function geoLatitude() {
	recordGeoStats('lat');

	$geoPosty = getGeoPosty();
	return $geoPosty['Latitude'];
}
function geoLongitude() {
	recordGeoStats('lon');

	$geoPosty = getGeoPosty();
	return $geoPosty['Longitude'];
}

?>
