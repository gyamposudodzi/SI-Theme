<?php
/**
 * Plugin Name: NerdyWithMe Tools
 * Plugin URI: https://nerdywithme.com/tools
 * Description: Trading tools, calculator shortcodes, and ad slot controls for the NerdyWithMe theme.
 * Version: 0.1.1
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

define('NERDYWITHME_TOOLS_VERSION', '0.1.1');
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
