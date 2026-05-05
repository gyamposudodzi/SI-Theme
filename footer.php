<?php
/**
 * Site footer.
 *
 * @package NerdyWithMe
 */
?>
		</div>
	</main>
	<footer class="site-footer nwm-deferred-render">
		<div class="nwm-shell">
			<?php if (function_exists('nerdywithme_tools_render_ad_slot')) : ?>
				<div class="theme-ad-slot theme-ad-slot--footer">
					<?php nerdywithme_tools_render_ad_slot('footer'); ?>
				</div>
			<?php endif; ?>
			<div class="footer-brand">
				<?php nerdywithme_branding(false); ?>
				<p class="section-intro"><?php echo esc_html(nerdywithme_get_option('footer_blurb', __('Where trading meets technology: practical market education, automation, AI, and tools for modern traders.', 'nerdywithme'))); ?></p>
			</div>
			<div class="footer-links">
				<div>
					<h2><?php esc_html_e('Core Pages', 'nerdywithme'); ?></h2>
					<?php if (has_nav_menu('footer_col_1')) : ?>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer_col_1',
								'container'      => false,
								'menu_class'     => 'footer-menu',
								'depth'          => 1,
							)
						);
						?>
					<?php else : ?>
						<ul class="footer-menu">
							<li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'nerdywithme'); ?></a></li>
							<li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php esc_html_e('About', 'nerdywithme'); ?></a></li>
							<li><a href="<?php echo esc_url(home_url('/blog')); ?>"><?php esc_html_e('Blog', 'nerdywithme'); ?></a></li>
						</ul>
					<?php endif; ?>
				</div>
				<div>
					<h2><?php esc_html_e('Categories', 'nerdywithme'); ?></h2>
					<?php if (has_nav_menu('footer_col_2')) : ?>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer_col_2',
								'container'      => false,
								'menu_class'     => 'footer-menu',
								'depth'          => 1,
							)
						);
						?>
					<?php else : ?>
						<ul class="footer-menu">
							<?php foreach (nerdywithme_get_primary_categories() as $category) : ?>
								<li><a href="<?php echo esc_url(get_category_link($category)); ?>"><?php echo esc_html($category->name); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				<div>
					<h2><?php esc_html_e('Build Stack', 'nerdywithme'); ?></h2>
					<?php if (has_nav_menu('footer_col_3')) : ?>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer_col_3',
								'container'      => false,
								'menu_class'     => 'footer-menu',
								'depth'          => 1,
							)
						);
						?>
					<?php else : ?>
						<ul class="footer-menu">
							<li><a href="<?php echo esc_url(home_url('/tools')); ?>"><?php esc_html_e('Tools', 'nerdywithme'); ?></a></li>
							<li><a href="<?php echo esc_url(home_url('/projects')); ?>"><?php esc_html_e('Projects', 'nerdywithme'); ?></a></li>
							<li><a href="<?php echo esc_url(home_url('/resources')); ?>"><?php esc_html_e('Resources', 'nerdywithme'); ?></a></li>
						</ul>
					<?php endif; ?>
				</div>
				<div>
					<h2><?php esc_html_e('Start Here', 'nerdywithme'); ?></h2>
					<?php if (has_nav_menu('footer_col_4')) : ?>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer_col_4',
								'container'      => false,
								'menu_class'     => 'footer-menu',
								'depth'          => 1,
							)
						);
						?>
					<?php else : ?>
						<ul class="footer-menu">
							<li><a href="<?php echo esc_url(home_url('/category/trading-foundations')); ?>"><?php esc_html_e('Trading Foundations', 'nerdywithme'); ?></a></li>
							<li><a href="<?php echo esc_url(home_url('/category/tech-for-traders')); ?>"><?php esc_html_e('Tech for Traders', 'nerdywithme'); ?></a></li>
							<li><a href="<?php echo esc_url(home_url('/category/ai-quant-trading')); ?>"><?php esc_html_e('AI & Quant Trading', 'nerdywithme'); ?></a></li>
						</ul>
					<?php endif; ?>
				</div>
				<div>
					<h2><?php esc_html_e('Follow Me', 'nerdywithme'); ?></h2>
					<?php nerdywithme_render_social_links(); ?>
				</div>
			</div>
			<div class="site-info">
				<?php if (has_nav_menu('footer_legal')) : ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer_legal',
							'container'      => false,
							'menu_class'     => 'site-info__menu',
							'depth'          => 1,
						)
					);
					?>
				<?php else : ?>
					<ul class="site-info__menu">
						<li><a href="#"><?php esc_html_e('Privacy Policy', 'nerdywithme'); ?></a></li>
						<li><a href="#"><?php esc_html_e('Terms of Use', 'nerdywithme'); ?></a></li>
					</ul>
				<?php endif; ?>
				<div>&copy; <?php echo esc_html(wp_date('Y')); ?> <?php bloginfo('name'); ?></div>
			</div>
		</div>
	</footer>
