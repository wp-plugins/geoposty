<?php

add_action('media_buttons_context', 'geoposty_shortcode_helper');

function geoposty_shortcode_helper($base) {
	global $posty_plugin_url;

	$output = '<a href="#TB_inline?width=450&inlineId=geoposty_helper" class="thickbox" title="' . __("Add GeoPosty Shortcode") . '"><img src="'.$posty_plugin_url.'/images/icon-small.png" alt="' . __("Add GeoPosty Shortcodes") . '" /></a>';
	return $base . $output;
}

add_action('admin_footer',  'geoposty_shortcodes');

function geoposty_shortcodes(){
?>

	<script>
	function InsertGeoShortCode(){
		var shortcode = jQuery("#add_geo_shortcode").val();
		var shortoptgroup = jQuery("#add_geo_shortcode").find("option:selected").parent().attr("label");
		var shortattr = '';

		//var form_name = jQuery("#add_form_id option[value='" + form_id + "']").text().replace(" ", "");
		//var display_title = jQuery("#display_title").is(":checked");
		//var display_description = jQuery("#display_description").is(":checked");
		//var title_qs = !display_title ? " title=false" : "";
		//var description_qs = !display_description ? " description=false" : "";

		if (shortoptgroup == 'Map Based Shortcodes') {
			if (jQuery('#geoWidgetWidth').val() > 0) shortattr += ' width="'+ jQuery('#geoWidgetWidth').val() +'"';
			if (jQuery('#geoWidgetHeight').val() > 0) shortattr += ' height="'+ jQuery('#geoWidgetHeight').val() +'"';
			if (jQuery('#geoWidgetZoom').val() > 0) shortattr += ' zoom="'+ jQuery('#geoWidgetZoom').val() +'"';
			if (jQuery('#geoWidgetResults').val() > 0) shortattr += ' results="'+ jQuery('#geoWidgetResults').val() +'"';
			if (jQuery('#geoWidgetSearch').val() != '') shortattr += ' search="'+ jQuery('#geoWidgetSearch').val() +'"';
			if (jQuery('#geoWidgetRadiusAddress').val() != '') shortattr += ' distancefrom="'+ jQuery('#geoWidgetRadiusAddress').val() +'"';
			if (jQuery('#geoWidgetRadiusDistance').val() > 0) shortattr += ' miles="'+ jQuery('#geoWidgetRadiusDistance').val() +'"';
		} else if (shortoptgroup == 'Weather Based Shortcodes') {
			if (jQuery('#geoWidgetImage').is(':checked')) shortattr += ' image="on"';
			if (jQuery('#geoWidgetHumidity').is(':checked')) shortattr += ' humidity="on"';
			if (jQuery('#geoWidgetWind').is(':checked')) shortattr += ' wind="on"';
			shortattr += ' measurement="'+ jQuery('#geoWidgetMeasurement').val() +'"';
			if (jQuery('#geoWidgetRadiusAddress').val() != '') shortattr += ' distancefrom="'+ jQuery('#geoWidgetRadiusAddress').val() +'"';
			if (jQuery('#geoWidgetRadiusDistance').val() > 0) shortattr += ' miles="'+ jQuery('#geoWidgetRadiusDistance').val() +'"';
			
		} 
		// PRIMER '1' FOR ADDED CODE M PILON
		  else if (shortoptgroup == 'Redirection Shortcodes') {
			if (jQuery('#destinationPage').val()  != '') shortattr += ' redirectpage="' + jQuery('#destinationPage').val() + '"';
			if (jQuery('input:radio[name=redirectType]:checked').val() != '') shortattr += ' redirecttype="' + jQuery('input:radio[name=redirectType]:checked').val() + '"';
			if (jQuery('#redirectURL').val() != '') shortattr += ' redirecturl="'+ jQuery('#destinationURL').val() +'"';
			if (jQuery('#geoWidgetRadiusAddress').val() != '') shortattr += ' distancefrom="'+ jQuery('#geoWidgetRadiusAddress').val() +'"';
			if (jQuery('#geoWidgetRadiusDistance').val() > 0) shortattr += ' miles="'+ jQuery('#geoWidgetRadiusDistance').val() +'"';
		}
		// BELOW ADDED BY M PILON 6/24/2010

		var win = window.dialogArguments || opener || parent || top;
		win.send_to_editor("[" + shortcode + shortattr +"]");
	    }

	function geoShortCodeForm(){
		var shortcode = jQuery("#add_geo_shortcode").val();
		var shortoptgroup = jQuery("#add_geo_shortcode").find("option:selected").parent().attr("label");

		if (shortoptgroup == 'Map Based Shortcodes') {
			jQuery('#geoMapOptions').slideDown();
			jQuery('#geoRadiusLimit').slideDown();
			jQuery('#geoWeatherOptions').slideUp();
			jQuery('#geoRedirectOptions').slideUp();
		} else if (shortoptgroup == 'Weather Based Shortcodes') {
			jQuery('#geoMapOptions').slideUp();
			jQuery('#geoRadiusLimit').slideDown();
			jQuery('#geoWeatherOptions').slideDown();
			jQuery('#geoRedirectOptions').slideUp();
		} else if (shortoptgroup == 'Redirection Shortcodes') {
			jQuery('#geoMapOptions').slideUp();
			jQuery('#geoRadiusLimit').slideDown();
			jQuery('#geoWeatherOptions').slideUp();	
			jQuery('#geoRedirectOptions').slideDown();
		} else {
			jQuery('#geoMapOptions').slideUp();
			jQuery('#geoRadiusLimit').slideUp();
			jQuery('#geoWeatherOptions').slideUp();
			jQuery('#geoRedirectOptions').slideUp();
		}
	}

	</script>

	<div id="geoposty_helper" style="display:none;">
	    <div class="wrap">
		<div>
		    <div style="padding:15px 15px 0 15px;">
			<h3><?php _e("GeoPosty Shortcodes"); ?></h3>
		    </div>
		    <div style="padding:15px 15px 0 15px;">
			<select id="add_geo_shortcode" onChange="geoShortCodeForm();">
			    <option value="">  <?php _e("Select a shortcode"); ?>  </option>
				<optgroup label="Basic Shortcodes">
					<option value="geoip">IP</option>
					<option value="geoisp">ISP</option>
					<option value="geocontinent">Continent</option>
					<option value="geocountry">Country</option>
					<option value="georegion">Region</option>
					<option value="geostate">State</option>
					<option value="geocity">City</option>
					<option value="geopostalcode">Postal Code</option>
					<option value="geoareacode">Area Code</option>
					<option value="geolatitude">Latitude</option>
					<option value="geolongitude">Longitude</option>
				</optgroup>
				<optgroup label="Map Based Shortcodes">
					<option value="geogooglemap">Google Business Map</option>
				</optgroup>
				<optgroup label="Weather Based Shortcodes">
					<option value="geoweather">Weather</option>
				</optgroup>
				<!---- below added by M PILON 6/25/2010 -->
				<optgroup label="Redirection Shortcodes">
						<option value="georredirect">Radius Based Redirect</option>

				</optgroup>
				<!--- end added -->
				

			</select> <br/>
		    </div>

			<div id="geoMapOptions" style="display:none;padding:15px 15px 0 15px;">
				<input type="text" id="geoWidgetWidth" maxlength="4" size="3"  /> <label for="geoWidgetWidth"><?php _e("Width"); ?></label><br />
				<input type="text" id="geoWidgetHeight" maxlength="4" size="3"  /> <label for="geoWidgetHeight"><?php _e("Height"); ?></label><br />
				<input type="text" id="geoWidgetZoom" maxlength="2" size="2"  /> <label for="geoWidgetZoom"><?php _e("Zoom"); ?></label><br />
				<input type="text" id="geoWidgetResults" maxlength="1" size="1"  /> <label for="geoWidgetResults"><?php _e("Results"); ?></label><br />
				<input type="text" id="geoWidgetSearch" maxlength="80" size="25"  /> <label for="geoWidgetSearch"><?php _e("Search"); ?></label><br />
			</div>

			<div id="geoWeatherOptions" style="display:none;padding:15px 15px 0 15px;">
				<input type="checkbox" id="geoWidgetImage"  /> <label for="geoWidgetImage"><?php _e("Image"); ?></label><br />
				<input type="checkbox" id="geoWidgetHumidity"  /> <label for="geoWidgetHumidity"><?php _e("Humidity"); ?></label><br />
				<input type="checkbox" id="geoWidgetWind"  /> <label for="geoWidgetWind"><?php _e("Wind"); ?></label><br />
				<select id="geoWidgetMeasurement"><option value="Fahenheit">Fahenheit</option><option value="Celsuis">Celsius</option> </select> <label for="geoWidgetMeasurement"><?php _e("Measurement"); ?></label><br />
			</div>

			<div id="geoRadiusLimit" style="display:none;padding:15px 15px 0 15px;">
				<h4>Radius Based Filtering</h4>

				<input type="text" id="geoWidgetRadiusAddress" maxlength="80" size="25"  /> <label for="geoWidgetRadiusAddress"><?php _e("Address"); ?></label><br />
				<input type="text" id="geoWidgetRadiusDistance" maxlength="5" size="3"  /> <label for="geoWidgetRadiusDistance"><?php _e("Distance"); ?></label>
			</div>
			
			
			<!-- added by matt pilon 6/25/2010 -->
			<div id="geoRedirectOptions" style="display:none; padding:15px 15px 0 15px;">
					<h4>Redirect users meeting this criterion to:</h4>
					<input type="radio" name="redirectType" value="page">&nbsp; &nbsp; This page: &nbsp; &nbsp;<?php wp_dropdown_pages(array("name" => 'destinationPage', "show_option_none" => "Select a page", "selected" => '')); ?></label><br />&nbsp; &nbsp;&nbsp; &nbsp;-OR-<br>
					<input type="radio" name="redirectType" value="url">&nbsp; &nbsp;This URL:&nbsp; &nbsp; <input type="text" id="destinationURL">	 
			</div>
			<!-- end added by matt pilon -->
			
		    <div style="padding:15px;">
			<input type="button" class="button-primary" value="Insert Shortcode" onclick="InsertGeoShortCode();"/>&nbsp;&nbsp;&nbsp;
		    <a class="button" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php _e("Cancel"); ?></a>
		    </div>
		</div>
	    </div>
	</div>

<?php
}

