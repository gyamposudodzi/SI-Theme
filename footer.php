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
<?php wp_footer(); ?>
</body>
</html>