</div>
<?php if (function_exists('nerdywithme_cookie_banner_enabled') && nerdywithme_cookie_banner_enabled()) : ?>
	<?php
		$privacy_url = function_exists('get_privacy_policy_url') ? get_privacy_policy_url() : '';
		$cookie_defaults = array(
			'banner_title'           => __('Cookies on NerdyWithMe', 'nerdywithme'),
			'banner_text'            => __('We use essential cookies to keep the site working and optional cookies to understand what readers find useful.', 'nerdywithme'),
			'details_label'          => __('Cookie details', 'nerdywithme'),
			'necessary_label'        => __('Necessary only', 'nerdywithme'),
			'accept_label'           => __('Accept all', 'nerdywithme'),
			'fab_label'              => __('Cookies', 'nerdywithme'),
			'modal_eyebrow'          => __('Cookie preferences', 'nerdywithme'),
			'modal_title'            => __('How NerdyWithMe uses cookies', 'nerdywithme'),
			'status_label'           => __('Current choice', 'nerdywithme'),
			'summary_text'           => __('This panel explains what the site stores in your browser today and what may be stored when optional features are enabled later.', 'nerdywithme'),
			'essential_title'        => __('Essential cookies', 'nerdywithme'),
			'essential_body'         => __('These keep core parts of the site working, such as your cookie choice and standard WordPress behaviour when you interact with forms or comments.', 'nerdywithme'),
			'optional_title'         => __('Optional cookies', 'nerdywithme'),
			'optional_body'          => __('Optional storage is there for convenience and future measurement features. It is not required to browse the site.', 'nerdywithme'),
			'glance_title'           => __('Storage at a glance', 'nerdywithme'),
			'glance_type_label'      => __('Type', 'nerdywithme'),
			'glance_type_value'      => __('Cookie and local storage', 'nerdywithme'),
			'glance_retention_label' => __('Retention', 'nerdywithme'),
			'glance_retention_value' => __('Consent is currently kept for 180 days unless you clear your browser data.', 'nerdywithme'),
			'glance_control_label'   => __('Control', 'nerdywithme'),
			'glance_control_value'   => __('You can return to this panel any time using the cookie button at the lower left.', 'nerdywithme'),
			'preferences_title'      => __('Choose what you allow', 'nerdywithme'),
			'pref_essential_title'   => __('Essential cookies', 'nerdywithme'),
			'pref_essential_body'    => __('Required for core site behaviour and always on.', 'nerdywithme'),
			'pref_optional_title'    => __('Optional cookies', 'nerdywithme'),
			'pref_optional_body'     => __('Allow convenience storage and future measurement features.', 'nerdywithme'),
			'privacy_label'          => __('Read the full privacy policy', 'nerdywithme'),
			'save_preferences_label' => __('Save preferences', 'nerdywithme'),
			'reset_choice_label'     => __('Reset choice', 'nerdywithme'),
		);
		$essential_items = nerdywithme_cookie_copy_lines(
			'essential_items',
			array(
				__('nwm_cookie_consent: remembers whether you chose necessary-only or accepted optional cookies.', 'nerdywithme'),
				__('WordPress comment cookies: may remember your name, email, and website if you ask the comment form to save them.', 'nerdywithme'),
			)
		);
		$optional_items = nerdywithme_cookie_copy_lines(
			'optional_items',
			array(
				__('nwmActiveTool: remembers the last calculator tab you used in the tools area so you can pick up where you left off.', 'nerdywithme'),
				__('Future analytics or ad measurement tools should only run after optional consent is accepted.', 'nerdywithme'),
			)
		);
	?>
	<div class="nwm-cookie-banner" data-cookie-banner hidden aria-hidden="true">
		<div class="nwm-cookie-banner__inner">
			<div class="nwm-cookie-banner__copy">
				<strong><?php echo esc_html(nerdywithme_get_cookie_copy('banner_title', $cookie_defaults['banner_title'])); ?></strong>
				<p><?php echo esc_html(nerdywithme_get_cookie_copy('banner_text', $cookie_defaults['banner_text'])); ?></p>
			</div>
			<div class="nwm-cookie-banner__actions">
				<?php if (! empty($privacy_url)) : ?>
					<a class="nwm-cookie-banner__link" href="<?php echo esc_url($privacy_url); ?>"><?php esc_html_e('Privacy policy', 'nerdywithme'); ?></a>
				<?php endif; ?>
				<button type="button" class="nwm-cookie-banner__link nwm-cookie-banner__link--details" data-cookie-modal-open><?php echo esc_html(nerdywithme_get_cookie_copy('details_label', $cookie_defaults['details_label'])); ?></button>
				<button type="button" class="nwm-cookie-banner__button nwm-cookie-banner__button--ghost" data-cookie-consent="necessary"><?php echo esc_html(nerdywithme_get_cookie_copy('necessary_label', $cookie_defaults['necessary_label'])); ?></button>
				<button type="button" class="nwm-cookie-banner__button" data-cookie-consent="accepted"><?php echo esc_html(nerdywithme_get_cookie_copy('accept_label', $cookie_defaults['accept_label'])); ?></button>
			</div>
		</div>
	</div>
	<button type="button" class="nwm-cookie-fab" data-cookie-modal-open aria-haspopup="dialog" aria-controls="nwm-cookie-modal" aria-label="<?php esc_attr_e('Open cookie preferences', 'nerdywithme'); ?>">
		<span class="nwm-cookie-fab__icon" aria-hidden="true"></span>
		<span class="nwm-cookie-fab__label"><?php echo esc_html(nerdywithme_get_cookie_copy('fab_label', $cookie_defaults['fab_label'])); ?></span>
	</button>
	<div id="nwm-cookie-modal" class="nwm-cookie-modal" data-cookie-modal aria-hidden="true" hidden>
		<div class="nwm-cookie-modal__backdrop" data-cookie-modal-close></div>
		<div class="nwm-cookie-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="nwm-cookie-modal-title">
			<div class="nwm-cookie-modal__header">
				<div>
					<p class="nwm-cookie-modal__eyebrow"><?php echo esc_html(nerdywithme_get_cookie_copy('modal_eyebrow', $cookie_defaults['modal_eyebrow'])); ?></p>
					<h2 id="nwm-cookie-modal-title"><?php echo esc_html(nerdywithme_get_cookie_copy('modal_title', $cookie_defaults['modal_title'])); ?></h2>
				</div>
				<button type="button" class="nwm-cookie-modal__close" data-cookie-modal-close aria-label="<?php esc_attr_e('Close cookie preferences', 'nerdywithme'); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="nwm-cookie-modal__content">
				<div class="nwm-cookie-modal__summary">
					<div class="nwm-cookie-modal__status">
						<span class="nwm-cookie-modal__status-label"><?php echo esc_html(nerdywithme_get_cookie_copy('status_label', $cookie_defaults['status_label'])); ?></span>
						<strong class="nwm-cookie-modal__status-value" data-cookie-status><?php esc_html_e('Not set yet', 'nerdywithme'); ?></strong>
					</div>
					<p><?php echo esc_html(nerdywithme_get_cookie_copy('summary_text', $cookie_defaults['summary_text'])); ?></p>
				</div>
				<div class="nwm-cookie-modal__section">
					<h3><?php echo esc_html(nerdywithme_get_cookie_copy('essential_title', $cookie_defaults['essential_title'])); ?></h3>
					<p><?php echo esc_html(nerdywithme_get_cookie_copy('essential_body', $cookie_defaults['essential_body'])); ?></p>
					<ul class="nwm-cookie-modal__list">
						<?php foreach ($essential_items as $item) : ?>
							<li><?php echo esc_html($item); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="nwm-cookie-modal__section">
					<h3><?php echo esc_html(nerdywithme_get_cookie_copy('optional_title', $cookie_defaults['optional_title'])); ?></h3>
					<p><?php echo esc_html(nerdywithme_get_cookie_copy('optional_body', $cookie_defaults['optional_body'])); ?></p>
					<ul class="nwm-cookie-modal__list">
						<?php foreach ($optional_items as $item) : ?>
							<li><?php echo esc_html($item); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="nwm-cookie-modal__section">
					<h3><?php echo esc_html(nerdywithme_get_cookie_copy('glance_title', $cookie_defaults['glance_title'])); ?></h3>
					<div class="nwm-cookie-modal__grid">
						<div class="nwm-cookie-modal__card">
							<strong><?php echo esc_html(nerdywithme_get_cookie_copy('glance_type_label', $cookie_defaults['glance_type_label'])); ?></strong>
							<p><?php echo esc_html(nerdywithme_get_cookie_copy('glance_type_value', $cookie_defaults['glance_type_value'])); ?></p>
						</div>
						<div class="nwm-cookie-modal__card">
							<strong><?php echo esc_html(nerdywithme_get_cookie_copy('glance_retention_label', $cookie_defaults['glance_retention_label'])); ?></strong>
							<p><?php echo esc_html(nerdywithme_get_cookie_copy('glance_retention_value', $cookie_defaults['glance_retention_value'])); ?></p>
						</div>
						<div class="nwm-cookie-modal__card">
							<strong><?php echo esc_html(nerdywithme_get_cookie_copy('glance_control_label', $cookie_defaults['glance_control_label'])); ?></strong>
							<p><?php echo esc_html(nerdywithme_get_cookie_copy('glance_control_value', $cookie_defaults['glance_control_value'])); ?></p>
						</div>
					</div>
				</div>
				<div class="nwm-cookie-modal__section">
					<h3><?php echo esc_html(nerdywithme_get_cookie_copy('preferences_title', $cookie_defaults['preferences_title'])); ?></h3>
					<div class="nwm-cookie-modal__prefs">
						<div class="nwm-cookie-pref">
							<div class="nwm-cookie-pref__copy">
								<strong><?php echo esc_html(nerdywithme_get_cookie_copy('pref_essential_title', $cookie_defaults['pref_essential_title'])); ?></strong>
								<p><?php echo esc_html(nerdywithme_get_cookie_copy('pref_essential_body', $cookie_defaults['pref_essential_body'])); ?></p>
							</div>
							<label class="nwm-cookie-toggle">
								<input type="checkbox" checked disabled>
								<span class="nwm-cookie-toggle__track"><span class="nwm-cookie-toggle__thumb"></span></span>
							</label>
						</div>
						<div class="nwm-cookie-pref">
							<div class="nwm-cookie-pref__copy">
								<strong><?php echo esc_html(nerdywithme_get_cookie_copy('pref_optional_title', $cookie_defaults['pref_optional_title'])); ?></strong>
								<p><?php echo esc_html(nerdywithme_get_cookie_copy('pref_optional_body', $cookie_defaults['pref_optional_body'])); ?></p>
							</div>
							<label class="nwm-cookie-toggle">
								<input type="checkbox" data-cookie-optional-toggle>
								<span class="nwm-cookie-toggle__track"><span class="nwm-cookie-toggle__thumb"></span></span>
							</label>
						</div>
					</div>
					<?php if (! empty($privacy_url)) : ?>
						<p><a href="<?php echo esc_url($privacy_url); ?>"><?php echo esc_html(nerdywithme_get_cookie_copy('privacy_label', $cookie_defaults['privacy_label'])); ?></a></p>
					<?php endif; ?>
				</div>
			</div>
			<div class="nwm-cookie-modal__actions">
				<button type="button" class="nwm-cookie-banner__button nwm-cookie-banner__button--ghost" data-cookie-save-preferences><?php echo esc_html(nerdywithme_get_cookie_copy('save_preferences_label', $cookie_defaults['save_preferences_label'])); ?></button>
				<button type="button" class="nwm-cookie-banner__link nwm-cookie-banner__link--details" data-cookie-reset><?php echo esc_html(nerdywithme_get_cookie_copy('reset_choice_label', $cookie_defaults['reset_choice_label'])); ?></button>
				<button type="button" class="nwm-cookie-banner__button nwm-cookie-banner__button--ghost" data-cookie-consent="necessary"><?php echo esc_html(nerdywithme_get_cookie_copy('necessary_label', $cookie_defaults['necessary_label'])); ?></button>
				<button type="button" class="nwm-cookie-banner__button" data-cookie-consent="accepted"><?php echo esc_html(nerdywithme_get_cookie_copy('accept_label', $cookie_defaults['accept_label'])); ?></button>
			</div>
		</div>
	</div>
<?php endif; ?>
<button type="button" class="nwm-back-to-top" data-back-to-top aria-label="<?php esc_attr_e('Back to top', 'nerdywithme'); ?>" hidden>
	<span aria-hidden="true">&uarr;</span>
</button>
<?php wp_footer(); ?>
</body>
</html>