add_action('wp_ajax_geo_register', 'geo_ajax_register');
add_action('wp_ajax_geo_confirm', 'geo_ajax_confirm');
add_action('wp_ajax_geo_followup', 'geo_ajax_followup');

function geo_ajax_register() {
	global $current_user;

	$geoRequestURL = 'http://api.geoposty.com/geo.php?domain='. $_SERVER['HTTP_HOST'] .'&email='. $current_user->user_email .'&nick=' . urlencode($current_user->display_name);

	$data = trim(wp_remote_retrieve_body(wp_remote_get($geoRequestURL)));

	if ($data == '1') {
		echo 'Thanks! We sent your API key to ' . $current_user->user_email . '. If you need any help with that, please <a href="http://geoposty.com/contact/">contact us</a>.';
	} else {
		echo 'Oh no! There was some type of problem. Please <a href="http://geoposty.com/contact/">contact us</a> so we can help you out. Please tell us you received the <strong>' . $data . '</strong> message';

		echo '<br />' . $geoRequestURL;

	}

	die();
}

function geo_ajax_confirm() {
	global $current_user, $_GET;

	$geoRequestURL = 'http://api.geoposty.com/geo.php?domain='. $_SERVER['HTTP_HOST'] .'&ip='. getGeoIPAddress() .'&domainkey=' . $_GET['domainkey'];


	$data = trim(wp_remote_retrieve_body(wp_remote_get($geoRequestURL)));

	$geoPostyXML = @simplexml_load_string($data);

	if (!$geoPostyXML) {
		echo 'There was some type of problem with your request. The API said: <em>' . $data . '</em>';
	}

	die();
}

