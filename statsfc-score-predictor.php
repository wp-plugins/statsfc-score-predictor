<?php
/*
Plugin Name: StatsFC Score Predictor
Plugin URI: https://statsfc.com/docs/wordpress
Description: StatsFC Score Predictor
Version: 1.5
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
		$defaults = array(
			'title'			=> __('Score Predictor', STATSFC_SCOREPREDICTOR_ID),
			'api_key'		=> __('', STATSFC_SCOREPREDICTOR_ID),
			'team'			=> __('', STATSFC_SCOREPREDICTOR_ID),
			'default_css'	=> __('', STATSFC_SCOREPREDICTOR_ID)
		);

		$instance		= wp_parse_args((array) $instance, $defaults);
		$title			= strip_tags($instance['title']);
		$api_key		= strip_tags($instance['api_key']);
		$team			= strip_tags($instance['team']);
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
				<?php _e('API key', STATSFC_SCOREPREDICTOR_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo esc_attr($api_key); ?>">
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
		$instance['api_key']		= strip_tags($new_instance['api_key']);
		$instance['team']			= strip_tags($new_instance['team']);
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
		$api_key		= $instance['api_key'];
		$team			= $instance['team'];
		$default_css	= $instance['default_css'];

		echo $before_widget;
		echo $before_title . $title . $after_title;

		try {
			if (strlen($team) == 0) {
				throw new Exception('Please choose a team from the widget options');
			}

			$data = $this->_fetchData('https://api.statsfc.com/widget/score-predictor.json.php?key=' . urlencode($api_key) . '&team=' . urlencode($team));

			if (empty($data)) {
				throw new Exception('There was an error connecting to the StatsFC API');
			}

			$json = json_decode($data);

			if (isset($json->error)) {
				throw new Exception($json->error);
			}

			$fixture		= $json->fixture;
			$predictions	= $json->scores;
			$customer		= $json->customer;

			$this->_loadExternals($default_css);
			?>
			<div class="statsfc_scorepredictor" data-api-key="<?php echo esc_attr($api_key); ?>" data-match-id="<?php echo esc_attr($fixture->id); ?>">
				<table>
					<tr>
						<td class="statsfc_team">
							<label for="statsfc_score_home">
								<img src="//api.statsfc.com/kit/<?php echo esc_attr($fixture->homepath); ?>.png" title="<?php echo esc_attr($fixture->home); ?>" width="80" height="80"><br>
								<?php echo esc_attr($fixture->home); ?>
							</label>
						</th>
						<td class="statsfc_scores">
							<?php
							$cookie_id = 'statsfc_scorepredictor_' . $api_key . '_' . $fixture->id;

							if (isset($_COOKIE[$cookie_id])) {
							?>
								<?php echo $_COOKIE[$cookie_id]; ?><br>
								<small>Your prediction</small>
							<?php
							} elseif (! $fixture->started) {
							?>
								<input type="text" name="statsfc_score_home" class="statsfc_score_home" maxlength="1">
								<input type="text" name="statsfc_score_away" class="statsfc_score_away" maxlength="1"><br>
								<input type="submit" value="Predict">
							<?php
							} else {
							?>
								<span>No more predictions</span>
							<?php
							}
							?>
						</td>
						<td class="statsfc_team">
							<label for="statsfc_score_away">
								<img src="//api.statsfc.com/kit/<?php echo esc_attr($fixture->awaypath); ?>.png" title="<?php echo esc_attr($fixture->away); ?>" width="80" height="80"><br>
								<?php echo esc_attr($fixture->away); ?>
							</label>
						</td>
					</tr>
					<?php
					if (! empty($predictions)) {
					?>
						<tr><th colspan="3">Popular scores</th></tr>
						<?php
						foreach ($predictions as $prediction) {
						?>
							<tr class="statsfc_popular_score">
								<td colspan="3" class="statsfc_score" data-percent="<?php echo esc_attr($prediction->percent); ?>">
									<div>
										<strong><?php echo esc_attr($prediction->home); ?>-<?php echo esc_attr($prediction->away); ?></strong>
										<em><?php echo esc_attr($prediction->percent); ?>%</em>
									</div>
								</td>
							</tr>
						<?php
						}
					}
					?>
				</table>

				<?php
				if ($customer->advert) {
				?>
					<p class="statsfc_footer"><small>Powered by StatsFC.com</small></p>
				<?php
				}
				?>
			</div>
		<?php
		} catch (Exception $e) {
		?>
			<p>StatsFC.com – <?php echo esc_attr($e->getMessage()); ?></p>
		<?php
		}

		echo $after_widget;
	}

	private function _fetchData($url) {
		if (function_exists('curl_exec')) {
			return $this->_curlRequest($url);
		} else {
			return $this->_fopenRequest($url);
		}
	}

	private function _curlRequest($url) {
		$ch = curl_init();

		curl_setopt_array($ch, array(
			CURLOPT_AUTOREFERER		=> true,
			CURLOPT_HEADER			=> false,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_TIMEOUT			=> 5,
			CURLOPT_URL				=> $url
		));

		$data = curl_exec($ch);
		if (empty($data)) {
			$data = $this->_fopenRequest($url);
		}

		curl_close($ch);

		return $data;
	}

	private function _fopenRequest($url) {
		return file_get_contents($url);
	}

	private function _loadExternals($default_css = true) {
		if ($default_css) {
			wp_register_style(STATSFC_SCOREPREDICTOR_ID . '-css', plugins_url('all.css', __FILE__));
			wp_enqueue_style(STATSFC_SCOREPREDICTOR_ID . '-css');
		}

		wp_register_script(STATSFC_SCOREPREDICTOR_ID . '-js', plugins_url('script.js', __FILE__), array('jquery'));
		wp_enqueue_script(STATSFC_SCOREPREDICTOR_ID . '-js');
	}
}

// register StatsFC widget
add_action('widgets_init', create_function('', 'register_widget("' . STATSFC_SCOREPREDICTOR_ID . '");'));