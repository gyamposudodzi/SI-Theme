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

		wp_enqueue_media();

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
			'enable_compound_growth_calculator',
			__('Enable Forex Compound Calculator', 'nerdywithme-tools'),
			array($this, 'render_checkbox_field'),
			'nerdywithme-tools',
			'nerdywithme_tools_calculators',
			array(
				'key' => 'enable_compound_growth_calculator',
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
			'ad_settings'                   => array(),
			'social_profiles'               => array(),
			'profile_card'                  => array(),
			'cookie_content'                => array(),
			'enable_risk_calculator'        => ! empty($input['enable_risk_calculator']),
			'enable_position_size_calculator' => ! empty($input['enable_position_size_calculator']),
			'enable_pip_value_calculator'   => ! empty($input['enable_pip_value_calculator']),
			'enable_profit_target_calculator' => ! empty($input['enable_profit_target_calculator']),
			'tool_order'                    => isset($input['tool_order']) ? sanitize_text_field($input['tool_order']) : 'risk-calculator,position-size,pip-value,profit-target,compound-growth',
			'tool_descriptions'             => array(),
			'tool_summaries'                => array(),
			'tool_meta_titles'              => array(),
			'tool_labels'                   => array(),
			'tool_slugs'                    => array(),
		);

		foreach (array_keys($this->get_ad_slot_labels()) as $slot) {
			$sanitized['ads'][ $slot ] = isset($input['ads'][ $slot ]) ? wp_kses_post($input['ads'][ $slot ]) : '';
			$sanitized['ad_settings'][ $slot ] = array(
				'enabled'     => ! empty($input['ad_settings'][ $slot ]['enabled']),
				'mode'        => isset($input['ad_settings'][ $slot ]['mode']) && array_key_exists($input['ad_settings'][ $slot ]['mode'], $this->get_ad_mode_options())
					? sanitize_key($input['ad_settings'][ $slot ]['mode'])
					: 'markup',
				'sticky'      => ! empty($input['ad_settings'][ $slot ]['sticky']),
				'hide_mobile' => ! empty($input['ad_settings'][ $slot ]['hide_mobile']),
				'style'       => isset($input['ad_settings'][ $slot ]['style']) && array_key_exists($input['ad_settings'][ $slot ]['style'], $this->get_ad_style_options())
					? sanitize_key($input['ad_settings'][ $slot ]['style'])
					: 'standard',
				'width_desktop' => isset($input['ad_settings'][ $slot ]['width_desktop']) && array_key_exists($input['ad_settings'][ $slot ]['width_desktop'], $this->get_ad_width_options())
					? sanitize_key($input['ad_settings'][ $slot ]['width_desktop'])
					: 'standard',
				'width_tablet' => isset($input['ad_settings'][ $slot ]['width_tablet']) && array_key_exists($input['ad_settings'][ $slot ]['width_tablet'], $this->get_ad_width_options())
					? sanitize_key($input['ad_settings'][ $slot ]['width_tablet'])
					: 'standard',
				'width_mobile' => isset($input['ad_settings'][ $slot ]['width_mobile']) && array_key_exists($input['ad_settings'][ $slot ]['width_mobile'], $this->get_ad_width_options())
					? sanitize_key($input['ad_settings'][ $slot ]['width_mobile'])
					: 'full',
				'align'       => isset($input['ad_settings'][ $slot ]['align']) && array_key_exists($input['ad_settings'][ $slot ]['align'], $this->get_ad_alignment_options())
					? sanitize_key($input['ad_settings'][ $slot ]['align'])
					: 'left',
				'offset_desktop' => isset($input['ad_settings'][ $slot ]['offset_desktop']) ? max(0, absint($input['ad_settings'][ $slot ]['offset_desktop'])) : 108,
				'offset_tablet'  => isset($input['ad_settings'][ $slot ]['offset_tablet']) ? max(0, absint($input['ad_settings'][ $slot ]['offset_tablet'])) : 96,
				'offset_mobile'  => isset($input['ad_settings'][ $slot ]['offset_mobile']) ? max(0, absint($input['ad_settings'][ $slot ]['offset_mobile'])) : 84,
				'image_url'      => isset($input['ad_settings'][ $slot ]['image_url']) ? esc_url_raw($input['ad_settings'][ $slot ]['image_url']) : '',
				'image_alt'      => isset($input['ad_settings'][ $slot ]['image_alt']) ? sanitize_text_field($input['ad_settings'][ $slot ]['image_alt']) : '',
				'target_url'     => isset($input['ad_settings'][ $slot ]['target_url']) ? esc_url_raw($input['ad_settings'][ $slot ]['target_url']) : '',
				'eyebrow'        => isset($input['ad_settings'][ $slot ]['eyebrow']) ? sanitize_text_field($input['ad_settings'][ $slot ]['eyebrow']) : '',
				'title'          => isset($input['ad_settings'][ $slot ]['title']) ? sanitize_text_field($input['ad_settings'][ $slot ]['title']) : '',
				'copy'           => isset($input['ad_settings'][ $slot ]['copy']) ? sanitize_textarea_field($input['ad_settings'][ $slot ]['copy']) : '',
				'button_label'   => isset($input['ad_settings'][ $slot ]['button_label']) ? sanitize_text_field($input['ad_settings'][ $slot ]['button_label']) : '',
				'meta'           => isset($input['ad_settings'][ $slot ]['meta']) ? sanitize_text_field($input['ad_settings'][ $slot ]['meta']) : '',
				'width'       => isset($input['ad_settings'][ $slot ]['width']) && array_key_exists($input['ad_settings'][ $slot ]['width'], $this->get_ad_width_options())
					? sanitize_key($input['ad_settings'][ $slot ]['width'])
					: 'standard',
			);
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

		foreach ($this->get_tool_label_defaults() as $slug => $label) {
			$sanitized['tool_labels'][ $slug ] = isset($input['tool_labels'][ $slug ]) ? sanitize_text_field($input['tool_labels'][ $slug ]) : $label;
		}

		foreach ($this->get_tool_slug_defaults() as $slug => $default_slug) {
			$raw = isset($input['tool_slugs'][ $slug ]) ? sanitize_title($input['tool_slugs'][ $slug ]) : $default_slug;
			$sanitized['tool_slugs'][ $slug ] = $raw ? $raw : $default_slug;
		}

		foreach ($this->get_social_profile_defaults() as $platform => $default_url) {
			$sanitized['social_profiles'][ $platform ] = isset($input['social_profiles'][ $platform ])
				? esc_url_raw($input['social_profiles'][ $platform ])
				: $default_url;
		}

		$profile_input = is_array($input['profile_card'] ?? null) ? $input['profile_card'] : array();
		$profile_defaults = $this->get_profile_card_defaults();
		$sanitized['profile_card'] = array(
			'button_label' => isset($profile_input['button_label']) ? sanitize_text_field($profile_input['button_label']) : $profile_defaults['button_label'],
			'button_url'   => isset($profile_input['button_url']) ? esc_url_raw($profile_input['button_url']) : $profile_defaults['button_url'],
		);

		$cookie_input    = is_array($input['cookie_content'] ?? null) ? $input['cookie_content'] : array();
		$cookie_defaults = $this->get_cookie_content_defaults();
		foreach ($cookie_defaults as $key => $default_value) {
			if (is_string($default_value) && false !== strpos($key, '_items')) {
				$sanitized['cookie_content'][ $key ] = isset($cookie_input[ $key ]) ? sanitize_textarea_field($cookie_input[ $key ]) : $default_value;
			} else {
				$sanitized['cookie_content'][ $key ] = isset($cookie_input[ $key ]) ? sanitize_textarea_field($cookie_input[ $key ]) : $default_value;
			}
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
		$tool_keys    = $this->get_tool_setting_keys();
		$active_count = 0;
		$descriptions = $settings['tool_descriptions'] ?? $this->get_tool_description_defaults();
		$summaries    = $settings['tool_summaries'] ?? $this->get_tool_summary_defaults();
		$meta_titles  = $settings['tool_meta_titles'] ?? $this->get_tool_meta_title_defaults();
		$tool_labels  = $settings['tool_labels'] ?? $this->get_tool_label_defaults();
		$tool_slugs   = $settings['tool_slugs'] ?? $this->get_tool_slug_defaults();
		$order        = array_filter(array_map('sanitize_key', array_map('trim', explode(',', (string) ($settings['tool_order'] ?? '')))));

		if (empty($order)) {
			$order = array_keys($tools);
		}

		foreach (array_keys($tools) as $tool_slug) {
			if (! in_array($tool_slug, $order, true)) {
				$order[] = $tool_slug;
			}
		}

		foreach ($tool_keys as $setting_key) {
			if (! empty($settings[ $setting_key ])) {
				++$active_count;
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
					<strong><?php esc_html_e('Active tools', 'nerdywithme-tools'); ?></strong>
					<span><?php echo esc_html($active_count); ?></span>
				</div>
			</div>
			<form action="options.php" method="post">
				<?php
				settings_fields('nerdywithme_tools');
				?>
				<div class="nwm-tools-admin__tabs" role="tablist" aria-label="<?php esc_attr_e('Plugin settings sections', 'nerdywithme-tools'); ?>">
					<button type="button" class="nwm-tools-admin__tab is-active" data-nwm-admin-tab="tools"><?php esc_html_e('Tools', 'nerdywithme-tools'); ?></button>
					<button type="button" class="nwm-tools-admin__tab" data-nwm-admin-tab="seo"><?php esc_html_e('SEO', 'nerdywithme-tools'); ?></button>
					<button type="button" class="nwm-tools-admin__tab" data-nwm-admin-tab="profiles"><?php esc_html_e('Profiles', 'nerdywithme-tools'); ?></button>
					<button type="button" class="nwm-tools-admin__tab" data-nwm-admin-tab="cookie"><?php esc_html_e('Cookie', 'nerdywithme-tools'); ?></button>
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
							<?php $setting_key = $tool_keys[ $tool_slug ] ?? ''; ?>
							<article class="nwm-tools-admin__card" data-tool-id="<?php echo esc_attr($tool_slug); ?>">
								<div class="nwm-tools-admin__card-head">
									<div class="nwm-tools-admin__drag" aria-hidden="true">⋮⋮</div>
									<div>
										<h3><?php echo esc_html($tools[ $tool_slug ]); ?></h3>
										<p><code><?php echo esc_html($tool_slug); ?></code></p>
									</div>
									<label class="nwm-tools-admin__toggle">
										<input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY . '[' . $setting_key . ']'); ?>" value="1" <?php checked($setting_key && ! empty($settings[ $setting_key ])); ?>>
										<span><?php esc_html_e('Enabled', 'nerdywithme-tools'); ?></span>
									</label>
								</div>
								<div class="nwm-tools-admin__fields">
									<label>
										<span><?php esc_html_e('Tool Name', 'nerdywithme-tools'); ?></span>
										<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[tool_labels][' . $tool_slug . ']'); ?>" value="<?php echo esc_attr($tool_labels[ $tool_slug ] ?? $tools[ $tool_slug ]); ?>">
									</label>
									<label>
										<span><?php esc_html_e('Tool Slug (URL)', 'nerdywithme-tools'); ?></span>
										<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[tool_slugs][' . $tool_slug . ']'); ?>" value="<?php echo esc_attr($tool_slugs[ $tool_slug ] ?? $tool_slug); ?>" placeholder="<?php echo esc_attr($tool_slug); ?>">
										<small class="description"><?php esc_html_e('Used for /tools/your-slug/. Keep it short and unique. Save, then re-save Permalinks if URLs do not update.', 'nerdywithme-tools'); ?></small>
									</label>
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

				<section class="nwm-tools-admin__panel" data-nwm-admin-panel="profiles">
					<div class="nwm-tools-admin__panel-header">
						<h2><?php esc_html_e('Shared Social Profiles', 'nerdywithme-tools'); ?></h2>
						<p><?php esc_html_e('Store your core social profile URLs and sidebar follow CTA here so theme changes do not make you re-enter them. The theme will reuse these values in headers, footers, cards, and the sidebar profile button.', 'nerdywithme-tools'); ?></p>
					</div>
					<div class="nwm-tools-admin__cards nwm-tools-admin__cards--seo">
						<article class="nwm-tools-admin__card">
							<div class="nwm-tools-admin__card-head">
								<div>
									<h3><?php esc_html_e('Social profile URLs', 'nerdywithme-tools'); ?></h3>
									<p><?php esc_html_e('Add the platforms you actually use. Leave any unused profile blank.', 'nerdywithme-tools'); ?></p>
								</div>
							</div>
							<div class="nwm-tools-admin__fields">
								<?php foreach ($this->get_social_profile_labels() as $platform => $label) : ?>
									<label>
										<span><?php echo esc_html($label); ?></span>
										<input type="url" name="<?php echo esc_attr(self::OPTION_KEY . '[social_profiles][' . $platform . ']'); ?>" value="<?php echo esc_attr($settings['social_profiles'][ $platform ] ?? ''); ?>" placeholder="https://">
									</label>
								<?php endforeach; ?>
							</div>
						</article>
						<article class="nwm-tools-admin__card">
							<div class="nwm-tools-admin__card-head">
								<div>
									<h3><?php esc_html_e('Sidebar Profile Button', 'nerdywithme-tools'); ?></h3>
									<p><?php esc_html_e('Control the label and destination for the sidebar profile card button from the plugin so it survives theme swaps.', 'nerdywithme-tools'); ?></p>
								</div>
							</div>
							<div class="nwm-tools-admin__fields">
								<label>
									<span><?php esc_html_e('Button Label', 'nerdywithme-tools'); ?></span>
									<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[profile_card][button_label]'); ?>" value="<?php echo esc_attr($settings['profile_card']['button_label'] ?? $this->get_profile_card_defaults()['button_label']); ?>">
								</label>
								<label>
									<span><?php esc_html_e('Button URL', 'nerdywithme-tools'); ?></span>
									<input type="url" name="<?php echo esc_attr(self::OPTION_KEY . '[profile_card][button_url]'); ?>" value="<?php echo esc_attr($settings['profile_card']['button_url'] ?? $this->get_profile_card_defaults()['button_url']); ?>" placeholder="https://">
								</label>
							</div>
						</article>
					</div>
				</section>

				<section class="nwm-tools-admin__panel" data-nwm-admin-panel="cookie">
					<div class="nwm-tools-admin__panel-header">
						<h2><?php esc_html_e('Cookie Banner and Modal Copy', 'nerdywithme-tools'); ?></h2>
						<p><?php esc_html_e('Edit the wording shown in the lightweight cookie banner and cookie preferences modal without touching theme files.', 'nerdywithme-tools'); ?></p>
					</div>
					<div class="nwm-tools-admin__cards nwm-tools-admin__cards--seo">
						<article class="nwm-tools-admin__card">
							<div class="nwm-tools-admin__card-head">
								<div>
									<h3><?php esc_html_e('Banner copy', 'nerdywithme-tools'); ?></h3>
									<p><?php esc_html_e('This is the first lightweight message readers see before opening the full cookie panel.', 'nerdywithme-tools'); ?></p>
								</div>
							</div>
							<div class="nwm-tools-admin__fields">
								<?php $cookie_content = $settings['cookie_content'] ?? $this->get_cookie_content_defaults(); ?>
								<label>
									<span><?php esc_html_e('Banner title', 'nerdywithme-tools'); ?></span>
									<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][banner_title]'); ?>" value="<?php echo esc_attr($cookie_content['banner_title'] ?? ''); ?>">
								</label>
								<label>
									<span><?php esc_html_e('Banner message', 'nerdywithme-tools'); ?></span>
									<textarea name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][banner_text]'); ?>" rows="3"><?php echo esc_textarea($cookie_content['banner_text'] ?? ''); ?></textarea>
								</label>
								<label>
									<span><?php esc_html_e('Cookie details button', 'nerdywithme-tools'); ?></span>
									<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][details_label]'); ?>" value="<?php echo esc_attr($cookie_content['details_label'] ?? ''); ?>">
								</label>
								<label>
									<span><?php esc_html_e('Necessary only button', 'nerdywithme-tools'); ?></span>
									<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][necessary_label]'); ?>" value="<?php echo esc_attr($cookie_content['necessary_label'] ?? ''); ?>">
								</label>
								<label>
									<span><?php esc_html_e('Accept all button', 'nerdywithme-tools'); ?></span>
									<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][accept_label]'); ?>" value="<?php echo esc_attr($cookie_content['accept_label'] ?? ''); ?>">
								</label>
								<label>
									<span><?php esc_html_e('Floating cookie button label', 'nerdywithme-tools'); ?></span>
									<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][fab_label]'); ?>" value="<?php echo esc_attr($cookie_content['fab_label'] ?? ''); ?>">
								</label>
							</div>
						</article>
						<article class="nwm-tools-admin__card">
							<div class="nwm-tools-admin__card-head">
								<div>
									<h3><?php esc_html_e('Modal header and summary', 'nerdywithme-tools'); ?></h3>
									<p><?php esc_html_e('These fields control the main introduction inside the cookie preferences modal.', 'nerdywithme-tools'); ?></p>
								</div>
							</div>
							<div class="nwm-tools-admin__fields">
								<label>
									<span><?php esc_html_e('Modal eyebrow', 'nerdywithme-tools'); ?></span>
									<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][modal_eyebrow]'); ?>" value="<?php echo esc_attr($cookie_content['modal_eyebrow'] ?? ''); ?>">
								</label>
								<label>
									<span><?php esc_html_e('Modal title', 'nerdywithme-tools'); ?></span>
									<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][modal_title]'); ?>" value="<?php echo esc_attr($cookie_content['modal_title'] ?? ''); ?>">
								</label>
								<label>
									<span><?php esc_html_e('Current choice label', 'nerdywithme-tools'); ?></span>
									<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][status_label]'); ?>" value="<?php echo esc_attr($cookie_content['status_label'] ?? ''); ?>">
								</label>
								<label>
									<span><?php esc_html_e('Summary copy', 'nerdywithme-tools'); ?></span>
									<textarea name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][summary_text]'); ?>" rows="4"><?php echo esc_textarea($cookie_content['summary_text'] ?? ''); ?></textarea>
								</label>
							</div>
						</article>
						<article class="nwm-tools-admin__card">
							<div class="nwm-tools-admin__card-head">
								<div>
									<h3><?php esc_html_e('Essential cookies section', 'nerdywithme-tools'); ?></h3>
									<p><?php esc_html_e('Use one line per bullet item.', 'nerdywithme-tools'); ?></p>
								</div>
							</div>
							<div class="nwm-tools-admin__fields">
								<label>
									<span><?php esc_html_e('Section title', 'nerdywithme-tools'); ?></span>
									<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][essential_title]'); ?>" value="<?php echo esc_attr($cookie_content['essential_title'] ?? ''); ?>">
								</label>
								<label>
									<span><?php esc_html_e('Section body', 'nerdywithme-tools'); ?></span>
									<textarea name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][essential_body]'); ?>" rows="4"><?php echo esc_textarea($cookie_content['essential_body'] ?? ''); ?></textarea>
								</label>
								<label>
									<span><?php esc_html_e('Bullet items', 'nerdywithme-tools'); ?></span>
									<textarea name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][essential_items]'); ?>" rows="5"><?php echo esc_textarea($cookie_content['essential_items'] ?? ''); ?></textarea>
								</label>
							</div>
						</article>
						<article class="nwm-tools-admin__card">
							<div class="nwm-tools-admin__card-head">
								<div>
									<h3><?php esc_html_e('Optional cookies section', 'nerdywithme-tools'); ?></h3>
									<p><?php esc_html_e('Use one line per bullet item.', 'nerdywithme-tools'); ?></p>
								</div>
							</div>
							<div class="nwm-tools-admin__fields">
								<label>
									<span><?php esc_html_e('Section title', 'nerdywithme-tools'); ?></span>
									<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][optional_title]'); ?>" value="<?php echo esc_attr($cookie_content['optional_title'] ?? ''); ?>">
								</label>
								<label>
									<span><?php esc_html_e('Section body', 'nerdywithme-tools'); ?></span>
									<textarea name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][optional_body]'); ?>" rows="4"><?php echo esc_textarea($cookie_content['optional_body'] ?? ''); ?></textarea>
								</label>
								<label>
									<span><?php esc_html_e('Bullet items', 'nerdywithme-tools'); ?></span>
									<textarea name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][optional_items]'); ?>" rows="5"><?php echo esc_textarea($cookie_content['optional_items'] ?? ''); ?></textarea>
								</label>
							</div>
						</article>
						<article class="nwm-tools-admin__card">
							<div class="nwm-tools-admin__card-head">
								<div>
									<h3><?php esc_html_e('Storage at a glance', 'nerdywithme-tools'); ?></h3>
									<p><?php esc_html_e('Edit the three cards that summarize storage type, retention, and user control.', 'nerdywithme-tools'); ?></p>
								</div>
							</div>
							<div class="nwm-tools-admin__fields">
								<label><span><?php esc_html_e('Section title', 'nerdywithme-tools'); ?></span><input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][glance_title]'); ?>" value="<?php echo esc_attr($cookie_content['glance_title'] ?? ''); ?>"></label>
								<label><span><?php esc_html_e('Type label', 'nerdywithme-tools'); ?></span><input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][glance_type_label]'); ?>" value="<?php echo esc_attr($cookie_content['glance_type_label'] ?? ''); ?>"></label>
								<label><span><?php esc_html_e('Type value', 'nerdywithme-tools'); ?></span><textarea name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][glance_type_value]'); ?>" rows="2"><?php echo esc_textarea($cookie_content['glance_type_value'] ?? ''); ?></textarea></label>
								<label><span><?php esc_html_e('Retention label', 'nerdywithme-tools'); ?></span><input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][glance_retention_label]'); ?>" value="<?php echo esc_attr($cookie_content['glance_retention_label'] ?? ''); ?>"></label>
								<label><span><?php esc_html_e('Retention value', 'nerdywithme-tools'); ?></span><textarea name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][glance_retention_value]'); ?>" rows="2"><?php echo esc_textarea($cookie_content['glance_retention_value'] ?? ''); ?></textarea></label>
								<label><span><?php esc_html_e('Control label', 'nerdywithme-tools'); ?></span><input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][glance_control_label]'); ?>" value="<?php echo esc_attr($cookie_content['glance_control_label'] ?? ''); ?>"></label>
								<label><span><?php esc_html_e('Control value', 'nerdywithme-tools'); ?></span><textarea name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][glance_control_value]'); ?>" rows="2"><?php echo esc_textarea($cookie_content['glance_control_value'] ?? ''); ?></textarea></label>
							</div>
						</article>
						<article class="nwm-tools-admin__card">
							<div class="nwm-tools-admin__card-head">
								<div>
									<h3><?php esc_html_e('Preferences section', 'nerdywithme-tools'); ?></h3>
									<p><?php esc_html_e('Control the copy around the essential and optional cookie toggles.', 'nerdywithme-tools'); ?></p>
								</div>
							</div>
							<div class="nwm-tools-admin__fields">
								<label><span><?php esc_html_e('Section title', 'nerdywithme-tools'); ?></span><input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][preferences_title]'); ?>" value="<?php echo esc_attr($cookie_content['preferences_title'] ?? ''); ?>"></label>
								<label><span><?php esc_html_e('Essential preference title', 'nerdywithme-tools'); ?></span><input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][pref_essential_title]'); ?>" value="<?php echo esc_attr($cookie_content['pref_essential_title'] ?? ''); ?>"></label>
								<label><span><?php esc_html_e('Essential preference body', 'nerdywithme-tools'); ?></span><textarea name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][pref_essential_body]'); ?>" rows="3"><?php echo esc_textarea($cookie_content['pref_essential_body'] ?? ''); ?></textarea></label>
								<label><span><?php esc_html_e('Optional preference title', 'nerdywithme-tools'); ?></span><input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][pref_optional_title]'); ?>" value="<?php echo esc_attr($cookie_content['pref_optional_title'] ?? ''); ?>"></label>
								<label><span><?php esc_html_e('Optional preference body', 'nerdywithme-tools'); ?></span><textarea name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][pref_optional_body]'); ?>" rows="3"><?php echo esc_textarea($cookie_content['pref_optional_body'] ?? ''); ?></textarea></label>
								<label><span><?php esc_html_e('Privacy policy link label', 'nerdywithme-tools'); ?></span><input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][privacy_label]'); ?>" value="<?php echo esc_attr($cookie_content['privacy_label'] ?? ''); ?>"></label>
								<label><span><?php esc_html_e('Save preferences button', 'nerdywithme-tools'); ?></span><input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][save_preferences_label]'); ?>" value="<?php echo esc_attr($cookie_content['save_preferences_label'] ?? ''); ?>"></label>
								<label><span><?php esc_html_e('Reset choice button', 'nerdywithme-tools'); ?></span><input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[cookie_content][reset_choice_label]'); ?>" value="<?php echo esc_attr($cookie_content['reset_choice_label'] ?? ''); ?>"></label>
							</div>
						</article>
					</div>
				</section>

				<section class="nwm-tools-admin__panel" data-nwm-admin-panel="ads">
					<div class="nwm-tools-admin__panel-header">
						<h2><?php esc_html_e('Theme Ad Slots', 'nerdywithme-tools'); ?></h2>
						<p><?php esc_html_e('Paste HTML, banners, affiliate embeds, or shortcode output into the exact slots already wired into the theme, then control their behavior slot by slot.', 'nerdywithme-tools'); ?></p>
					</div>
					<div class="nwm-tools-admin__cards">
						<?php foreach ($this->get_ad_slot_labels() as $slot => $label) : ?>
							<article class="nwm-tools-admin__card" data-nwm-ad-card>
								<div class="nwm-tools-admin__card-head">
									<div>
										<h3><?php echo esc_html($label); ?></h3>
										<p><code><?php echo esc_html($slot); ?></code></p>
									</div>
								</div>
								<label class="nwm-tools-admin__fields nwm-tools-admin__fields--ad-markup" data-nwm-ad-group="markup">
									<span><?php esc_html_e('Ad Markup', 'nerdywithme-tools'); ?></span>
									<textarea name="<?php echo esc_attr(self::OPTION_KEY . '[ads][' . $slot . ']'); ?>" rows="6" class="code"><?php echo esc_textarea($settings['ads'][ $slot ] ?? ''); ?></textarea>
								</label>
								<div class="nwm-tools-admin__ad-options">
									<label class="nwm-tools-admin__toggle">
										<input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][enabled]'); ?>" value="1" <?php checked(! empty($settings['ad_settings'][ $slot ]['enabled'])); ?>>
										<span><?php esc_html_e('Enable this slot', 'nerdywithme-tools'); ?></span>
									</label>
									<label>
										<span><?php esc_html_e('Content mode', 'nerdywithme-tools'); ?></span>
										<select name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][mode]'); ?>" data-nwm-ad-mode>
											<?php foreach ($this->get_ad_mode_options() as $mode_key => $mode_label) : ?>
												<option value="<?php echo esc_attr($mode_key); ?>" <?php selected($settings['ad_settings'][ $slot ]['mode'] ?? 'markup', $mode_key); ?>><?php echo esc_html($mode_label); ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label class="nwm-tools-admin__toggle">
										<input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][sticky]'); ?>" value="1" <?php checked(! empty($settings['ad_settings'][ $slot ]['sticky'])); ?>>
										<span><?php esc_html_e('Make this slot sticky', 'nerdywithme-tools'); ?></span>
									</label>
									<label class="nwm-tools-admin__toggle">
										<input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][hide_mobile]'); ?>" value="1" <?php checked(! empty($settings['ad_settings'][ $slot ]['hide_mobile'])); ?>>
										<span><?php esc_html_e('Hide on phone widths', 'nerdywithme-tools'); ?></span>
									</label>
									<label>
										<span><?php esc_html_e('Slot style', 'nerdywithme-tools'); ?></span>
										<select name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][style]'); ?>">
											<?php foreach ($this->get_ad_style_options() as $style_key => $style_label) : ?>
												<option value="<?php echo esc_attr($style_key); ?>" <?php selected($settings['ad_settings'][ $slot ]['style'] ?? 'standard', $style_key); ?>><?php echo esc_html($style_label); ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span><?php esc_html_e('Desktop width', 'nerdywithme-tools'); ?></span>
										<select name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][width_desktop]'); ?>">
											<?php foreach ($this->get_ad_width_options() as $width_key => $width_label) : ?>
												<option value="<?php echo esc_attr($width_key); ?>" <?php selected($settings['ad_settings'][ $slot ]['width_desktop'] ?? ($settings['ad_settings'][ $slot ]['width'] ?? 'standard'), $width_key); ?>><?php echo esc_html($width_label); ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span><?php esc_html_e('Tablet width', 'nerdywithme-tools'); ?></span>
										<select name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][width_tablet]'); ?>">
											<?php foreach ($this->get_ad_width_options() as $width_key => $width_label) : ?>
												<option value="<?php echo esc_attr($width_key); ?>" <?php selected($settings['ad_settings'][ $slot ]['width_tablet'] ?? 'standard', $width_key); ?>><?php echo esc_html($width_label); ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span><?php esc_html_e('Phone width', 'nerdywithme-tools'); ?></span>
										<select name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][width_mobile]'); ?>">
											<?php foreach ($this->get_ad_width_options() as $width_key => $width_label) : ?>
												<option value="<?php echo esc_attr($width_key); ?>" <?php selected($settings['ad_settings'][ $slot ]['width_mobile'] ?? 'full', $width_key); ?>><?php echo esc_html($width_label); ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span><?php esc_html_e('Alignment', 'nerdywithme-tools'); ?></span>
										<select name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][align]'); ?>">
											<?php foreach ($this->get_ad_alignment_options() as $align_key => $align_label) : ?>
												<option value="<?php echo esc_attr($align_key); ?>" <?php selected($settings['ad_settings'][ $slot ]['align'] ?? 'left', $align_key); ?>><?php echo esc_html($align_label); ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span><?php esc_html_e('Desktop sticky offset (px)', 'nerdywithme-tools'); ?></span>
										<input type="number" min="0" step="1" name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][offset_desktop]'); ?>" value="<?php echo esc_attr((string) ($settings['ad_settings'][ $slot ]['offset_desktop'] ?? 108)); ?>">
									</label>
									<label>
										<span><?php esc_html_e('Tablet sticky offset (px)', 'nerdywithme-tools'); ?></span>
										<input type="number" min="0" step="1" name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][offset_tablet]'); ?>" value="<?php echo esc_attr((string) ($settings['ad_settings'][ $slot ]['offset_tablet'] ?? 96)); ?>">
									</label>
									<label>
										<span><?php esc_html_e('Phone sticky offset (px)', 'nerdywithme-tools'); ?></span>
										<input type="number" min="0" step="1" name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][offset_mobile]'); ?>" value="<?php echo esc_attr((string) ($settings['ad_settings'][ $slot ]['offset_mobile'] ?? 84)); ?>">
									</label>
								</div>
								<div class="nwm-tools-admin__fields nwm-tools-admin__fields--ad-content" data-nwm-ad-group="managed">
									<label>
										<span><?php esc_html_e('Image URL', 'nerdywithme-tools'); ?></span>
										<div class="nwm-tools-admin__media-field">
											<input type="url" name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][image_url]'); ?>" value="<?php echo esc_attr($settings['ad_settings'][ $slot ]['image_url'] ?? ''); ?>" data-nwm-media-target>
											<button type="button" class="button button-secondary" data-nwm-media-open><?php esc_html_e('Choose Image', 'nerdywithme-tools'); ?></button>
										</div>
									</label>
									<label>
										<span><?php esc_html_e('Image Alt Text', 'nerdywithme-tools'); ?></span>
										<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][image_alt]'); ?>" value="<?php echo esc_attr($settings['ad_settings'][ $slot ]['image_alt'] ?? ''); ?>">
									</label>
									<label>
										<span><?php esc_html_e('Target URL', 'nerdywithme-tools'); ?></span>
										<input type="url" name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][target_url]'); ?>" value="<?php echo esc_attr($settings['ad_settings'][ $slot ]['target_url'] ?? ''); ?>">
									</label>
									<label>
										<span><?php esc_html_e('Eyebrow', 'nerdywithme-tools'); ?></span>
										<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][eyebrow]'); ?>" value="<?php echo esc_attr($settings['ad_settings'][ $slot ]['eyebrow'] ?? ''); ?>">
									</label>
									<label>
										<span><?php esc_html_e('Title', 'nerdywithme-tools'); ?></span>
										<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][title]'); ?>" value="<?php echo esc_attr($settings['ad_settings'][ $slot ]['title'] ?? ''); ?>">
									</label>
									<label>
										<span><?php esc_html_e('Button Label', 'nerdywithme-tools'); ?></span>
										<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][button_label]'); ?>" value="<?php echo esc_attr($settings['ad_settings'][ $slot ]['button_label'] ?? ''); ?>">
									</label>
									<label>
										<span><?php esc_html_e('Meta Line', 'nerdywithme-tools'); ?></span>
										<input type="text" name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][meta]'); ?>" value="<?php echo esc_attr($settings['ad_settings'][ $slot ]['meta'] ?? ''); ?>">
									</label>
									<label class="nwm-tools-admin__fields nwm-tools-admin__fields--full">
										<span><?php esc_html_e('Body Copy', 'nerdywithme-tools'); ?></span>
										<textarea name="<?php echo esc_attr(self::OPTION_KEY . '[ad_settings][' . $slot . '][copy]'); ?>" rows="3"><?php echo esc_textarea($settings['ad_settings'][ $slot ]['copy'] ?? ''); ?></textarea>
									</label>
								</div>
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
			'ad_settings'                     => $this->get_ad_behavior_defaults(),
			'social_profiles'                 => $this->get_social_profile_defaults(),
			'profile_card'                    => $this->get_profile_card_defaults(),
			'cookie_content'                  => $this->get_cookie_content_defaults(),
			'enable_risk_calculator'          => true,
			'enable_position_size_calculator' => true,
			'enable_pip_value_calculator'     => true,
			'enable_profit_target_calculator' => true,
			'enable_compound_growth_calculator' => true,
			'tool_order'                      => 'risk-calculator,position-size,pip-value,profit-target,compound-growth',
			'tool_descriptions'               => $this->get_tool_description_defaults(),
			'tool_summaries'                  => $this->get_tool_summary_defaults(),
			'tool_meta_titles'               => $this->get_tool_meta_title_defaults(),
			'tool_labels'                     => $this->get_tool_label_defaults(),
			'tool_slugs'                      => $this->get_tool_slug_defaults(),
		);

		$settings = wp_parse_args(get_option(self::OPTION_KEY, array()), $defaults);

		$settings['ads']         = wp_parse_args(is_array($settings['ads']) ? $settings['ads'] : array(), $defaults['ads']);
		$settings['ad_settings'] = wp_parse_args(is_array($settings['ad_settings']) ? $settings['ad_settings'] : array(), $defaults['ad_settings']);
		$settings['social_profiles'] = wp_parse_args(is_array($settings['social_profiles']) ? $settings['social_profiles'] : array(), $defaults['social_profiles']);
		$settings['profile_card'] = wp_parse_args(is_array($settings['profile_card']) ? $settings['profile_card'] : array(), $defaults['profile_card']);
		$settings['cookie_content'] = wp_parse_args(is_array($settings['cookie_content']) ? $settings['cookie_content'] : array(), $defaults['cookie_content']);

		foreach ($defaults['ad_settings'] as $slot => $slot_defaults) {
			$settings['ad_settings'][ $slot ] = wp_parse_args(
				is_array($settings['ad_settings'][ $slot ] ?? null) ? $settings['ad_settings'][ $slot ] : array(),
				$slot_defaults
			);
		}

		$settings['tool_labels'] = wp_parse_args(is_array($settings['tool_labels']) ? $settings['tool_labels'] : array(), $defaults['tool_labels']);
		$settings['tool_slugs']  = wp_parse_args(is_array($settings['tool_slugs']) ? $settings['tool_slugs'] : array(), $defaults['tool_slugs']);

		return $settings;
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
	 * Shared social profile labels.
	 *
	 * @return array
	 */
	private function get_social_profile_labels() {
		return array(
			'facebook'    => __('Facebook URL', 'nerdywithme-tools'),
			'x'           => __('X URL', 'nerdywithme-tools'),
			'instagram'   => __('Instagram URL', 'nerdywithme-tools'),
			'pinterest'   => __('Pinterest URL', 'nerdywithme-tools'),
			'youtube'     => __('YouTube URL', 'nerdywithme-tools'),
			'tiktok'      => __('TikTok URL', 'nerdywithme-tools'),
			'discord'     => __('Discord URL', 'nerdywithme-tools'),
			'telegram'    => __('Telegram URL', 'nerdywithme-tools'),
			'linkedin'    => __('LinkedIn URL', 'nerdywithme-tools'),
			'github'      => __('GitHub URL', 'nerdywithme-tools'),
			'reddit'      => __('Reddit URL', 'nerdywithme-tools'),
			'tradingview' => __('TradingView URL', 'nerdywithme-tools'),
			'whatsapp'    => __('WhatsApp URL', 'nerdywithme-tools'),
			'snapchat'    => __('Snapchat URL', 'nerdywithme-tools'),
			'threads'     => __('Threads URL', 'nerdywithme-tools'),
			'twitch'      => __('Twitch URL', 'nerdywithme-tools'),
			'newsletter'  => __('Newsletter URL', 'nerdywithme-tools'),
			'website'     => __('Website URL', 'nerdywithme-tools'),
		);
	}

	/**
	 * Shared social profile defaults.
	 *
	 * @return array
	 */
	private function get_social_profile_defaults() {
		return array_fill_keys(array_keys($this->get_social_profile_labels()), '');
	}

	/**
	 * Sidebar profile card defaults.
	 *
	 * @return array
	 */
	private function get_profile_card_defaults() {
		return array(
			'button_label' => __('Follow', 'nerdywithme-tools'),
			'button_url'   => '#',
		);
	}

	/**
	 * Cookie banner/modal copy defaults.
	 *
	 * @return array
	 */
	private function get_cookie_content_defaults() {
		return array(
			'banner_title'            => __('Cookies on NerdyWithMe', 'nerdywithme-tools'),
			'banner_text'             => __('We use essential cookies to keep the site working and optional cookies to understand what readers find useful.', 'nerdywithme-tools'),
			'details_label'           => __('Cookie details', 'nerdywithme-tools'),
			'necessary_label'         => __('Necessary only', 'nerdywithme-tools'),
			'accept_label'            => __('Accept all', 'nerdywithme-tools'),
			'fab_label'               => __('Cookies', 'nerdywithme-tools'),
			'modal_eyebrow'           => __('Cookie preferences', 'nerdywithme-tools'),
			'modal_title'             => __('How NerdyWithMe uses cookies', 'nerdywithme-tools'),
			'status_label'            => __('Current choice', 'nerdywithme-tools'),
			'summary_text'            => __('This panel explains what the site stores in your browser today and what may be stored when optional features are enabled later.', 'nerdywithme-tools'),
			'essential_title'         => __('Essential cookies', 'nerdywithme-tools'),
			'essential_body'          => __('These keep core parts of the site working, such as your cookie choice and standard WordPress behaviour when you interact with forms or comments.', 'nerdywithme-tools'),
			'essential_items'         => "nwm_cookie_consent: remembers whether you chose necessary-only or accepted optional cookies.\nWordPress comment cookies: may remember your name, email, and website if you ask the comment form to save them.",
			'optional_title'          => __('Optional cookies', 'nerdywithme-tools'),
			'optional_body'           => __('Optional storage is there for convenience and future measurement features. It is not required to browse the site.', 'nerdywithme-tools'),
			'optional_items'          => "nwmActiveTool: remembers the last calculator tab you used in the tools area so you can pick up where you left off.\nFuture analytics or ad measurement tools should only run after optional consent is accepted.",
			'glance_title'            => __('Storage at a glance', 'nerdywithme-tools'),
			'glance_type_label'       => __('Type', 'nerdywithme-tools'),
			'glance_type_value'       => __('Cookie and local storage', 'nerdywithme-tools'),
			'glance_retention_label'  => __('Retention', 'nerdywithme-tools'),
			'glance_retention_value'  => __('Consent is currently kept for 180 days unless you clear your browser data.', 'nerdywithme-tools'),
			'glance_control_label'    => __('Control', 'nerdywithme-tools'),
			'glance_control_value'    => __('You can return to this panel any time using the cookie button at the lower left.', 'nerdywithme-tools'),
			'preferences_title'       => __('Choose what you allow', 'nerdywithme-tools'),
			'pref_essential_title'    => __('Essential cookies', 'nerdywithme-tools'),
			'pref_essential_body'     => __('Required for core site behaviour and always on.', 'nerdywithme-tools'),
			'pref_optional_title'     => __('Optional cookies', 'nerdywithme-tools'),
			'pref_optional_body'      => __('Allow convenience storage and future measurement features.', 'nerdywithme-tools'),
			'privacy_label'           => __('Read the full privacy policy', 'nerdywithme-tools'),
			'save_preferences_label'  => __('Save preferences', 'nerdywithme-tools'),
			'reset_choice_label'      => __('Reset choice', 'nerdywithme-tools'),
		);
	}

	/**
	 * Per-slot ad behavior defaults.
	 *
	 * @return array
	 */
	private function get_ad_behavior_defaults() {
		$defaults = array();

		foreach (array_keys($this->get_ad_slot_labels()) as $slot) {
			$defaults[ $slot ] = array(
				'enabled'     => true,
				'sticky'      => in_array($slot, array('sidebar', 'archive_header'), true),
				'hide_mobile' => false,
				'style'       => 'standard',
				'width'       => 'standard',
				'width_desktop' => 'standard',
				'width_tablet'  => 'standard',
				'width_mobile'  => 'full',
				'align'         => 'left',
				'offset_desktop' => 108,
				'offset_tablet'  => 96,
				'offset_mobile'  => 84,
				'mode'           => 'markup',
				'image_url'      => '',
				'image_alt'      => '',
				'target_url'     => '',
				'eyebrow'        => '',
				'title'          => '',
				'copy'           => '',
				'button_label'   => '',
				'meta'           => '',
			);
		}

		$defaults['sidebar']['style'] = 'premium';
		$defaults['sidebar']['width'] = 'wide';
		$defaults['sidebar']['width_desktop'] = 'wide';
		$defaults['sidebar']['width_tablet'] = 'standard';

		return $defaults;
	}

	/**
	 * Ad style options.
	 *
	 * @return array
	 */
	private function get_ad_style_options() {
		return array(
			'standard' => __('Standard', 'nerdywithme-tools'),
			'premium'  => __('Premium', 'nerdywithme-tools'),
			'minimal'  => __('Minimal', 'nerdywithme-tools'),
		);
	}

	/**
	 * Ad content mode options.
	 *
	 * @return array
	 */
	private function get_ad_mode_options() {
		return array(
			'markup' => __('Raw Markup / Embed', 'nerdywithme-tools'),
			'promo'  => __('Managed Promo Card', 'nerdywithme-tools'),
			'image'  => __('Image Banner', 'nerdywithme-tools'),
		);
	}

	/**
	 * Ad width options.
	 *
	 * @return array
	 */
	private function get_ad_width_options() {
		return array(
			'narrow'   => __('Narrow', 'nerdywithme-tools'),
			'standard' => __('Standard', 'nerdywithme-tools'),
			'wide'     => __('Wide', 'nerdywithme-tools'),
			'full'     => __('Full', 'nerdywithme-tools'),
		);
	}

	/**
	 * Ad alignment options.
	 *
	 * @return array
	 */
	private function get_ad_alignment_options() {
		return array(
			'left'   => __('Left', 'nerdywithme-tools'),
			'center' => __('Center', 'nerdywithme-tools'),
			'right'  => __('Right', 'nerdywithme-tools'),
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
			'compound-growth' => __('Project compounded account growth over time.', 'nerdywithme-tools'),
		);
	}

	/**
	 * Default tool labels.
	 *
	 * @return array
	 */
	private function get_tool_label_defaults() {
		return array(
			'risk-calculator' => __('Risk Calculator', 'nerdywithme-tools'),
			'position-size'   => __('Position Size', 'nerdywithme-tools'),
			'pip-value'       => __('Pip / Point Value', 'nerdywithme-tools'),
			'profit-target'   => __('Profit Target', 'nerdywithme-tools'),
			'compound-growth' => __('Forex Compound Calculator', 'nerdywithme-tools'),
		);
	}

	/**
	 * Tool labels.
	 *
	 * @return array
	 */
	private function get_tool_labels() {
		$defaults = $this->get_tool_label_defaults();
		$settings = $this->get_settings();
		$labels   = is_array($settings['tool_labels'] ?? null) ? $settings['tool_labels'] : array();
		$output   = array();

		foreach ($defaults as $slug => $label) {
			$value = sanitize_text_field($labels[ $slug ] ?? $label);
			$output[ $slug ] = $value ? $value : $label;
		}

		return $output;
	}

	/**
	 * Default tool slugs.
	 *
	 * @return array
	 */
	private function get_tool_slug_defaults() {
		return array(
			'risk-calculator' => 'risk-calculator',
			'position-size'   => 'position-size',
			'pip-value'       => 'pip-value',
			'profit-target'   => 'profit-target',
			'compound-growth' => 'compound-growth',
		);
	}

	/**
	 * Setting keys for tool enable toggles.
	 *
	 * @return array
	 */
	private function get_tool_setting_keys() {
		return array(
			'risk-calculator' => 'enable_risk_calculator',
			'position-size'   => 'enable_position_size_calculator',
			'pip-value'       => 'enable_pip_value_calculator',
			'profit-target'   => 'enable_profit_target_calculator',
			'compound-growth' => 'enable_compound_growth_calculator',
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
			'compound-growth' => __('Project how your account could grow over time with compounded returns, recurring contributions, and a period you choose.', 'nerdywithme-tools'),
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
			'compound-growth' => __('Forex Compound Calculator | NerdyWithMe Tools', 'nerdywithme-tools'),
		);
	}
}