function geo_ajax_followup() {
	global $current_user;

	$geoRequestURL = 'http://api.geoposty.com/geo.php?domain='. $_SERVER['HTTP_HOST'] .'&email='. $current_user->user_email .'&confirm=1';

	$data = trim(wp_remote_retrieve_body(wp_remote_get($geoRequestURL)));

	echo $data;

	die();
}

add_action('admin_menu', 'geoposty_config_page');
function geoposty_config_page() {
	global $posty_plugin_url;

	if ( function_exists('add_menu_page') )
		add_menu_page(__('GeoPosty Account Manager'), __('GeoPosty'), 'manage_options', 'geoposty-key-config', 'geoposty_conf', $posty_plugin_url . '/images/icon.png');

}

function geoCachingPlugins() {
	require_once(ABSPATH.'/wp-admin/admin-functions.php');

	$plugins = get_plugins();

	foreach($plugins as $plug) {
		$cacher = $plug['Name'] . $plug['Description'];

		if (stristr($cacher, 'cache')) return true;
	}

	return false;
}


function geoStatsGraph($type, $count) {
	$geoDailyStats = geoAdminStats($type);

	$geoMaxDay = 0;
	$geoDayCount = 0;

	foreach ($geoDailyStats as $geoDay) {

		$geoDayCount++;
		if ($geoDayCount == $count) break;

		if ($type == 'd') {
			$arrayGeoStats[] = $geoDay['1'];
			$arrayGeoDays[] = date('m-d', strtotime($geoDay['0']));
			if ($geoMaxDay < $geoDay['1']) $geoMaxDay = $geoDay['1'];
		} else {
			$arrayGeoStats[] = $geoDay['2'];
			$arrayGeoDays[] = $geoDay['0'];
			if ($geoMaxDay < $geoDay['2']) $geoMaxDay = $geoDay['2'];
		}
	}

	$displayGeoStats = array_reverse($arrayGeoStats);
	$displayGeoDays = array_reverse($arrayGeoDays);

	$graph = 'http://chart.apis.google.com/chart?cht=lc&chs=800x300&chco=217297&chd=t:'. implode($displayGeoStats, ',') .'&chxt=y,x&chxr=0,0,'. $geoMaxDay .'&chds=0,'. $geoMaxDay .'&chxl=1:|'. implode($displayGeoDays, '|');
	return $graph;
}

