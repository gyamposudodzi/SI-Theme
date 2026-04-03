<?php
/**
 * Admin settings.
 *
 * @package NerdyWithMeTools
 */

if (! defined('ABSPATH')) {
	exit;
}

class NerdyWithMe_Tools_Admin {
	/**
	 * Option key.
	 *
	 * @var string
	 */
	const OPTION_KEY = 'nerdywithme_tools_settings';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action('admin_menu', array($this, 'register_menu'));
		add_action('admin_init', array($this, 'register_settings'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
	}

	/**
	 * Enqueue admin assets for the tools screen.
	 *
	 * @param string $hook Current admin hook.
	 * @return void
	 */
	public function enqueue_assets($hook) {
		if ('toplevel_page_nerdywithme-tools' !== $hook) {
			return;
		}

		wp_enqueue_style(
			'nerdywithme-tools-admin',
			NERDYWITHME_TOOLS_URL . 'assets/css/nerdywithme-tools-admin.css',
			array(),
			(string) filemtime(NERDYWITHME_TOOLS_PATH . 'assets/css/nerdywithme-tools-admin.css')
		);

		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script(
			'nerdywithme-tools-admin',
			NERDYWITHME_TOOLS_URL . 'assets/js/nerdywithme-tools-admin.js',
			array('jquery', 'jquery-ui-sortable'),
			(string) filemtime(NERDYWITHME_TOOLS_PATH . 'assets/js/nerdywithme-tools-admin.js'),
			true
		);
	}

	/**
	 * Register settings page.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_menu_page(
			__('NerdyWithMe Tools', 'nerdywithme-tools'),
			__('NWM Tools', 'nerdywithme-tools'),
			'manage_options',
			'nerdywithme-tools',
			array($this, 'render_page'),
			'dashicons-chart-line',
			59
		);
	}

	/**
	 * Register settings fields.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'nerdywithme_tools',
			self::OPTION_KEY,
			array($this, 'sanitize_settings')
		);

		add_settings_section(
			'nerdywithme_tools_ads',
			__('Ad Slots', 'nerdywithme-tools'),
			function() {
				echo '<p>' . esc_html__('Add HTML banners, affiliate embeds, or promo blocks for the theme ad spaces.', 'nerdywithme-tools') . '</p>';
			},
			'nerdywithme-tools'
		);

		foreach ($this->get_ad_slot_labels() as $slot => $label) {
			add_settings_field(
				'ad_slot_' . $slot,
				$label,
				array($this, 'render_ad_slot_field'),
				'nerdywithme-tools',
				'nerdywithme_tools_ads',
				array(
					'slot' => $slot,
				)
			);
		}

		add_settings_section(
			'nerdywithme_tools_calculators',
			__('Calculator Controls', 'nerdywithme-tools'),
			function() {
				echo '<p>' . esc_html__('Toggle the tools you want active while the plugin grows.', 'nerdywithme-tools') . '</p>';
			},
			'nerdywithme-tools'
		);

		add_settings_field(
			'enable_risk_calculator',
			__('Enable Risk Calculator', 'nerdywithme-tools'),
			array($this, 'render_checkbox_field'),
			'nerdywithme-tools',
			'nerdywithme_tools_calculators',
			array(
				'key' => 'enable_risk_calculator',
			)
		);

		add_settings_field(
			'enable_position_size_calculator',
			__('Enable Position Size', 'nerdywithme-tools'),
			array($this, 'render_checkbox_field'),
			'nerdywithme-tools',
			'nerdywithme_tools_calculators',
			array(
				'key' => 'enable_position_size_calculator',
			)
		);

		add_settings_field(
			'enable_pip_value_calculator',
			__('Enable Pip / Point Value', 'nerdywithme-tools'),
			array($this, 'render_checkbox_field'),
			'nerdywithme-tools',
			'nerdywithme_tools_calculators',
			array(
				'key' => 'enable_pip_value_calculator',
			)
		);

		add_settings_field(
			'enable_profit_target_calculator',
			__('Enable Profit Target', 'nerdywithme-tools'),
			array($this, 'render_checkbox_field'),
			'nerdywithme-tools',
			'nerdywithme_tools_calculators',
			array(
				'key' => 'enable_profit_target_calculator',
			)
		);

		add_settings_field(
			'tool_order',
			__('Tool Order', 'nerdywithme-tools'),
			array($this, 'render_text_field'),
			'nerdywithme-tools',
			'nerdywithme_tools_calculators',
			array(
				'key'         => 'tool_order',
				'placeholder' => 'risk-calculator,position-size,pip-value,profit-target',
				'help'        => __('Use comma-separated tool slugs to control the top bar order.', 'nerdywithme-tools'),
			)
		);

		add_settings_field(
			'tool_descriptions',
			__('Tool Descriptions', 'nerdywithme-tools'),
			array($this, 'render_description_fields'),
			'nerdywithme-tools',
			'nerdywithme_tools_calculators'
		);

		add_settings_field(
			'tool_summaries',
			__('Tool Page Summaries', 'nerdywithme-tools'),
			array($this, 'render_summary_fields'),
			'nerdywithme-tools',
			'nerdywithme_tools_calculators'
		);

		add_settings_field(
			'tool_meta_titles',
			__('Tool Meta Titles', 'nerdywithme-tools'),
			array($this, 'render_meta_title_fields'),
			'nerdywithme-tools',
			'nerdywithme_tools_calculators'
		);
	}

	/**
	 * Sanitize option payload.
	 *
	 * @param array $input Raw input.
	 * @return array
	 */
	public function sanitize_settings($input) {
		$input    = is_array($input) ? $input : array();
		$sanitized = array(
			'ads'                           => array(),
			'enable_risk_calculator'        => ! empty($input['enable_risk_calculator']),
			'enable_position_size_calculator' => ! empty($input['enable_position_size_calculator']),
			'enable_pip_value_calculator'   => ! empty($input['enable_pip_value_calculator']),
			'enable_profit_target_calculator' => ! empty($input['enable_profit_target_calculator']),
			'tool_order'                    => isset($input['tool_order']) ? sanitize_text_field($input['tool_order']) : 'risk-calculator,position-size,pip-value,profit-target',
			'tool_descriptions'             => array(),
			'tool_summaries'                => array(),
			'tool_meta_titles'              => array(),
		);

		foreach (array_keys($this->get_ad_slot_labels()) as $slot) {
			$sanitized['ads'][ $slot ] = isset($input['ads'][ $slot ]) ? wp_kses_post($input['ads'][ $slot ]) : '';
		}

		foreach ($this->get_tool_description_defaults() as $slug => $description) {
			$sanitized['tool_descriptions'][ $slug ] = isset($input['tool_descriptions'][ $slug ]) ? sanitize_text_field($input['tool_descriptions'][ $slug ]) : $description;
		}

		foreach ($this->get_tool_summary_defaults() as $slug => $summary) {
			$sanitized['tool_summaries'][ $slug ] = isset($input['tool_summaries'][ $slug ]) ? sanitize_textarea_field($input['tool_summaries'][ $slug ]) : $summary;
		}

		foreach ($this->get_tool_meta_title_defaults() as $slug => $meta_title) {
			$sanitized['tool_meta_titles'][ $slug ] = isset($input['tool_meta_titles'][ $slug ]) ? sanitize_text_field($input['tool_meta_titles'][ $slug ]) : $meta_title;
		}

		return $sanitized;
	}

	/**
	 * Render textarea for ad slots.
	 *
	 * @param array $args Field args.
	 * @return void
	 */
	public function render_ad_slot_field($args) {
		$slot     = $args['slot'];
		$settings = $this->get_settings();
		$value    = $settings['ads'][ $slot ] ?? '';
		?>
		<textarea
			name="<?php echo esc_attr(self::OPTION_KEY . '[ads][' . $slot . ']'); ?>"
			rows="5"
			class="large-text code"
			placeholder="<?php esc_attr_e('Paste banner HTML, shortcode output, or affiliate markup here.', 'nerdywithme-tools'); ?>"
		><?php echo esc_textarea($value); ?></textarea>
		<?php
	}

	/**
	 * Render checkbox field.
	 *
	 * @param array $args Field args.
	 * @return void
	 */
	public function render_checkbox_field($args) {
		$key      = $args['key'];
		$settings = $this->get_settings();
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY . '[' . $key . ']'); ?>" value="1" <?php checked(! empty($settings[ $key ])); ?>>
			<?php esc_html_e('Enabled', 'nerdywithme-tools'); ?>
		</label>
		<?php
	}

