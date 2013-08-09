=== StatsFC Score Predictor ===
Contributors: willjw
Donate link:
Tags: widget, football, soccer, score, predictor, premier league, fa cup, league cup
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 1.4.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This widget will place a score predictor for Premier League, FA Cup or League Cup matches on your website.

== Description ==

Add a score predictor for Premier League, FA Cup or League Cup matches to your WordPress website. To request an API key sign up for free at [statsfc.com](https://statsfc.com).

For a demo, check out [wp.statsfc.com](http://wp.statsfc.com).

== Installation ==

1. Upload the `statsfc-score-predictor` folder and all files to the `/wp-content/plugins/` directory
2. Activate the widget through the 'Plugins' menu in WordPress
3. Drag the widget to the relevant sidebar on the 'Widgets' page in WordPress
4. Set the API key and any other options. If you don't have any API key, sign up for free at statsfc.com

If you want to place the widget into a page rather than a sidebar:

1. Install and activate 'Widgets on Pages' from the 'Plugins' menu in WordPress
2. Add a sidebar named "StatsFC Score Predictor" from the 'Settings > Widgets on Pages' menu in WordPress
3. Place the widget anywhere in a page, using the following code:

		[widgets_on_pages id="StatsFC Score Predictor"]

== Frequently asked questions ==



== Screenshots ==



== Changelog ==

**1.1**:

- Allow multiple score predictors on the same page.

**1.1.1**:

- Fixed minor CSS bug where percentage bar can be hidden.

**1.2**:

- If the match has started, show the live score.

**1.2.1**:

- Fixed a bug when selecting a specific team.

**1.3**:

- Updated team badges for 2013/14.

**1.4**:

- Added Community Shield fixtures.

**1.4.1**:

- Fixed bug to include shirts of teams no longer in the Premier League.

**1.4.2**:

- Make sure input elements are displayed inline, not as blocks.

**1.4.3**:

- Auto-focus on the away score when the home score is added.

**1.4.4**:

- Use cURL to fetch API data if possible.
- Fixed image CSS overrides from WordPress themes.

**1.4.6**:

- Fixed 'Popular scores' CSS bug in Firefox.

**1.4.7**:

- Submit button text could be too big.

**1.4.8**:

- Fixed possible cURL bug.

**1.4.9**:

- Added fopen fallback if cURL request fails.

== Upgrade notice ==