function geoposty_conf() {

	if ( isset($_POST['submit']) ) {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));

		// check_admin_referer( $geoposty_nonce );
		$key = $_POST['geoPostyKey'];

		// reset our cache for testing!
		// this isn't permanent
		reset_geo_cache();

		if ( empty($key) ) {
			delete_option('geoposty_api_key');
		} else {
			update_option('geoposty_api_key', $key);
		}
	}

	// don't use global here
	$geoposty_api_key = get_option('geoposty_api_key');
?>
<?php if ( !empty($_POST['submit'] ) ) : ?>
	<div id="message" class="updated fade">
		<p><strong><?php _e('Information saved. This page will automatically refresh to get you the most recent information. <a href="'.$_SERVER['SCRIPT_NAME'].'?page=geoposty-key-config">You can manually reload.</a>') ?></strong></p>
	</div>
	<script type="text/javascript">window.location = '<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=geoposty-key-config';</script>
<?php 
	return;
	endif;
 ?>

<?php
	if (geoCachingPlugins()) {
?>
<div class="updated fade"><p><strong><?php _e('It looks like you have a caching plugin installed, that may cause problems with GeoPosty. <a href="http://geoposty.com/faq/">Take a look at our FAQs.</a>') ?></strong></p></div>
<?php
	}
