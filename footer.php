<?php
/**
 * Site footer.
 *
 * @package NerdyWithMe
 */
?>
		</div>
	</main>
	<footer class="site-footer">
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
	?>
	<div class="nwm-cookie-banner" data-cookie-banner hidden aria-hidden="true">
		<div class="nwm-cookie-banner__inner">
			<div class="nwm-cookie-banner__copy">
				<strong><?php esc_html_e('Cookies on NerdyWithMe', 'nerdywithme'); ?></strong>
				<p><?php esc_html_e('We use essential cookies to keep the site working and optional cookies to understand what readers find useful.', 'nerdywithme'); ?></p>
			</div>
			<div class="nwm-cookie-banner__actions">
				<?php if (! empty($privacy_url)) : ?>
					<a class="nwm-cookie-banner__link" href="<?php echo esc_url($privacy_url); ?>"><?php esc_html_e('Privacy policy', 'nerdywithme'); ?></a>
				<?php endif; ?>
				<button type="button" class="nwm-cookie-banner__link nwm-cookie-banner__link--details" data-cookie-modal-open><?php esc_html_e('Cookie details', 'nerdywithme'); ?></button>
				<button type="button" class="nwm-cookie-banner__button nwm-cookie-banner__button--ghost" data-cookie-consent="necessary"><?php esc_html_e('Necessary only', 'nerdywithme'); ?></button>
				<button type="button" class="nwm-cookie-banner__button" data-cookie-consent="accepted"><?php esc_html_e('Accept all', 'nerdywithme'); ?></button>
			</div>
		</div>
	</div>
	<button type="button" class="nwm-cookie-fab" data-cookie-modal-open aria-haspopup="dialog" aria-controls="nwm-cookie-modal" aria-label="<?php esc_attr_e('Open cookie preferences', 'nerdywithme'); ?>">
		<span class="nwm-cookie-fab__icon" aria-hidden="true"></span>
		<span class="nwm-cookie-fab__label"><?php esc_html_e('Cookies', 'nerdywithme'); ?></span>
	</button>
	<div id="nwm-cookie-modal" class="nwm-cookie-modal" data-cookie-modal aria-hidden="true" hidden>
		<div class="nwm-cookie-modal__backdrop" data-cookie-modal-close></div>
		<div class="nwm-cookie-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="nwm-cookie-modal-title">
			<div class="nwm-cookie-modal__header">
				<div>
					<p class="nwm-cookie-modal__eyebrow"><?php esc_html_e('Cookie preferences', 'nerdywithme'); ?></p>
					<h2 id="nwm-cookie-modal-title"><?php esc_html_e('How NerdyWithMe uses cookies', 'nerdywithme'); ?></h2>
				</div>
				<button type="button" class="nwm-cookie-modal__close" data-cookie-modal-close aria-label="<?php esc_attr_e('Close cookie preferences', 'nerdywithme'); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="nwm-cookie-modal__content">
				<div class="nwm-cookie-modal__summary">
					<div class="nwm-cookie-modal__status">
						<span class="nwm-cookie-modal__status-label"><?php esc_html_e('Current choice', 'nerdywithme'); ?></span>
						<strong class="nwm-cookie-modal__status-value" data-cookie-status><?php esc_html_e('Not set yet', 'nerdywithme'); ?></strong>
					</div>
					<p><?php esc_html_e('This panel explains what the site stores in your browser today and what may be stored when optional features are enabled later.', 'nerdywithme'); ?></p>
				</div>
				<div class="nwm-cookie-modal__section">
					<h3><?php esc_html_e('Essential cookies', 'nerdywithme'); ?></h3>
					<p><?php esc_html_e('These keep core parts of the site working, such as your cookie choice and standard WordPress behaviour when you interact with forms or comments.', 'nerdywithme'); ?></p>
					<ul class="nwm-cookie-modal__list">
						<li><?php esc_html_e('nwm_cookie_consent: remembers whether you chose necessary-only or accepted optional cookies.', 'nerdywithme'); ?></li>
						<li><?php esc_html_e('WordPress comment cookies: may remember your name, email, and website if you ask the comment form to save them.', 'nerdywithme'); ?></li>
					</ul>
				</div>
				<div class="nwm-cookie-modal__section">
					<h3><?php esc_html_e('Optional cookies', 'nerdywithme'); ?></h3>
					<p><?php esc_html_e('Optional storage is there for convenience and future measurement features. It is not required to browse the site.', 'nerdywithme'); ?></p>
					<ul class="nwm-cookie-modal__list">
						<li><?php esc_html_e('nwmActiveTool: remembers the last calculator tab you used in the tools area so you can pick up where you left off.', 'nerdywithme'); ?></li>
						<li><?php esc_html_e('Future analytics or ad measurement tools should only run after optional consent is accepted.', 'nerdywithme'); ?></li>
					</ul>
				</div>
				<div class="nwm-cookie-modal__section">
					<h3><?php esc_html_e('Storage at a glance', 'nerdywithme'); ?></h3>
					<div class="nwm-cookie-modal__grid">
						<div class="nwm-cookie-modal__card">
							<strong><?php esc_html_e('Type', 'nerdywithme'); ?></strong>
							<p><?php esc_html_e('Cookie and local storage', 'nerdywithme'); ?></p>
						</div>
						<div class="nwm-cookie-modal__card">
							<strong><?php esc_html_e('Retention', 'nerdywithme'); ?></strong>
							<p><?php esc_html_e('Consent is currently kept for 180 days unless you clear your browser data.', 'nerdywithme'); ?></p>
						</div>
						<div class="nwm-cookie-modal__card">
							<strong><?php esc_html_e('Control', 'nerdywithme'); ?></strong>
							<p><?php esc_html_e('You can return to this panel any time using the cookie button at the lower left.', 'nerdywithme'); ?></p>
						</div>
					</div>
				</div>
				<div class="nwm-cookie-modal__section">
					<h3><?php esc_html_e('Choose what you allow', 'nerdywithme'); ?></h3>
					<div class="nwm-cookie-modal__prefs">
						<div class="nwm-cookie-pref">
							<div class="nwm-cookie-pref__copy">
								<strong><?php esc_html_e('Essential cookies', 'nerdywithme'); ?></strong>
								<p><?php esc_html_e('Required for core site behaviour and always on.', 'nerdywithme'); ?></p>
							</div>
							<label class="nwm-cookie-toggle">
								<input type="checkbox" checked disabled>
								<span class="nwm-cookie-toggle__track"><span class="nwm-cookie-toggle__thumb"></span></span>
							</label>
						</div>
						<div class="nwm-cookie-pref">
							<div class="nwm-cookie-pref__copy">
								<strong><?php esc_html_e('Optional cookies', 'nerdywithme'); ?></strong>
								<p><?php esc_html_e('Allow convenience storage and future measurement features.', 'nerdywithme'); ?></p>
							</div>
							<label class="nwm-cookie-toggle">
								<input type="checkbox" data-cookie-optional-toggle>
								<span class="nwm-cookie-toggle__track"><span class="nwm-cookie-toggle__thumb"></span></span>
							</label>
						</div>
					</div>
					<?php if (! empty($privacy_url)) : ?>
						<p><a href="<?php echo esc_url($privacy_url); ?>"><?php esc_html_e('Read the full privacy policy', 'nerdywithme'); ?></a></p>
					<?php endif; ?>
				</div>
			</div>
			<div class="nwm-cookie-modal__actions">
				<button type="button" class="nwm-cookie-banner__button nwm-cookie-banner__button--ghost" data-cookie-save-preferences><?php esc_html_e('Save preferences', 'nerdywithme'); ?></button>
				<button type="button" class="nwm-cookie-banner__link nwm-cookie-banner__link--details" data-cookie-reset><?php esc_html_e('Reset choice', 'nerdywithme'); ?></button>
				<button type="button" class="nwm-cookie-banner__button nwm-cookie-banner__button--ghost" data-cookie-consent="necessary"><?php esc_html_e('Necessary only', 'nerdywithme'); ?></button>
				<button type="button" class="nwm-cookie-banner__button" data-cookie-consent="accepted"><?php esc_html_e('Accept all', 'nerdywithme'); ?></button>
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
