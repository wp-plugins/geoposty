jQuery(document).ready(function($) {
	// $() will work as an alias for jQuery() inside of this function

	$('textarea.geoTextCounter').keypress(function() { 
		$('span.geoTextCounter').html($(this).val().length);
	});

	$('#geoposty-conf').live('submit', function() {
		var geoKey = $('#geoPostyKey').val();
		var geoTest = $('#geoPostyTest').val();

		if (geoTest == 'go') {
			return true;
		}

		$('#geoKeyReply').addClass('updated').html('Testing key <em>'+geoKey+'</em>, please wait...');

		var data = {
			action: 'geo_confirm',
			domainkey: geoKey
		};		

		$.get(ajaxurl, data, function(response) {
			if (response.length > 2) {
				$('#geoKeyReply').addClass('updated').html(response);
			} else {
				$('#geoKeyReply').html('Your key looks good! Please save it.');

				var data = {
					action: 'geo_followup'
				};

				$.get(ajaxurl, data);
				$('#geoPostyTest').val('go');
				$('#geosubmit').val('Save Key');
			}
		});

		return false;
	});

	function geoRegister() {
		var data = {
			action: 'geo_register'
		};

		jQuery.get(ajaxurl, data, function(response) {
			jQuery('#geoKeyReply').addClass('updated').html(response);
		});
	}



	// this sucks.
	$('#geoDailyLink').click(function() {
		$('#geoDailyGraph').fadeIn();
		$('#geoWeeklyGraph').hide();
		$('#geoMonthlyGraph').hide();
		return false;
	});
	$('#geoWeeklyLink').click(function() {
		$('#geoDailyGraph').hide();
		$('#geoWeeklyGraph').fadeIn();
		$('#geoMonthlyGraph').hide();
		return false;
	});
	$('#geoMonthlyLink').click(function() {
		$('#geoDailyGraph').hide();
		$('#geoWeeklyGraph').hide();
		$('#geoMonthlyGraph').fadeIn();
		return false;
	});
});
