<?php
/**
 * Plugin Name: NerdyWithMe Tools
 * Plugin URI: https://example.com/nerdywithme-tools
 * Description: Trading tools, calculator shortcodes, and ad slot controls for the NerdyWithMe theme.
 * Version: 0.1.0
 * Author: OpenAI
 * Text Domain: nerdywithme-tools
 *
 * @package NerdyWithMeTools
 */

if (! defined('ABSPATH')) {
	exit;
}

define('NERDYWITHME_TOOLS_VERSION', '0.1.0');
define('NERDYWITHME_TOOLS_FILE', __FILE__);
define('NERDYWITHME_TOOLS_PATH', plugin_dir_path(__FILE__));
define('NERDYWITHME_TOOLS_URL', plugin_dir_url(__FILE__));

require_once NERDYWITHME_TOOLS_PATH . 'includes/class-nerdywithme-tools-plugin.php';

function nerdywithme_tools() {
	return NerdyWithMe_Tools_Plugin::instance();
}

nerdywithme_tools();
