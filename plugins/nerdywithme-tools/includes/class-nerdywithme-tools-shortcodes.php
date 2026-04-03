<?php
/**
 * Shortcodes and public helpers.
 *
 * @package NerdyWithMeTools
 */

if (! defined('ABSPATH')) {
	exit;
}

class NerdyWithMe_Tools_Shortcodes {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode('nwm_ad_slot', array($this, 'render_ad_slot_shortcode'));
		add_shortcode('nwm_risk_calculator', array($this, 'render_risk_calculator_shortcode'));
		add_shortcode('nwm_tools_hub', array($this, 'render_tools_hub_shortcode'));
	}

	/**
	 * Render ad slot shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function render_ad_slot_shortcode($atts) {
		$atts     = shortcode_atts(
			array(
				'slot' => 'sidebar',
			),
			$atts,
			'nwm_ad_slot'
		);
		$settings = get_option(NerdyWithMe_Tools_Admin::OPTION_KEY, array());
		$ads      = $settings['ads'] ?? array();
		$behaviors = $settings['ad_settings'] ?? array();
		$slot     = sanitize_key($atts['slot']);
		$content  = $ads[ $slot ] ?? '';

		if (! $content) {
			return '';
		}

		$slot_settings = wp_parse_args(
			isset($behaviors[ $slot ]) && is_array($behaviors[ $slot ]) ? $behaviors[ $slot ] : array(),
			array(
				'sticky'      => false,
				'hide_mobile' => false,
				'style'       => 'standard',
				'width'       => 'standard',
				'width_desktop' => 'standard',
				'width_tablet'  => 'standard',
				'width_mobile'  => 'full',
				'align'         => 'left',
			)
		);

		$classes = array(
			'nwm-ad-slot',
			'nwm-ad-slot--' . sanitize_html_class($slot),
			'nwm-ad-slot--style-' . sanitize_html_class($slot_settings['style']),
			'nwm-ad-slot--width-desktop-' . sanitize_html_class($slot_settings['width_desktop'] ?: $slot_settings['width']),
			'nwm-ad-slot--width-tablet-' . sanitize_html_class($slot_settings['width_tablet']),
			'nwm-ad-slot--width-mobile-' . sanitize_html_class($slot_settings['width_mobile']),
			'nwm-ad-slot--align-' . sanitize_html_class($slot_settings['align']),
		);

		if (! empty($slot_settings['sticky'])) {
			$classes[] = 'nwm-ad-slot--sticky';
		}

		if (! empty($slot_settings['hide_mobile'])) {
			$classes[] = 'nwm-ad-slot--hide-mobile';
		}

		return '<div class="' . esc_attr(implode(' ', $classes)) . '">' . do_shortcode(wp_kses_post($content)) . '</div>';
	}

	/**
	 * Render risk calculator.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_risk_calculator_shortcode($atts) {
		$settings = get_option(NerdyWithMe_Tools_Admin::OPTION_KEY, array());

		if (isset($settings['enable_risk_calculator']) && ! $settings['enable_risk_calculator']) {
			return '';
		}

		return $this->render_risk_calculator_inner();
	}

	/**
	 * Render tools hub shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_tools_hub_shortcode($atts) {
		$tools = $this->get_tool_registry();

		if (empty($tools)) {
			return '';
		}

		$requested_tool = sanitize_key((string) get_query_var('nwm_tool'));
		$active_tool    = isset($tools[ $requested_tool ]) ? $requested_tool : array_key_first($tools);

		ob_start();
		?>
		<div class="nwm-tools-hub" data-nwm-tools-hub>
			<nav class="nwm-tools-hub__nav" aria-label="<?php esc_attr_e('Trading tools', 'nerdywithme-tools'); ?>">
				<div class="nwm-tools-hub__nav-header">
					<h2><?php esc_html_e('Trading Tools', 'nerdywithme-tools'); ?></h2>
					<p><?php esc_html_e('Switch between calculators without leaving the page.', 'nerdywithme-tools'); ?></p>
				</div>
				<div class="nwm-tools-hub__menu">
					<?php foreach ($tools as $tool_id => $tool) : ?>
						<a
							class="nwm-tools-hub__tab<?php echo $tool_id === $active_tool ? ' is-active' : ''; ?>"
							href="<?php echo esc_url($this->get_tool_url($tool_id)); ?>"
							data-nwm-tool-tab="<?php echo esc_attr($tool_id); ?>"
							title="<?php echo esc_attr($tool['description']); ?>"
							aria-current="<?php echo $tool_id === $active_tool ? 'page' : 'false'; ?>"
						>
							<span class="nwm-tools-hub__tab-label"><?php echo esc_html($tool['label']); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			</nav>
			<div class="nwm-tools-hub__panes">
				<?php foreach ($tools as $tool_id => $tool) : ?>
					<section
						class="nwm-tools-hub__pane<?php echo $tool_id === $active_tool ? ' is-active' : ''; ?>"
						data-nwm-tool-pane="<?php echo esc_attr($tool_id); ?>"
						<?php echo $tool_id === $active_tool ? '' : 'hidden'; ?>
					>
						<div class="nwm-tools-hub__pane-header">
							<h3><?php echo esc_html($tool['label']); ?></h3>
							<p><?php echo esc_html($tool['description']); ?></p>
						</div>
						<div class="nwm-tools-hub__pane-body">
							<?php
							if (is_callable($tool['render'])) {
								echo call_user_func($tool['render']);
							}
							?>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Get public URL for a tool within the tools hub.
	 *
	 * @param string $tool_id Tool id.
	 * @return string
	 */
	public function get_tool_url($tool_id) {
		$tools_page = get_page_by_path('tools');
		$base_url   = $tools_page ? get_permalink($tools_page) : home_url('/tools/');

		return trailingslashit(trailingslashit($base_url) . sanitize_title($tool_id));
	}

	/**
	 * Tool registry.
	 *
	 * @return array
	 */
	public function get_tool_registry() {
		$settings      = get_option(NerdyWithMe_Tools_Admin::OPTION_KEY, array());
		$descriptions  = $settings['tool_descriptions'] ?? array();
		$summaries     = $settings['tool_summaries'] ?? array();
		$meta_titles   = $settings['tool_meta_titles'] ?? array();
		$tool_order    = array_filter(array_map('sanitize_key', array_map('trim', explode(',', (string) ($settings['tool_order'] ?? '')))));
		$registry = array(
			'risk-calculator' => array(
				'label'       => __('Risk Calculator', 'nerdywithme-tools'),
				'description' => $descriptions['risk-calculator'] ?? __('Risk, size, and reward planning.', 'nerdywithme-tools'),
				'summary'     => $summaries['risk-calculator'] ?? __('Plan safer trades by calculating how much capital to risk, how far your stop sits from entry, and what position size fits your rules before you execute.', 'nerdywithme-tools'),
				'meta_title'  => $meta_titles['risk-calculator'] ?? __('Risk Calculator for Trade Sizing | NerdyWithMe Tools', 'nerdywithme-tools'),
				'enabled'     => ! isset($settings['enable_risk_calculator']) || ! empty($settings['enable_risk_calculator']),
				'render'      => array($this, 'render_risk_calculator_inner'),
			),
			'position-size' => array(
				'label'       => __('Position Size', 'nerdywithme-tools'),
				'description' => $descriptions['position-size'] ?? __('Size a trade from risk amount and stop distance.', 'nerdywithme-tools'),
				'summary'     => $summaries['position-size'] ?? __('Convert a fixed risk amount and stop distance into a cleaner lot size so you stop guessing your exposure on every trade idea.', 'nerdywithme-tools'),
				'meta_title'  => $meta_titles['position-size'] ?? __('Position Size Calculator | NerdyWithMe Tools', 'nerdywithme-tools'),
				'enabled'     => ! isset($settings['enable_position_size_calculator']) || ! empty($settings['enable_position_size_calculator']),
				'render'      => array($this, 'render_position_size_calculator_inner'),
			),
			'pip-value' => array(
				'label'       => __('Pip / Point Value', 'nerdywithme-tools'),
				'description' => $descriptions['pip-value'] ?? __('Estimate pip or point value from lot size.', 'nerdywithme-tools'),
				'summary'     => $summaries['pip-value'] ?? __('Estimate what each pip or point is worth for your position size, then project the value of a move before you place the trade.', 'nerdywithme-tools'),
				'meta_title'  => $meta_titles['pip-value'] ?? __('Pip and Point Value Calculator | NerdyWithMe Tools', 'nerdywithme-tools'),
				'enabled'     => ! isset($settings['enable_pip_value_calculator']) || ! empty($settings['enable_pip_value_calculator']),
				'render'      => array($this, 'render_pip_value_calculator_inner'),
			),
			'profit-target' => array(
				'label'       => __('Profit Target', 'nerdywithme-tools'),
				'description' => $descriptions['profit-target'] ?? __('Target and payoff planner.', 'nerdywithme-tools'),
				'summary'     => $summaries['profit-target'] ?? __('Map out the payoff at your target, compare reward to risk, and see the percentage growth a winning trade could add to your account.', 'nerdywithme-tools'),
				'meta_title'  => $meta_titles['profit-target'] ?? __('Profit Target Calculator | NerdyWithMe Tools', 'nerdywithme-tools'),
				'enabled'     => ! isset($settings['enable_profit_target_calculator']) || ! empty($settings['enable_profit_target_calculator']),
				'render'      => array($this, 'render_profit_target_calculator_inner'),
			),
			'compound-growth' => array(
				'label'       => __('Compound Growth', 'nerdywithme-tools'),
				'description' => $descriptions['compound-growth'] ?? __('Project compounded account growth over time.', 'nerdywithme-tools'),
				'summary'     => $summaries['compound-growth'] ?? __('Project how your account could grow over time with compounded returns, recurring contributions, and a period you choose.', 'nerdywithme-tools'),
				'meta_title'  => $meta_titles['compound-growth'] ?? __('Compound Growth Calculator | NerdyWithMe Tools', 'nerdywithme-tools'),
				'enabled'     => ! isset($settings['enable_compound_growth_calculator']) || ! empty($settings['enable_compound_growth_calculator']),
				'render'      => array($this, 'render_compound_growth_calculator_inner'),
			),
		);

		$registry = array_filter(
			$registry,
			function ($tool) {
				return ! empty($tool['enabled']);
			}
		);

		if (! empty($tool_order)) {
			$ordered_registry = array();

			foreach ($tool_order as $tool_id) {
				if (isset($registry[ $tool_id ])) {
					$ordered_registry[ $tool_id ] = $registry[ $tool_id ];
				}
			}

			foreach ($registry as $tool_id => $tool) {
				if (! isset($ordered_registry[ $tool_id ])) {
					$ordered_registry[ $tool_id ] = $tool;
				}
			}

			return $ordered_registry;
		}

		return $registry;
	}

	/**
	 * Get active tool data from query var.
	 *
	 * @return array|null
	 */
	public function get_active_tool_data() {
		$tools          = $this->get_tool_registry();
		$requested_tool = sanitize_key((string) get_query_var('nwm_tool'));

		if ($requested_tool && isset($tools[ $requested_tool ])) {
			return array_merge(
				$tools[ $requested_tool ],
				array(
					'id'         => $requested_tool,
					'url'        => $this->get_tool_url($requested_tool),
					'summary'    => $tools[ $requested_tool ]['summary'] ?? '',
					'meta_title' => $tools[ $requested_tool ]['meta_title'] ?? '',
				)
			);
		}

		return null;
	}

	/**
	 * Inner risk calculator renderer.
	 *
	 * @return string
	 */
	private function render_risk_calculator_inner() {
		ob_start();
		?>
		<div class="nwm-tool-card nwm-tool-card--risk" data-nwm-risk-calculator>
			<div class="nwm-tool-card__header">
				<h3><?php esc_html_e('Risk Calculator', 'nerdywithme-tools'); ?></h3>
				<p><?php esc_html_e('Work out how much to risk, your stop distance, suggested position size, and the reward at your target.', 'nerdywithme-tools'); ?></p>
			</div>
			<div class="nwm-tool-grid">
				<label>
					<span><?php esc_html_e('Account Balance', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0" inputmode="decimal" data-nwm-numeric data-nwm-balance placeholder="1000" value="1000">
				</label>
				<label>
					<span><?php esc_html_e('Risk %', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0" inputmode="decimal" data-nwm-numeric data-nwm-risk placeholder="1" value="1">
				</label>
				<label>
					<span><?php esc_html_e('Entry Price', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.00001" min="0" inputmode="decimal" data-nwm-numeric data-nwm-entry placeholder="1.25000" value="1.25000">
				</label>
				<label>
					<span><?php esc_html_e('Stop Loss Price', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.00001" min="0" inputmode="decimal" data-nwm-numeric data-nwm-stop placeholder="1.24500" value="1.24500">
				</label>
				<label>
					<span><?php esc_html_e('Take Profit Price', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.00001" min="0" inputmode="decimal" data-nwm-numeric data-nwm-target placeholder="1.26000" value="1.26000">
				</label>
				<label>
					<span><?php esc_html_e('Value Per Point / Pip', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0.01" inputmode="decimal" data-nwm-numeric data-nwm-point-value placeholder="10" value="10">
				</label>
			</div>
			<div class="nwm-tool-results">
				<div>
					<strong><?php esc_html_e('Risk Amount', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-risk-amount>$0.00</span>
				</div>
				<div>
					<strong><?php esc_html_e('Stop Distance', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-stop-distance>0.00</span>
				</div>
				<div>
					<strong><?php esc_html_e('Suggested Position Size', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-position-size>0.00 lots</span>
				</div>
				<div>
					<strong><?php esc_html_e('Reward At Target', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-reward-amount>$0.00</span>
				</div>
				<div>
					<strong><?php esc_html_e('Risk / Reward', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-rr>0.00R</span>
				</div>
				<div>
					<strong><?php esc_html_e('Risk Budget Per Point', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-risk-per-point>$0.00</span>
				</div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Position size calculator renderer.
	 *
	 * @return string
	 */
	private function render_position_size_calculator_inner() {
		ob_start();
		?>
		<div class="nwm-tool-card nwm-tool-card--position" data-nwm-position-calculator>
			<div class="nwm-tool-card__header">
				<h3><?php esc_html_e('Position Size Calculator', 'nerdywithme-tools'); ?></h3>
				<p><?php esc_html_e('Start with the exact dollar amount you want to risk, then calculate a suggested lot size and value per point budget.', 'nerdywithme-tools'); ?></p>
			</div>
			<div class="nwm-tool-grid">
				<label>
					<span><?php esc_html_e('Risk Amount', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0" inputmode="decimal" data-nwm-numeric data-nwm-position-risk placeholder="50" value="50">
				</label>
				<label>
					<span><?php esc_html_e('Stop Distance', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.00001" min="0.00001" inputmode="decimal" data-nwm-numeric data-nwm-position-stop placeholder="0.00500" value="0.00500">
				</label>
				<label>
					<span><?php esc_html_e('Value Per Point / Pip', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0.01" inputmode="decimal" data-nwm-numeric data-nwm-position-point-value placeholder="10" value="10">
				</label>
			</div>
			<div class="nwm-tool-results">
				<div>
					<strong><?php esc_html_e('Suggested Position Size', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-position-lots>0.00 lots</span>
				</div>
				<div>
					<strong><?php esc_html_e('Risk Budget Per Point', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-position-per-point>$0.00</span>
				</div>
				<div>
					<strong><?php esc_html_e('Position Value At Stop', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-position-stop-value>$0.00</span>
				</div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Pip value calculator renderer.
	 *
	 * @return string
	 */
	private function render_pip_value_calculator_inner() {
		ob_start();
		?>
		<div class="nwm-tool-card nwm-tool-card--pip" data-nwm-pip-calculator>
			<div class="nwm-tool-card__header">
				<h3><?php esc_html_e('Pip / Point Value Calculator', 'nerdywithme-tools'); ?></h3>
				<p><?php esc_html_e('Estimate the value of each pip or point for your current lot size, then project the total move value over a distance.', 'nerdywithme-tools'); ?></p>
			</div>
			<div class="nwm-tool-grid">
				<label>
					<span><?php esc_html_e('Lot Size', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0.01" inputmode="decimal" data-nwm-numeric data-nwm-pip-lots placeholder="1.00" value="1.00">
				</label>
				<label>
					<span><?php esc_html_e('Value Per Pip / Point (1 Lot)', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0.01" inputmode="decimal" data-nwm-numeric data-nwm-pip-base-value placeholder="10" value="10">
				</label>
				<label>
					<span><?php esc_html_e('Move Distance', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0" inputmode="decimal" data-nwm-numeric data-nwm-pip-distance placeholder="25" value="25">
				</label>
			</div>
			<div class="nwm-tool-results">
				<div>
					<strong><?php esc_html_e('Value Per Pip / Point', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-pip-value>$0.00</span>
				</div>
				<div>
					<strong><?php esc_html_e('Total Move Value', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-pip-total>$0.00</span>
				</div>
				<div>
					<strong><?php esc_html_e('Mini Lot Equivalent', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-pip-mini>0.00 mini lots</span>
				</div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Profit target calculator renderer.
	 *
	 * @return string
	 */
	private function render_profit_target_calculator_inner() {
		ob_start();
		?>
		<div class="nwm-tool-card nwm-tool-card--profit" data-nwm-profit-calculator>
			<div class="nwm-tool-card__header">
				<h3><?php esc_html_e('Profit Target Calculator', 'nerdywithme-tools'); ?></h3>
				<p><?php esc_html_e('Plan the payout at a target price, the reward multiple, and the percentage growth on your account.', 'nerdywithme-tools'); ?></p>
			</div>
			<div class="nwm-tool-grid">
				<label>
					<span><?php esc_html_e('Account Balance', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0" inputmode="decimal" data-nwm-numeric data-nwm-profit-balance placeholder="1000" value="1000">
				</label>
				<label>
					<span><?php esc_html_e('Entry Price', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.00001" min="0" inputmode="decimal" data-nwm-numeric data-nwm-profit-entry placeholder="1.25000" value="1.25000">
				</label>
				<label>
					<span><?php esc_html_e('Take Profit Price', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.00001" min="0" inputmode="decimal" data-nwm-numeric data-nwm-profit-target placeholder="1.26500" value="1.26500">
				</label>
				<label>
					<span><?php esc_html_e('Stop Loss Price', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.00001" min="0" inputmode="decimal" data-nwm-numeric data-nwm-profit-stop placeholder="1.24500" value="1.24500">
				</label>
				<label>
					<span><?php esc_html_e('Lot Size', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0.01" inputmode="decimal" data-nwm-numeric data-nwm-profit-lots placeholder="1.00" value="1.00">
				</label>
				<label>
					<span><?php esc_html_e('Value Per Pip / Point (1 Lot)', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0.01" inputmode="decimal" data-nwm-numeric data-nwm-profit-point-value placeholder="10" value="10">
				</label>
			</div>
			<div class="nwm-tool-results">
				<div>
					<strong><?php esc_html_e('Target Distance', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-profit-distance>0.00000</span>
				</div>
				<div>
					<strong><?php esc_html_e('Profit At Target', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-profit-amount>$0.00</span>
				</div>
				<div>
					<strong><?php esc_html_e('Reward / Risk', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-profit-rr>0.00R</span>
				</div>
				<div>
					<strong><?php esc_html_e('Account Growth', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-profit-growth>0.00%</span>
				</div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Compound growth calculator renderer.
	 *
	 * @return string
	 */
	private function render_compound_growth_calculator_inner() {
		ob_start();
		?>
		<div class="nwm-tool-card nwm-tool-card--compound" data-nwm-compound-calculator>
			<div class="nwm-tool-card__header">
				<h3><?php esc_html_e('Compound Growth Calculator', 'nerdywithme-tools'); ?></h3>
				<p><?php esc_html_e('Estimate how your capital could grow over a chosen period with compounded returns and optional recurring contributions.', 'nerdywithme-tools'); ?></p>
			</div>
			<div class="nwm-tool-presets" aria-label="<?php esc_attr_e('Compound growth presets', 'nerdywithme-tools'); ?>">
				<button type="button" class="nwm-tool-presets__button" data-nwm-compound-preset='{"principal":"1000","contribution":"100","rate":"12","years":"5","frequency":"12"}'><?php esc_html_e('Steady Monthly', 'nerdywithme-tools'); ?></button>
				<button type="button" class="nwm-tool-presets__button" data-nwm-compound-preset='{"principal":"2500","contribution":"150","rate":"18","years":"3","frequency":"52"}'><?php esc_html_e('Weekly Growth', 'nerdywithme-tools'); ?></button>
				<button type="button" class="nwm-tool-presets__button" data-nwm-compound-preset='{"principal":"5000","contribution":"250","rate":"24","years":"4","frequency":"12"}'><?php esc_html_e('Aggressive Plan', 'nerdywithme-tools'); ?></button>
			</div>
			<div class="nwm-tool-grid">
				<label>
					<span><?php esc_html_e('Starting Capital', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0" inputmode="decimal" data-nwm-numeric data-nwm-compound-principal placeholder="1000" value="1000">
				</label>
				<label>
					<span><?php esc_html_e('Contribution Per Period', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0" inputmode="decimal" data-nwm-numeric data-nwm-compound-contribution placeholder="100" value="100">
				</label>
				<label>
					<span><?php esc_html_e('Annual Return %', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.01" min="0" inputmode="decimal" data-nwm-numeric data-nwm-compound-rate placeholder="12" value="12">
				</label>
				<label>
					<span><?php esc_html_e('Years', 'nerdywithme-tools'); ?></span>
					<input type="number" step="0.1" min="0" inputmode="decimal" data-nwm-numeric data-nwm-compound-years placeholder="5" value="5">
				</label>
				<label>
					<span><?php esc_html_e('Compounds Per Year', 'nerdywithme-tools'); ?></span>
					<input type="number" step="1" min="1" inputmode="numeric" data-nwm-numeric data-nwm-compound-frequency placeholder="12" value="12">
				</label>
			</div>
			<div class="nwm-tool-results">
				<div>
					<strong><?php esc_html_e('Ending Balance', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-compound-ending>$0.00</span>
				</div>
				<div>
					<strong><?php esc_html_e('Total Contributions', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-compound-contributions>$0.00</span>
				</div>
				<div>
					<strong><?php esc_html_e('Interest Earned', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-compound-interest>$0.00</span>
				</div>
				<div>
					<strong><?php esc_html_e('Growth Multiple', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-compound-multiple>0.00x</span>
				</div>
				<div>
					<strong><?php esc_html_e('Net Profit', 'nerdywithme-tools'); ?></strong>
					<span data-nwm-compound-profit>$0.00</span>
				</div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Simple placeholder tool renderer.
	 *
	 * @return string
	 */
	private function render_coming_soon_tool() {
		ob_start();
		?>
		<div class="nwm-tool-card nwm-tool-card--placeholder">
			<div class="nwm-tool-card__header">
				<h3><?php esc_html_e('Coming Soon', 'nerdywithme-tools'); ?></h3>
				<p><?php esc_html_e('This tool slot is ready. You can add the real calculator here next without changing the page structure.', 'nerdywithme-tools'); ?></p>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}
}

/**
 * Public helper so the theme can render ad slots safely later.
 *
 * @param string $slot Slot key.
 * @return void
 */
function nerdywithme_tools_render_ad_slot($slot) {
	echo do_shortcode('[nwm_ad_slot slot="' . sanitize_key($slot) . '"]');
}

/**
 * Get the active tool data for the current tools URL.
 *
 * @return array|null
 */
function nerdywithme_tools_get_active_tool_data() {
	return nerdywithme_tools()->get_active_tool_context();
}
