=== StatsFC Score Predictor ===
Contributors: willjw
Donate link:
Tags: widget, football, soccer, score, predictor, premier league, fa cup, league cup, champions league, europa league, uefa
Requires at least: 3.3
Tested up to: 4.0
Stable tag: 1.14
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This widget will place a score predictor for a football team's matches on your website.

== Description ==

Add a score predictor for football matches to your WordPress website. To request a key sign up for your free trial at [statsfc.com](https://statsfc.com).

For a demo, check out [wp.statsfc.com/score-predictor/](http://wp.statsfc.com/score-predictor/).

== Installation ==

1. Upload the `statsfc-score-predictor` folder and all files to the `/wp-content/plugins/` directory
2. Activate the widget through the 'Plugins' menu in WordPress
3. Drag the widget to the relevant sidebar on the 'Widgets' page in WordPress
4. Set the StatsFC key and any other options. If you don't have a key, sign up for free at [statsfc.com](https://statsfc.com)

You can also use the `[statsfc-score-predictor]` shortcode, with the following options:

- `key` (required): Your StatsFC key
- `team` (required): Team name, e.g., `Liverpool`
- `date` (optional): For a back-dated score predictor, e.g., `2013-12-31`
- `default_css` (optional): Use the default widget styles, `true` or `false`

== Frequently asked questions ==



== Screenshots ==



== Changelog ==

**1.1**: Allow multiple score predictors on the same page.

**1.1.1**: Fixed minor CSS bug where percentage bar can be hidden.

**1.2**: If the match has started, show the live score.

**1.2.1**: Fixed a bug when selecting a specific team.

**1.3**: Updated team badges for 2013/14.

**1.4**: Added Community Shield fixtures.

**1.4.1**: Fixed bug to include shirts of teams no longer in the Premier League.

**1.4.2**: Make sure input elements are displayed inline, not as blocks.

**1.4.3**: Auto-focus on the away score when the home score is added.

**1.4.4**: Use cURL to fetch API data if possible. Fixed image CSS overrides from WordPress themes.

**1.4.6**: Fixed 'Popular scores' CSS bug in Firefox.

**1.4.7**: Submit button text could be too big.

**1.4.8**: Fixed possible cURL bug.

**1.4.9**: Added fopen fallback if cURL request fails.

**1.4.10**: Fixed bug where external parent form could be submitted.

**1.4.11**: Simplified Javascript cookies.

**1.4.12**: Tweaked error message.

**1.5**: Updated to the new API.

**1.6**: Show the live score if the match has started.

**1.6.2**: Fixed a minor Javascript bug.

**1.7**: Added an option to control whether the API call is over SSL or not.

**1.7.1**: Having fixed the root cause, the SSL option has been removed.

**1.8**: Added a `date` parameter.

**1.9**: Added `[statsfc-score-predictor]` shortcode.

**1.9.2**: Updated team badges.

**1.9.3**: Default `default_css` parameter to `true`

**1.9.4**: Added badge class for each team

**1.9.5**: Use built-in WordPress HTTP API functions

**1.10**: Enabled ad-support

**1.12**: Allow more discrete ads for ad-supported accounts

**1.13**: Converted to JS widget

**1.14**: Fixed "Invalid domain" bug caused by referal domain

== Upgrade notice ==