?>

<div class="wrap">
	<h2><?php _e('GeoPosty Configuration'); ?></h2>
	<div class="tool-box">
<?php
	if (!empty($geoposty_api_key)) {
	
	$geoAdminSummary = geoAdminStats('s');
	$geoAdminSubscription = geoAdminStats('p');

	if ($geoAdminSubscription[0][2] > 1) {
		// subscribed!
?>
		<h3><?php echo number_format(($geoAdminSummary['0']['2']/$geoAdminSummary['0']['1'])*100); ?>% of lookups used for this subscription period.</h3>

		<p><strong>Subscription Information</strong><br />
		Monthly Subscription: <?php echo number_format($geoAdminSubscription[0][2]); ?> lookups/month for $<?php echo number_format($geoAdminSubscription[0][0], 2); ?><br />
		Month Start Day: <?php echo $geoAdminSubscription[0][1]; ?>
		</p>

<?php
	} else {
		// not subscribed!
?>

		<h3><?php echo number_format(($geoAdminSummary['0']['2']/$geoAdminSummary['0']['1'])*100); ?>% of lookups used in your free trial! Subscribe now!</h3>

		<p>Based on your site traffic and usage, we recommend the following subscription:</p>
		<iframe src="http://api.geoposty.com/isubs.php?domainkey=<?php echo $geoposty_api_key; ?>&domain=<?php echo $_SERVER['HTTP_HOST']; ?>" width="400" height="150" style="margin-left:100px;"></iframe>
<?php
	}
?>


			<h3>GeoPosty Usage</h3>

			<h4 style="float:left;margin-right:10px;"><a href="#" id="geoDailyLink">Daily Usage</a></h4> 
			<h4 style="float:left;margin-right:10px;"><a href="#" id="geoWeeklyLink">Weekly Usage</a></h4> 
			<h4 style="float:left;margin-right:10px;"><a href="#" id="geoMonthlyLink">Monthly Usage</a></h4> 

			<div style="width:800px;height:300px;clear:both;" id="geoGraphsWrapper">
				<img src="<?php echo geoStatsGraph('d', '21'); ?>" alt="Daily Usage" id="geoDailyGraph" />
				<img src="<?php echo geoStatsGraph('w', '10'); ?>" alt="Weekly Usage" id="geoWeeklyGraph" style="display:none;" />
				<img src="<?php echo geoStatsGraph('m', '6'); ?>" alt="Monthly Usage" id="geoMonthlyGraph" style="display:none;" />
			</div>




<?php
	}

if (empty($geoposty_api_key)) echo '<p>Hello, new GeoPosty user! Let\'s get you set up to start putting localized content on your site!</p>';
?>
		


		<form action="" method="post" id="geoposty-conf" >

			<input type="hidden" id="geoPostyTest" value="" />

			<table class="form-table">
				<tr>
					<th scope="row"><label for="geoPostyKey">First, enter your key here:</label></th>
					<td><input id="geoPostyKey" name="geoPostyKey" type="text" size="40" value="<?php echo $geoposty_api_key; ?>" /></td>
				</tr>
<?php
if (empty($geoposty_api_key)) {
?>
				<tr>
					<td colspan="2" class="aligncenter"><a href="#" onclick="geoRegister()">request a key</a><div id="geoKeyReply"></div></td>
				</tr>
<?php
}
?>
			</table>

		<p class="submit"><input type="submit" id="geosubmit" class="button-primary" name="submit" value="<?php _e('Test Your Key &raquo;'); ?>" /></p>
		</form>



	</div><!-- narrow -->
</div><!-- wrap -->
<?php
}



// this isn't permanent
function reset_geo_cache() {
	global $wpdb;

	// is there not a built in way to do this?
	$facestransients = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '%_transient_geo%'" );

	foreach ($facestransients as $trans) {
		$deleteTransient = str_replace('_transient_' , '', $trans->option_name);
		delete_transient($deleteTransient);
	}
}
?>
