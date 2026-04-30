<?php
/**
 * Main plugin bootstrap.
 *
 * @package NerdyWithMeTools
 */

if (! defined('ABSPATH')) {
	exit;
}

$nwm_tools_admin = NERDYWITHME_TOOLS_PATH . 'includes/class-nerdywithme-tools-admin.php';
$nwm_tools_shortcodes = NERDYWITHME_TOOLS_PATH . 'includes/class-nerdywithme-tools-shortcodes.php';

if (! file_exists($nwm_tools_admin)) {
	$nwm_tools_admin = NERDYWITHME_TOOLS_PATH . 'class-nerdywithme-tools-admin.php';
}
if (! file_exists($nwm_tools_shortcodes)) {
	$nwm_tools_shortcodes = NERDYWITHME_TOOLS_PATH . 'class-nerdywithme-tools-shortcodes.php';
}

require_once $nwm_tools_admin;
require_once $nwm_tools_shortcodes;

class NerdyWithMe_Tools_Plugin {
	/**
	 * Singleton instance.
	 *
	 * @var NerdyWithMe_Tools_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Admin object.
	 *
	 * @var NerdyWithMe_Tools_Admin
	 */
	private $admin;

	/**
	 * Shortcodes object.
	 *
	 * @var NerdyWithMe_Tools_Shortcodes
	 */
	private $shortcodes;

	/**
	 * Get instance.
	 *
	 * @return NerdyWithMe_Tools_Plugin
	 */
	public static function instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->admin      = new NerdyWithMe_Tools_Admin();
		$this->shortcodes = new NerdyWithMe_Tools_Shortcodes();

		add_action('init', array($this, 'register_rewrites'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
		add_action('wp_head', array($this, 'render_active_tool_meta'), 1);
		add_filter('query_vars', array($this, 'register_query_vars'));
		add_filter('document_title_parts', array($this, 'filter_document_title_parts'));
		add_filter('body_class', array($this, 'filter_body_classes'));
	}

	/**
	 * Register tool rewrite rules.
	 *
	 * @return void
	 */
	public function register_rewrites() {
		add_rewrite_tag('%nwm_tool%', '([^&]+)');
		add_rewrite_rule('^tools/([a-z0-9-]+)/?$', 'index.php?pagename=tools&nwm_tool=$matches[1]', 'top');
	}

	/**
	 * Register public query vars.
	 *
	 * @param array $vars Query vars.
	 * @return array
	 */
	public function register_query_vars($vars) {
		$vars[] = 'nwm_tool';

		return $vars;
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		if (! is_page('tools') && ! get_query_var('nwm_tool')) {
			return;
		}

		$css_path = NERDYWITHME_TOOLS_PATH . 'assets/css/nerdywithme-tools.css';
		$css_url  = NERDYWITHME_TOOLS_URL . 'assets/css/nerdywithme-tools.css';
		if (! file_exists($css_path)) {
			$css_path = NERDYWITHME_TOOLS_PATH . 'nerdywithme-tools.css';
			$css_url  = NERDYWITHME_TOOLS_URL . 'nerdywithme-tools.css';
		}

		$js_path = NERDYWITHME_TOOLS_PATH . 'assets/js/nerdywithme-tools.js';
		$js_url  = NERDYWITHME_TOOLS_URL . 'assets/js/nerdywithme-tools.js';
		if (! file_exists($js_path)) {
			$js_path = NERDYWITHME_TOOLS_PATH . 'nerdywithme-tools.js';
			$js_url  = NERDYWITHME_TOOLS_URL . 'nerdywithme-tools.js';
		}

		wp_enqueue_style(
			'nerdywithme-tools',
			$css_url,
			array(),
			(string) filemtime($css_path)
		);

		wp_enqueue_script(
			'nerdywithme-tools',
			$js_url,
			array(),
			(string) filemtime($js_path),
			true
		);
	}

	/**
	 * Public access to active tool data.
	 *
	 * @return array|null
	 */
	public function get_active_tool_context() {
		return $this->get_active_tool_data();
	}

	/**
	 * Public access to the tool registry.
	 *
	 * @return array
	 */
	public function get_tools_registry() {
		$tools = $this->shortcodes->get_tool_registry();

		foreach ($tools as $tool_id => &$tool) {
			$tool['id']  = $tool_id;
			$tool['url'] = $this->shortcodes->get_tool_url($tool_id);
		}
		unset($tool);

		return $tools;
	}

	/**
	 * Filter browser title for active tool views.
	 *
	 * @param array $parts Title parts.
	 * @return array
	 */
	public function filter_document_title_parts($parts) {
		$tool = $this->get_active_tool_data();

		if (! $tool) {
			return $parts;
		}

		$parts['title'] = ! empty($tool['meta_title'])
			? $tool['meta_title']
			: sprintf(
				/* translators: %s: tool name. */
				__('Tools: %s', 'nerdywithme-tools'),
				$tool['label']
			);

		return $parts;
	}

	/**
	 * Add body classes for active tool pages.
	 *
	 * @param array $classes Existing classes.
	 * @return array
	 */
	public function filter_body_classes($classes) {
		$tool = $this->get_active_tool_data();

		if (! $tool) {
			return $classes;
		}

		$classes[] = 'nwm-tool-view';
		$classes[] = 'nwm-tool-view--' . sanitize_html_class($tool['id']);

		return $classes;
	}

	/**
	 * Output meta tags for active tool views.
	 *
	 * @return void
	 */
	public function render_active_tool_meta() {
		$tool = $this->get_active_tool_data();

		if (! $tool) {
			return;
		}

		$description = wp_strip_all_tags($tool['summary'] ?: $tool['description']);
		$title       = ! empty($tool['meta_title'])
			? $tool['meta_title']
			: sprintf(__('Tools: %s', 'nerdywithme-tools'), $tool['label']);
		?>
		<meta name="description" content="<?php echo esc_attr($description); ?>">
		<link rel="canonical" href="<?php echo esc_url($tool['url']); ?>">
		<meta property="og:title" content="<?php echo esc_attr($title); ?>">
		<meta property="og:description" content="<?php echo esc_attr($description); ?>">
		<meta property="og:url" content="<?php echo esc_url($tool['url']); ?>">
		<?php
	}

	/**
	 * Resolve active tool data from shortcodes layer.
	 *
	 * @return array|null
	 */
	private function get_active_tool_data() {
		if (! is_page('tools')) {
			return null;
		}

		return $this->shortcodes->get_active_tool_data();
	}
}
