<?php
// this is where the magic happens.
function geoUserXMLData() {

	$getHundred = get_option('geoHundred');

	$usersXML = new SimpleXMLElement("<geoUsers></geoUsers>");
//	$newsXML->addAttribute('newsPagePrefix', 'value goes here');

	foreach($getHundred as $user) {
		$usersIntro = $usersXML->addChild('user');
		$usersIntro->addAttribute('lat', $user['Latitude']);
		$usersIntro->addAttribute('lon', $user['Longitude']);
		$usersIntro->addAttribute('ip', $user['IPAddress']);
		$usersIntro->addAttribute('city', $user['City']);
		$usersIntro->addAttribute('state', $user['State']);
		$usersIntro->addAttribute('isp', $user['Carrier']);
		$usersIntro->addAttribute('continent', $user['Continent']);
		$usersIntro->addAttribute('country', $user['Country']);
		$usersIntro->addAttribute('region', $user['Region']);
		$usersIntro->addAttribute('postalcode', $user['PostalCode']);
		$usersIntro->addAttribute('areacode', $user['AreaCode']);
	}

	header('Content-type: text/xml');
	echo $usersXML->asXML();
}

add_action('do_feed_geouserxml', 'geoUserXMLData', 10, 1); // Make sure to have 'do_feed_customfeed'

function geoStaticMap($zoom, $width, $height, $maptype, $marker = false) {
	$geoPosty = getGeoPosty();
	if (!is_array($geoPosty)) return false;

	if ($marker == 'on') $mapMarker = '&amp;markers='. $geoPosty['Latitude'] .','. $geoPosty['Longitude'];

	return '<img src="http://maps.google.com/maps/api/staticmap?center='. $geoPosty['Latitude'] .','. $geoPosty['Longitude'] .'&amp;zoom='. $zoom .'&amp;size='. $width .'x'. $height .'&amp;maptype='. $maptype .'&amp;sensor=false'.$mapMarker.'" class="geoMap" width="'.$width.'" height="'.$height.'" alt="GeoPosty &amp Google Generated Map" />';
}

function geoGoogleMap($zoom, $width, $height, $search, $results) {
	$geoPosty = getGeoPosty();
	if (!is_array($geoPosty)) return false;

	$output .= '<div id="geoPostyGoogleMap" style="width:'.$width.'px;height:'.$height.'px;"></div>';
	$output .= '<script type="text/javascript"> loadGoogleMap('.$geoPosty['Latitude'].', '. $geoPosty['Longitude'] .', '.$zoom.', \''.$search.'\', \''.$results.'\');  </script>'; //boo-urns

	return $output;
}

function geoUserMap($width, $height) {
	$geoPosty = getGeoPosty();
	if (!is_array($geoPosty)) return false;

	$output .= '<div id="geoPostyUserMap" style="width:'.$width.'px;height:'.$height.'px;"></div>';
	$output .= '<script type="text/javascript"> loadUserGoogleMap();  </script>'; //boo-urns

	return $output;
}

function geoZoomUserMap($width, $height) {
	$geoPosty = getGeoPosty();
	if (!is_array($geoPosty)) return false;

	$output .= '<div id="geoPostyZoomMap" style="width:'.$width.'px;height:'.$height.'px;"></div>';
	$output .= '<script type="text/javascript"> loadZoomUserGoogleMap();  </script>'; //boo-urns

	return $output;
}

function geoWeather($image, $measurement, $humidity, $wind) {
	$geoPosty = getGeoPosty();
	if (!is_array($geoPosty)) return false;

	$weather = getGeoPostyWeather();

	$output = '<span class="geoweather">';

	if ($image == 'on') $output .= '<span class="geoposty-weather-image"><img src="http://www.google.com' . $weather->{weather}->{current_conditions}->{icon}->attributes()->data . '" alt="'.$weather->{weather}->{current_conditions}->{condition}->attributes()->data.'" /></span><br />';
	$output .= '<strong class="geoposty-weather-condition">' . $weather->{weather}->{current_conditions}->{condition}->attributes()->data . ', <span class="';

	if ($measurement == 'Celcius') $output .= 'geopost-weather-celcius">' . $weather->{weather}->{current_conditions}->{temp_c}->attributes()->data;
	else $output .= 'geopost-weather-farenheit">' . $weather->{weather}->{current_conditions}->{temp_f}->attributes()->data ;

	$output .= '&deg;</span></strong><br />';

	if ($humidity == 'on') $output .= '<span class="geoposty-weather-humidity">' . $weather->{weather}->{current_conditions}->{humidity}->attributes()->data.'</span><br />';
	if ($wind == 'on') $output .= '<span class="geoposty-weather-wind">' . $weather->{weather}->{current_conditions}->{wind_condition}->attributes()->data . '</span>';

	$output .= '</span>';

	return $output;
}

function recordGeoStats($name) {
	$geoStats = get_option('geoStats');
	$geoStats[$name]++;
	update_option('geoStats', $geoStats);
}

// borrowed from http://roshanbh.com.np/2007/12/getting-real-ip-address-in-php.html
function getGeoIpAddress() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) $ip=$_SERVER['HTTP_CLIENT_IP'];
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	else $ip=$_SERVER['REMOTE_ADDR'];

	return $ip;
}

// borrowed from http://snipplr.com/view/2531/calculate-the-distance-between-two-coordinates-latitude-longitude/
function getGeoDistance($lat1, $lng1, $lat2, $lng2, $miles = true)
{
	$pi80 = M_PI / 180;
	$lat1 *= $pi80;
	$lng1 *= $pi80;
	$lat2 *= $pi80;
	$lng2 *= $pi80;
 
	$r = 6372.797; // mean radius of Earth in km
	$dlat = $lat2 - $lat1;
	$dlng = $lng2 - $lng1;
	$a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
	$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
	$km = $r * $c;
 
	return ($miles ? ($km * 0.621371192) : $km);
}

function geoDistanceFrom($address) {
	$geoPosty = getGeoPosty();
	if (!is_array($geoPosty)) return false;

	$latlng = geoGetAddressLocation($address);

	return getGeoDistance($geoPosty['Latitude'], $geoPosty['Longitude'], $latlng['latitude'], $latlng['longitude']);
}

?>
