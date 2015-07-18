var $j = jQuery;

function StatsFC_ScorePredictor(key) {
	this.domain  = 'https://api.statsfc.com';
	this.referer = '';
	this.key     = key;
	this.team    = '';
	this.date    = '';

	this.display = function(placeholder) {
		if (placeholder.length == 0) {
			return;
		}

		var $j = jQuery;

		var $placeholder = $j('#' + placeholder);

		if ($placeholder.length == 0) {
			return;
		}

		if (this.referer.length == 0) {
			this.referer = window.location.hostname;
		}

		var $container = $j('<div>').addClass('sfc_scorepredictor').attr('data-api-key', this.key);

		// Store globals variables here so we can use it later.
		var domain = this.domain;
		var key    = this.key;
		var object = this;

		$j.getJSON(
			domain + '/crowdscores/score-predictor.php?callback=?',
			{
				key:	this.key,
				domain:	this.referer,
				team:	this.team,
				date:	this.date
			},
			function(data) {
				if (data.error) {
					$container.append(
						$j('<p>').css('text-align', 'center').append(
							$j('<a>').attr({ href: 'https://statsfc.com', title: 'Football widgets', target: '_blank' }).text('StatsFC.com'),
							' – ',
							data.error
						)
					);

					return;
				}

				$container.attr('data-match-id', data.match.id);

				var $scores = $j('<td>').addClass('sfc_scores');

				var cookie_id	= 'sfc_scorepredictor_' + key + '_' + data.match.id;
				var cookie		= sfc_getCookie(cookie_id);

				if (cookie !== null) {
					$scores.append(
						$j('<span>').text(cookie),
						$j('<br>'),
						$j('<small>').text('Your prediction')
					);
				} else if (! data.match.started) {
					$scores.append(
						$j('<input>').addClass('sfc_score_home').attr({ type: 'text', name: 'sfc_score_home', id: 'sfc_score_home', maxlength: 1 }),
						' ',
						$j('<input>').addClass('sfc_score_away').attr({ type: 'text', name: 'sfc_score_away', id: 'sfc_score_away', maxlength: 1 }),
						$j('<br>'),
						$j('<input>').attr('type', 'submit').val('Predict').on('click', function(e) {
							e.preventDefault();
							object.predict($j(this));
						})
					);
				} else {
					$scores.append(
						$j('<span>').html('<small>Live: ' + data.match.status + '</small><br>' + data.match.score[0] + ' - ' + data.match.score[1])
					);
				}

				var $table = $j('<table>');

				var $tbody = $j('<tbody>').append(
					$j('<tr>').append(
						$j('<td>').addClass('sfc_team sfc_home sfc_badge_' + data.match.homepath).append(
							$j('<label>').attr('for', 'sfc_score_home').css('background-image', 'url(https://api.statsfc.com/kit/' + data.match.homepath + '.svg)').text(data.match.home)
						),
						$scores,
						$j('<td>').addClass('sfc_team sfc_away sfc_badge_' + data.match.awaypath).append(
							$j('<label>').attr('for', 'sfc_score_away').css('background-image', 'url(https://api.statsfc.com/kit/' + data.match.awaypath + '.svg)').text(data.match.away)
						)
					)
				);

				if (data.scores.length > 0) {
					$tbody.append(
						$j('<tr>').append(
							$j('<th>').attr('colspan', 3).text('Popular scores')
						)
					);

					$j.each(data.scores, function(key, score) {
						$tbody.append(
							$j('<tr>').addClass('sfc_popular_score').append(
								$j('<td>').addClass('sfc_score').attr('colspan', 3).append(
									$j('<div>').append(
										$j('<span>').addClass('sfc_percent').css('width', score.percent + '%'),
										$j('<strong>').text(score.home + '-' + score.away),
										$j('<em>').text(score.percent + '%')
									)
								)
							)
						);
					});
				}

				$table.append($tbody);

				$container.append($table);

				if (data.customer.attribution) {
					$container.append(
						$j('<div>').attr('class', 'sfc_footer').append(
							$j('<p>').append(
								$j('<small>').append('Powered by ').append(
									$j('<a>').attr({ href: 'https://statsfc.com', title: 'StatsFC – Football widgets', target: '_blank' }).text('StatsFC.com')
								).append('. Fan data via ').append(
									$j('<a>').attr({ href: 'https://crowdscores.com', title: 'CrowdScores', target: '_blank' }).text('CrowdScores.com')
								)
							)
						)
					);
				}
			}
		);

		$j('#' + placeholder).append($container);
	};

	this.predict = function(e) {
		var $parent	= e.parents('.sfc_scorepredictor');
		var $home	= $parent.find('.sfc_score_home');
		var $away	= $parent.find('.sfc_score_away');

		// Check scores are numeric.
		var home	= $home.val();
		var away	= $away.val();

		if (home.length <= 0 || isNaN(home) || home < 0 || home > 9 || away.length <= 0 || isNaN(away) || away < 0 || away > 9) {
			alert('Each score should be a number between 0 and 9');
			return;
		}

		// Check that cookie doesn't exist for the current match.
		var cookie_id	= 'sfc_scorepredictor_' + this.key + '_' + $parent.attr('data-match-id');
		var cookie		= sfc_getCookie(cookie_id);

		if (cookie !== null) {
			alert('You can only submit one score per match');
			return;
		}

		// Submit the score to StatsFC.
		$j.getJSON(
			this.domain + '/crowdscores/score-predictor.php?callback=?',
			{
				key:		this.key,
				domain:		window.location.hostname,
				match_id:	$parent.attr('data-match-id'),
				home_score:	$home.val(),
				away_score:	$away.val()
			},
			function(data) {
				if (data.error) {
					alert(data.error);
					return;
				}

				// Save cookie.
				var score = $home.val() + '-' + $away.val();
				sfc_setCookie(cookie_id, score);

				// Swap textboxes for prediction.
				$parent.find('.sfc_scores').empty().append(
					$j('<span>').text(score),
					$j('<br>'),
					$j('<small>').text('Your prediction')
				);

				// Update percentages.
				$parent.find('.sfc_popular_score').remove();

				$j.each(data.scores, function(key, val) {
					$parent.find('table').append(
						$j('<tr>').addClass('sfc_popular_score').append(
							$j('<td>').attr('colspan', 3).addClass('sfc_score').append(
								$j('<div>').append(
									$j('<span>').addClass('sfc_percent').css('width', val.percent + '%'),
									$j('<strong>').text(val.home + '-' + val.away),
									$j('<em>').text(val.percent + '%')
								)
							)
						)
					);
				});
			}
		);
	};
}

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
