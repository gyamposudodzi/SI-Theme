<?php
/**
 * Plugin Name: NerdyWithMe Tools
 * Plugin URI: https://nerdywithme.com/tools
 * Description: Trading tools, calculator shortcodes, and ad slot controls for the NerdyWithMe theme.
 * Version: 0.1.3
 * Author: NerdyWithMe
 * Author URI: https://nerdywithme.com
 * Requires at least: 6.0
 * Tested up to: 6.7
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * Text Domain: nerdywithme-tools
 *
 * @package NerdyWithMeTools
 */

if (! defined('ABSPATH')) {
	exit;
}

define('NERDYWITHME_TOOLS_VERSION', '0.1.3');
define('NERDYWITHME_TOOLS_FILE', __FILE__);
define('NERDYWITHME_TOOLS_PATH', plugin_dir_path(__FILE__));
define('NERDYWITHME_TOOLS_URL', plugin_dir_url(__FILE__));

$nwm_tools_bootstrap = NERDYWITHME_TOOLS_PATH . 'includes/class-nerdywithme-tools-plugin.php';
if (! file_exists($nwm_tools_bootstrap)) {
	$nwm_tools_bootstrap = NERDYWITHME_TOOLS_PATH . 'class-nerdywithme-tools-plugin.php';
}

require_once $nwm_tools_bootstrap;

function nerdywithme_tools() {
	return NerdyWithMe_Tools_Plugin::instance();
}

nerdywithme_tools();

function nerdywithme_tools_get_settings() {
	return get_option(NerdyWithMe_Tools_Admin::OPTION_KEY, array());
}

function nerdywithme_tools_get_social_profile_url($platform, $default = '') {
	$settings = nerdywithme_tools_get_settings();
	$profiles = is_array($settings['social_profiles'] ?? null) ? $settings['social_profiles'] : array();
	$value    = isset($profiles[ $platform ]) ? esc_url($profiles[ $platform ]) : '';

	return $value ?: $default;
}

function nerdywithme_tools_get_profile_card_setting($key, $default = '') {
	$settings = nerdywithme_tools_get_settings();
	$profile  = is_array($settings['profile_card'] ?? null) ? $settings['profile_card'] : array();
	$value    = $profile[ $key ] ?? '';

	if ('button_url' === $key) {
		$value = esc_url($value);
	} else {
		$value = sanitize_text_field($value);
	}

	return $value ?: $default;
}

function nerdywithme_tools_get_cookie_content($key, $default = '') {
	$settings = nerdywithme_tools_get_settings();
	$content  = is_array($settings['cookie_content'] ?? null) ? $settings['cookie_content'] : array();
	$value    = isset($content[ $key ]) ? sanitize_textarea_field($content[ $key ]) : '';

	return '' !== $value ? $value : $default;
}

function nerdywithme_tools_maybe_migrate_social_profiles() {
	$settings = nerdywithme_tools_get_settings();
	$profiles = is_array($settings['social_profiles'] ?? null) ? $settings['social_profiles'] : array();
	$profile_card = is_array($settings['profile_card'] ?? null) ? $settings['profile_card'] : array();

	$theme_map = array(
		'facebook'  => get_theme_mod('nerdywithme_facebook_url', ''),
		'x'         => get_theme_mod('nerdywithme_x_url', ''),
		'instagram' => get_theme_mod('nerdywithme_instagram_url', ''),
	);

	$changed = false;

	foreach ($theme_map as $platform => $url) {
		$url = esc_url_raw($url);
		if ($url && empty($profiles[ $platform ])) {
			$profiles[ $platform ] = $url;
			$changed = true;
		}
	}

	$theme_button_label = sanitize_text_field(get_theme_mod('nerdywithme_profile_button_label', ''));
	$theme_button_url   = esc_url_raw(get_theme_mod('nerdywithme_profile_button_url', ''));

	if ($theme_button_label && empty($profile_card['button_label'])) {
		$profile_card['button_label'] = $theme_button_label;
		$changed = true;
	}

	if ($theme_button_url && empty($profile_card['button_url'])) {
		$profile_card['button_url'] = $theme_button_url;
		$changed = true;
	}

	if ($changed) {
		$settings['social_profiles'] = $profiles;
		$settings['profile_card']    = $profile_card;
		update_option(NerdyWithMe_Tools_Admin::OPTION_KEY, $settings);
	}
}
add_action('init', 'nerdywithme_tools_maybe_migrate_social_profiles', 20);