	/**
	 * Render text field.
	 *
	 * @param array $args Field args.
	 * @return void
	 */
	public function render_text_field($args) {
		$key         = $args['key'];
		$placeholder = $args['placeholder'] ?? '';
		$help        = $args['help'] ?? '';
		$settings    = $this->get_settings();
		$value       = $settings[ $key ] ?? '';
		?>
		<input
			type="text"
			name="<?php echo esc_attr(self::OPTION_KEY . '[' . $key . ']'); ?>"
			value="<?php echo esc_attr($value); ?>"
			placeholder="<?php echo esc_attr($placeholder); ?>"
			class="regular-text"
		>
		<?php if ($help) : ?>
			<p class="description"><?php echo esc_html($help); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render tool description fields.
	 *
	 * @return void
	 */
	public function render_description_fields() {
		$settings = $this->get_settings();
		$descriptions = $settings['tool_descriptions'] ?? $this->get_tool_description_defaults();

		foreach ($this->get_tool_description_defaults() as $slug => $default) {
			?>
			<p>
				<label for="<?php echo esc_attr('nwm-desc-' . $slug); ?>"><strong><?php echo esc_html(ucwords(str_replace('-', ' ', $slug))); ?></strong></label><br>
				<input
					id="<?php echo esc_attr('nwm-desc-' . $slug); ?>"
					type="text"
					name="<?php echo esc_attr(self::OPTION_KEY . '[tool_descriptions][' . $slug . ']'); ?>"
					value="<?php echo esc_attr($descriptions[ $slug ] ?? $default); ?>"
					class="large-text"
				>
			</p>
			<?php
		}
	}

	/**
	 * Render tool summary fields.
	 *
	 * @return void
	 */
	public function render_summary_fields() {
		$settings  = $this->get_settings();
		$summaries = $settings['tool_summaries'] ?? $this->get_tool_summary_defaults();

		foreach ($this->get_tool_summary_defaults() as $slug => $default) {
			?>
			<p>
				<label for="<?php echo esc_attr('nwm-summary-' . $slug); ?>"><strong><?php echo esc_html(ucwords(str_replace('-', ' ', $slug))); ?></strong></label><br>
				<textarea
					id="<?php echo esc_attr('nwm-summary-' . $slug); ?>"
					name="<?php echo esc_attr(self::OPTION_KEY . '[tool_summaries][' . $slug . ']'); ?>"
					rows="3"
					class="large-text"
				><?php echo esc_textarea($summaries[ $slug ] ?? $default); ?></textarea>
			</p>
			<?php
		}
	}

	/**
	 * Render tool meta title fields.
	 *
	 * @return void
	 */
	public function render_meta_title_fields() {
		$settings    = $this->get_settings();
		$meta_titles = $settings['tool_meta_titles'] ?? $this->get_tool_meta_title_defaults();

		foreach ($this->get_tool_meta_title_defaults() as $slug => $default) {
			?>
			<p>
				<label for="<?php echo esc_attr('nwm-meta-title-' . $slug); ?>"><strong><?php echo esc_html(ucwords(str_replace('-', ' ', $slug))); ?></strong></label><br>
				<input
					id="<?php echo esc_attr('nwm-meta-title-' . $slug); ?>"
					type="text"
					name="<?php echo esc_attr(self::OPTION_KEY . '[tool_meta_titles][' . $slug . ']'); ?>"
					value="<?php echo esc_attr($meta_titles[ $slug ] ?? $default); ?>"
					class="large-text"
				>
			</p>
			<?php
		}
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_page() {
		$settings     = $this->get_settings();
		$tools        = $this->get_tool_labels();
		$descriptions = $settings['tool_descriptions'] ?? $this->get_tool_description_defaults();
		$summaries    = $settings['tool_summaries'] ?? $this->get_tool_summary_defaults();
		$meta_titles  = $settings['tool_meta_titles'] ?? $this->get_tool_meta_title_defaults();
		$order        = array_filter(array_map('sanitize_key', array_map('trim', explode(',', (string) ($settings['tool_order'] ?? '')))));

		if (empty($order)) {
			$order = array_keys($tools);
		}

		foreach (array_keys($tools) as $tool_slug) {
			if (! in_array($tool_slug, $order, true)) {
				$order[] = $tool_slug;
			}
		}
		?>
		<div class="wrap nwm-tools-admin">
			<div class="nwm-tools-admin__hero">
				<div>
					<h1><?php esc_html_e('NerdyWithMe Tools', 'nerdywithme-tools'); ?></h1>
					<p><?php esc_html_e('Manage your calculator hub, standalone tool pages, and theme-connected ad placements from one cleaner control center.', 'nerdywithme-tools'); ?></p>
				</div>
				<div class="nwm-tools-admin__hero-meta">
					<strong><?php esc_html_e('Current tools', 'nerdywithme-tools'); ?></strong>
					<span><?php echo esc_html(count($tools)); ?></span>
				</div>
			</div>
			<form action="options.php" method="post">
				<?php
				settings_fields('nerdywithme_tools');
				?>
				<div class="nwm-tools-admin__tabs" role="tablist" aria-label="<?php esc_attr_e('Plugin settings sections', 'nerdywithme-tools'); ?>">
					<button type="button" class="nwm-tools-admin__tab is-active" data-nwm-admin-tab="tools"><?php esc_html_e('Tools', 'nerdywithme-tools'); ?></button>
					<button type="button" class="nwm-tools-admin__tab" data-nwm-admin-tab="seo"><?php esc_html_e('SEO', 'nerdywithme-tools'); ?></button>
					<button type="button" class="nwm-tools-admin__tab" data-nwm-admin-tab="ads"><?php esc_html_e('Ads', 'nerdywithme-tools'); ?></button>
					<button type="button" class="nwm-tools-admin__tab" data-nwm-admin-tab="help"><?php esc_html_e('Help', 'nerdywithme-tools'); ?></button>
				</div>

				<section class="nwm-tools-admin__panel is-active" data-nwm-admin-panel="tools">
					<div class="nwm-tools-admin__panel-header">
						<h2><?php esc_html_e('Tool Hub Controls', 'nerdywithme-tools'); ?></h2>
						<p><?php esc_html_e('Drag tools into the order you want, toggle them on or off, and edit the copy users see inside the hub.', 'nerdywithme-tools'); ?></p>
					</div>
					<input type="hidden" name="<?php echo esc_attr(self::OPTION_KEY . '[tool_order]'); ?>" value="<?php echo esc_attr(implode(',', $order)); ?>" data-nwm-tool-order-input>
					<div class="nwm-tools-admin__cards" data-nwm-sortable-tools>
						<?php foreach ($order as $tool_slug) : ?>
							<?php if (! isset($tools[ $tool_slug ])) { continue; } ?>
							<article class="nwm-tools-admin__card" data-tool-id="<?php echo esc_attr($tool_slug); ?>">
								<div class="nwm-tools-admin__card-head">
									<div class="nwm-tools-admin__drag" aria-hidden="true">⋮⋮</div>
									<div>
										<h3><?php echo esc_html($tools[ $tool_slug ]); ?></h3>
										<p><code><?php echo esc_html($tool_slug); ?></code></p>
									</div>
									<label class="nwm-tools-admin__toggle">
										<input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY . '[enable_' . str_replace('-', '_', $tool_slug) . ']'); ?>" value="1" <?php checked(! empty($settings[ 'enable_' . str_replace('-', '_', $tool_slug) ])); ?>>
										<span><?php esc_html_e('Enabled', 'nerdywithme-tools'); ?></span>
									</label>
								</div>
								<div class="nwm-tools-admin__fields">
									<label>
										<span><?php esc_html_e('Tooltip / Tab Description', 'nerdywithme-tools'); ?></span>
										<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[tool_descriptions][' . $tool_slug . ']'); ?>" value="<?php echo esc_attr($descriptions[ $tool_slug ] ?? ''); ?>">
									</label>
									<label>
										<span><?php esc_html_e('Tool Page Summary', 'nerdywithme-tools'); ?></span>
										<textarea name="<?php echo esc_attr(self::OPTION_KEY . '[tool_summaries][' . $tool_slug . ']'); ?>" rows="3"><?php echo esc_textarea($summaries[ $tool_slug ] ?? ''); ?></textarea>
									</label>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>

				<section class="nwm-tools-admin__panel" data-nwm-admin-panel="seo">
					<div class="nwm-tools-admin__panel-header">
						<h2><?php esc_html_e('Standalone Tool SEO', 'nerdywithme-tools'); ?></h2>
						<p><?php esc_html_e('Control the browser/meta title for each individual tool URL so every calculator can feel like its own destination.', 'nerdywithme-tools'); ?></p>
					</div>
					<div class="nwm-tools-admin__cards nwm-tools-admin__cards--seo">
						<?php foreach ($order as $tool_slug) : ?>
							<?php if (! isset($tools[ $tool_slug ])) { continue; } ?>
							<article class="nwm-tools-admin__card">
								<div class="nwm-tools-admin__card-head">
									<div>
										<h3><?php echo esc_html($tools[ $tool_slug ]); ?></h3>
										<p><?php echo esc_html__('Used for browser title and search-facing link title.', 'nerdywithme-tools'); ?></p>
									</div>
								</div>
								<div class="nwm-tools-admin__fields">
									<label>
										<span><?php esc_html_e('Meta Title', 'nerdywithme-tools'); ?></span>
										<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[tool_meta_titles][' . $tool_slug . ']'); ?>" value="<?php echo esc_attr($meta_titles[ $tool_slug ] ?? ''); ?>">
									</label>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>

				<section class="nwm-tools-admin__panel" data-nwm-admin-panel="ads">
					<div class="nwm-tools-admin__panel-header">
						<h2><?php esc_html_e('Theme Ad Slots', 'nerdywithme-tools'); ?></h2>
						<p><?php esc_html_e('Paste HTML, banners, affiliate embeds, or shortcode output into the exact slots already wired into the theme.', 'nerdywithme-tools'); ?></p>
					</div>
					<div class="nwm-tools-admin__cards">
						<?php foreach ($this->get_ad_slot_labels() as $slot => $label) : ?>
							<article class="nwm-tools-admin__card">
								<div class="nwm-tools-admin__card-head">
									<div>
										<h3><?php echo esc_html($label); ?></h3>
										<p><code><?php echo esc_html($slot); ?></code></p>
									</div>
								</div>
								<label class="nwm-tools-admin__fields">
									<span><?php esc_html_e('Ad Markup', 'nerdywithme-tools'); ?></span>
									<textarea name="<?php echo esc_attr(self::OPTION_KEY . '[ads][' . $slot . ']'); ?>" rows="6" class="code"><?php echo esc_textarea($settings['ads'][ $slot ] ?? ''); ?></textarea>
								</label>
							</article>
						<?php endforeach; ?>
					</div>
				</section>

				<section class="nwm-tools-admin__panel" data-nwm-admin-panel="help">
					<div class="nwm-tools-admin__panel-header">
						<h2><?php esc_html_e('Shortcodes and Notes', 'nerdywithme-tools'); ?></h2>
						<p><?php esc_html_e('These are the current entry points you can use while the plugin keeps growing.', 'nerdywithme-tools'); ?></p>
					</div>
					<div class="nwm-tools-admin__help">
						<div class="nwm-tools-admin__help-card">
							<h3><?php esc_html_e('Available Shortcodes', 'nerdywithme-tools'); ?></h3>
							<ul>
								<li><code>[nwm_tools_hub]</code></li>
								<li><code>[nwm_risk_calculator]</code></li>
								<li><code>[nwm_ad_slot slot="sidebar"]</code></li>
							</ul>
						</div>
						<div class="nwm-tools-admin__help-card">
							<h3><?php esc_html_e('How tool URLs work', 'nerdywithme-tools'); ?></h3>
							<p><?php esc_html_e('Each tool now has its own URL under the Tools page, for example /tools/risk-calculator/. The same hub still switches instantly without a full reload.', 'nerdywithme-tools'); ?></p>
						</div>
					</div>
				</section>
				<?php
				submit_button(__('Save Tools Settings', 'nerdywithme-tools'));
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Get stored settings.
	 *
	 * @return array
	 */
	public function get_settings() {
		$defaults = array(
			'ads'                             => array_fill_keys(array_keys($this->get_ad_slot_labels()), ''),
			'enable_risk_calculator'          => true,
			'enable_position_size_calculator' => true,
			'enable_pip_value_calculator'     => true,
			'enable_profit_target_calculator' => true,
			'tool_order'                      => 'risk-calculator,position-size,pip-value,profit-target',
			'tool_descriptions'               => $this->get_tool_description_defaults(),
			'tool_summaries'                  => $this->get_tool_summary_defaults(),
			'tool_meta_titles'               => $this->get_tool_meta_title_defaults(),
		);

		return wp_parse_args(get_option(self::OPTION_KEY, array()), $defaults);
	}

	/**
	 * Ad slot labels.
	 *
	 * @return array
	 */
	private function get_ad_slot_labels() {
		return array(
			'homepage_after_hero' => __('Homepage After Hero', 'nerdywithme-tools'),
			'archive_header'      => __('Archive / Page Header Ad', 'nerdywithme-tools'),
			'sidebar'             => __('Sidebar Ad', 'nerdywithme-tools'),
			'single_inline'       => __('Single Post Inline Ad', 'nerdywithme-tools'),
			'footer'              => __('Footer Promo Slot', 'nerdywithme-tools'),
		);
	}

	/**
	 * Default tool descriptions.
	 *
	 * @return array
	 */
	private function get_tool_description_defaults() {
		return array(
			'risk-calculator' => __('Risk, size, and reward planning.', 'nerdywithme-tools'),
			'position-size'   => __('Size a trade from risk amount and stop distance.', 'nerdywithme-tools'),
			'pip-value'       => __('Estimate pip or point value from lot size.', 'nerdywithme-tools'),
			'profit-target'   => __('Target and payoff planner.', 'nerdywithme-tools'),
		);
	}

	/**
	 * Tool labels.
	 *
	 * @return array
	 */
	private function get_tool_labels() {
		return array(
			'risk-calculator' => __('Risk Calculator', 'nerdywithme-tools'),
			'position-size'   => __('Position Size', 'nerdywithme-tools'),
			'pip-value'       => __('Pip / Point Value', 'nerdywithme-tools'),
			'profit-target'   => __('Profit Target', 'nerdywithme-tools'),
		);
	}

	/**
	 * Default tool page summaries.
	 *
	 * @return array
	 */
	private function get_tool_summary_defaults() {
		return array(
			'risk-calculator' => __('Plan safer trades by calculating how much capital to risk, how far your stop sits from entry, and what position size fits your rules before you execute.', 'nerdywithme-tools'),
			'position-size'   => __('Convert a fixed risk amount and stop distance into a cleaner lot size so you stop guessing your exposure on every trade idea.', 'nerdywithme-tools'),
			'pip-value'       => __('Estimate what each pip or point is worth for your position size, then project the value of a move before you place the trade.', 'nerdywithme-tools'),
			'profit-target'   => __('Map out the payoff at your target, compare reward to risk, and see the percentage growth a winning trade could add to your account.', 'nerdywithme-tools'),
		);
	}

	/**
	 * Default tool meta titles.
	 *
	 * @return array
	 */
	private function get_tool_meta_title_defaults() {
		return array(
			'risk-calculator' => __('Risk Calculator for Trade Sizing | NerdyWithMe Tools', 'nerdywithme-tools'),
			'position-size'   => __('Position Size Calculator | NerdyWithMe Tools', 'nerdywithme-tools'),
			'pip-value'       => __('Pip and Point Value Calculator | NerdyWithMe Tools', 'nerdywithme-tools'),
			'profit-target'   => __('Profit Target Calculator | NerdyWithMe Tools', 'nerdywithme-tools'),
		);
	}
}
