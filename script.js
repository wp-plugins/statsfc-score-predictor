var $j = jQuery;

$j(function() {
	$j('.statsfc_scorepredictor .statsfc_score[data-percent]').each(function() {
		$j(this).drawBar();
	});

	$j('.statsfc_scorepredictor .statsfc_scores input:submit').click(function(e) {
		e.preventDefault();

		var $parent	= $j(this).parents('.statsfc_scorepredictor');
		var $home	= $parent.find('.statsfc_score_home');
		var $away	= $parent.find('.statsfc_score_away');

		// Check scores are numeric.
		if (! $home.isNumeric() || ! $away.isNumeric()) {
			alert('Each score should be a number between 0 and 9');
			return;
		}

		// Check that cookie doesn't exist for the current match.
		var api_key		= $parent.attr('data-api-key');
		var match_id	= $parent.attr('data-match-id');
		var cookie_id	= 'statsfc_scorepredictor_' + api_key + '_' + match_id;
		var cookie		= sfc_getCookie(cookie_id);

		if (cookie !== null) {
			alert('You can only submit one score per match');
			return;
		}

		// Submit the score to StatsFC.
		$j.getJSON(
			'https://api.statsfc.com/crowdscores/score-predictor.php?callback=?',
			{
				key:		api_key,
				match_id:	match_id,
				home_score:	$home.val(),
				away_score:	$away.val()
			},
			function(data) {
				if (data.error) {
					alert(data.error);
					return;
				}

				var score = $home.val() + '-' + $away.val();

				sfc_setCookie(cookie_id, score);

				// Update prediction and percentages.
				$parent.find('.statsfc_scores').empty().append(
					$j('<span>').text(score),
					$j('<br>'),
					$j('<small>').text('Your prediction')
				);

				$parent.find('.statsfc_popular_score').remove();

				$j.each(data.scores, function(key, val) {
					var $row = $j('<tr>').addClass('statsfc_popular_score').append(
						$j('<td>').attr({ colspan: '3', 'data-percent': val.percent }).addClass('statsfc_score').append(
							$j('<div>').append(
								$j('<strong>').text(val.home + '-' + val.away),
								$j('<em>').text(val.percent + '%')
							)
						)
					);

					$parent.find('table').append($row);
					$row.find('.statsfc_score[data-percent]').drawBar();
				});
			}
		);
	});
});

$j.fn.isNumeric = function() {
	var val = $j(this).val();

	if (val.length <= 0 || isNaN(val) || val < 0 || val > 9) {
		return false;
	}

	return true;
};

$j.fn.drawBar = function() {
	$j(this).find('div').prepend(
		$j('<span>').addClass('statsfc_percent').delay(500).animate({ width: $j(this).attr('data-percent') + '%' }, 1000)
	);
};

function sfc_setCookie(name, value) {
	var date = new Date();
	date.setTime(date.getTime() + (28 * 24 * 60 * 60 * 1000));
	var expires = '; expires=' + date.toGMTString();
	document.cookie = escape(name) + '=' + escape(value) + expires + '; path=/';
}

function sfc_getCookie(name) {
	var nameEQ	= escape(name) + "=";
	var ca		= document.cookie.split(';');

	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1, c.length);
		}

		if (c.indexOf(nameEQ) == 0) {
			return unescape(c.substring(nameEQ.length, c.length));
		}
	}

	return null;
}