<?php
/*
Plugin Name: StatsFC Score Predictor
Plugin URI: https://statsfc.com/docs/wordpress
Description: StatsFC Score Predictor
Version: 1.9.5
Author: Will Woodward
Author URI: http://willjw.co.uk
License: GPL2
*/

/*  Copyright 2013  Will Woodward  (email : will@willjw.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('STATSFC_SCOREPREDICTOR_ID',		'StatsFC_ScorePredictor');
define('STATSFC_SCOREPREDICTOR_NAME',	'StatsFC Score Predictor');

/**
 * Adds StatsFC widget.
 */
class StatsFC_ScorePredictor extends WP_Widget {
	public $isShortcode = false;

	private static $defaults = array(
		'title'			=> '',
		'key'			=> '',
		'team'			=> '',
		'date'			=> '',
		'default_css'	=> true
	);

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(STATSFC_SCOREPREDICTOR_ID, STATSFC_SCOREPREDICTOR_NAME, array('description' => 'StatsFC Score Predictor'));
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance) {
		$instance		= wp_parse_args((array) $instance, self::$defaults);
		$title			= strip_tags($instance['title']);
		$key			= strip_tags($instance['key']);
		$team			= strip_tags($instance['team']);
		$date			= strip_tags($instance['date']);
		$default_css	= strip_tags($instance['default_css']);
		?>
		<p>
			<label>
				<?php _e('Title', STATSFC_SCOREPREDICTOR_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Key', STATSFC_SCOREPREDICTOR_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('key'); ?>" type="text" value="<?php echo esc_attr($key); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Team', STATSFC_SCOREPREDICTOR_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('team'); ?>" type="text" value="<?php echo esc_attr($team); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Date (YYYY-MM-DD)', STATSFC_SCOREPREDICTOR_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('date'); ?>" type="text" value="<?php echo esc_attr($date); ?>" placeholder="YYYY-MM-DD">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Use default CSS?', STATSFC_SCOREPREDICTOR_ID); ?>
				<input type="checkbox" name="<?php echo $this->get_field_name('default_css'); ?>"<?php echo ($default_css == 'on' ? ' checked' : ''); ?>>
			</label>
		</p>
	<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update($new_instance, $old_instance) {
		$instance					= $old_instance;
		$instance['title']			= strip_tags($new_instance['title']);
		$instance['key']			= strip_tags($new_instance['key']);
		$instance['team']			= strip_tags($new_instance['team']);
		$instance['date']			= strip_tags($new_instance['date']);
		$instance['default_css']	= strip_tags($new_instance['default_css']);

		return $instance;
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget($args, $instance) {
		extract($args);

		$title			= apply_filters('widget_title', $instance['title']);
		$key			= $instance['key'];
		$team			= $instance['team'];
		$date			= $instance['date'];
		$default_css	= filter_var($instance['default_css'], FILTER_VALIDATE_BOOLEAN);

		$html  = $before_widget;
		$html .= $before_title . $title . $after_title;

		try {
			if (strlen($team) == 0) {
				throw new Exception('Please choose a team from the widget options');
			}

			$data = $this->_fetchData('https://api.statsfc.com/crowdscores/score-predictor.php?key=' . urlencode($key) . '&team=' . urlencode($team) . '&date=' . urlencode($date));

			if (empty($data)) {
				throw new Exception('There was an error connecting to the StatsFC API');
			}

			$json = json_decode($data);

			if (isset($json->error)) {
				throw new Exception($json->error);
			}

			$match			= $json->match;
			$predictions	= $json->scores;
			$customer		= $json->customer;

			if ($default_css) {
				wp_register_style(STATSFC_SCOREPREDICTOR_ID . '-css', plugins_url('all.css', __FILE__));
				wp_enqueue_style(STATSFC_SCOREPREDICTOR_ID . '-css');
			}

			wp_register_script(STATSFC_SCOREPREDICTOR_ID . '-js', plugins_url('script.js', __FILE__), array('jquery'));
			wp_enqueue_script(STATSFC_SCOREPREDICTOR_ID . '-js');

			$key		= esc_attr($key);
			$match_id	= esc_attr($match->id);
			$homeBadge	= esc_attr($match->homepath);
			$home		= esc_attr($match->home);
			$cookie_id	= 'statsfc_scorepredictor_' . $key . '_' . $match->id;
			$awayBadge	= esc_attr($match->awaypath);
			$away		= esc_attr($match->away);

			$html .= <<< HTML
			<div class="statsfc_scorepredictor" data-api-key="{$key}" data-match-id="{$match_id}">
				<table>
					<tr>
						<td class="statsfc_team statsfc_badge_{$homeBadge}">
							<label for="statsfc_score_home">
								<img src="//api.statsfc.com/kit/{$homeBadge}.svg" title="{$home}" width="80" height="80"><br>
								{$home}
							</label>
						</th>
						<td class="statsfc_scores">
HTML;

			

			if (isset($_COOKIE[$cookie_id])) {
				$html .= $_COOKIE[$cookie_id] . '<br><small>Your prediction</small>' . PHP_EOL;
			} elseif (! $match->started) {
				$html .= <<< HTML
				<input type="text" name="statsfc_score_home" class="statsfc_score_home" maxlength="1">
				<input type="text" name="statsfc_score_away" class="statsfc_score_away" maxlength="1"><br>
				<input type="submit" value="Predict">
HTML;
			} else {
				$status	= esc_attr($match->status);
				$score	= esc_attr(implode(' - ', $match->score));

				$html .= <<< HTML
				<span>
					<small>Live: {$status}</small><br>
					{$score}
				</span>
HTML;
			}

			$html .= <<< HTML
				</td>
				<td class="statsfc_team statsfc_badge_{$awayBadge}">
					<label for="statsfc_score_away">
						<img src="//api.statsfc.com/kit/{$awayBadge}.svg" title="{$away}" width="80" height="80"><br>
						{$away}
					</label>
				</td>
			</tr>
HTML;

			if (! empty($predictions)) {
				$html .= '<tr><th colspan="3">Popular scores</th></tr>' . PHP_EOL;

				foreach ($predictions as $prediction) {
					$home		= esc_attr($prediction->home);
					$away		= esc_attr($prediction->away);
					$percent	= esc_attr($prediction->percent);

					$html .= <<< HTML
					<tr class="statsfc_popular_score">
						<td colspan="3" class="statsfc_score" data-percent="{$percent}">
							<div>
								<strong>{$home}-{$away}</strong>
								<em>{$percent}%</em>
							</div>
						</td>
					</tr>
HTML;
				}
			}

			$html .= <<< HTML
				</table>
HTML;

			if ($customer->advert) {
				$html .= <<< HTML
				<p class="statsfc_footer"><small>Powered by StatsFC.com. Fan data via CrowdScores.com</small></p>
HTML;
			}

			$html .= <<< HTML
			</div>
HTML;
		} catch (Exception $e) {
			$html .= '<p style="text-align: center;">StatsFC.com – ' . esc_attr($e->getMessage()) . '</p>' . PHP_EOL;
		}

		$html .= $after_widget;

		if ($this->isShortcode) {
			return $html;
		} else {
			echo $html;
		}
	}

	private function _fetchData($url) {
		$response = wp_remote_get($url);

		return wp_remote_retrieve_body($response);
	}

	public static function shortcode($atts) {
		$args = shortcode_atts(self::$defaults, $atts);

		$widget					= new self;
		$widget->isShortcode	= true;

		return $widget->widget(array(), $args);
	}
}

// register StatsFC widget
add_action('widgets_init', create_function('', 'register_widget("' . STATSFC_SCOREPREDICTOR_ID . '");'));
add_shortcode('statsfc-score-predictor', STATSFC_SCOREPREDICTOR_ID . '::shortcode');
