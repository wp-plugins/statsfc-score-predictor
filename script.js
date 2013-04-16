var $j = jQuery;

$j(function() {
	$j('.statsfc_score[data-percent]').each(function() {
		$j(this).drawBar();
	});

	$j('.statsfc_scores input:submit').click(function(e) {
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
		var cookie		= $j.cookie(cookie_id);

		if (typeof cookie !== 'undefined') {
			alert('You can only submit one score per match');
			return;
		}

		// Submit the score to StatsFC.
		$j.getJSON(
			'https://api.statsfc.com/score-predictor.json?callback=?',
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

				// Update percentages.
				$parent.find('.statsfc_popular_score').remove();

				$j.each(data, function(key, val) {
					var $row = $j('<tr>').addClass('statsfc_popular_score').append(
						$j('<td>').attr({ colspan: '3', 'data-percent': val.percent }).addClass('statsfc_score').append(
							$j('<strong>').text(val.home + '-' + val.away),
							$j('<em>').text(val.percent + '%')
						)
					);

					$parent.find('table').append($row);
					$row.find('.statsfc_score[data-percent]').drawBar();
				});

				// Save cookie.
				$j.cookie(cookie_id, $home.val() + '-' + $away.val(), { expires: 28, path: '/' });
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
	$j(this).prepend(
		$j('<span>').addClass('statsfc_percent').delay(500).animate({ width: $j(this).attr('data-percent') + '%' }, 1000)
	);
};