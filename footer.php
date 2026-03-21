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
			<div class="footer-brand">
				<?php nerdywithme_branding(false); ?>
				<p class="section-intro"><?php echo esc_html(get_bloginfo('description') ? get_bloginfo('description') : __('Pop culture, blogging, games, stories, and everything delightfully nerdy.', 'nerdywithme')); ?></p>
			</div>
			<div class="footer-links">
				<div>
					<h3><?php esc_html_e('Homepages', 'nerdywithme'); ?></h3>
					<ul>
						<li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Front Page', 'nerdywithme'); ?></a></li>
						<li><a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/')); ?>"><?php esc_html_e('Latest Posts', 'nerdywithme'); ?></a></li>
						<li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Featured Stories', 'nerdywithme'); ?></a></li>
					</ul>
				</div>
				<div>
					<h3><?php esc_html_e('Categories', 'nerdywithme'); ?></h3>
					<ul>
						<?php foreach (nerdywithme_get_primary_categories() as $category) : ?>
							<li><a href="<?php echo esc_url(get_category_link($category)); ?>"><?php echo esc_html($category->name); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div>
					<h3><?php esc_html_e('Features', 'nerdywithme'); ?></h3>
					<ul>
						<li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Editor Picks', 'nerdywithme'); ?></a></li>
						<li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Popular Reads', 'nerdywithme'); ?></a></li>
						<li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Read Next', 'nerdywithme'); ?></a></li>
					</ul>
				</div>
				<div>
					<h3><?php esc_html_e('Pages', 'nerdywithme'); ?></h3>
					<ul>
						<li><a href="<?php echo esc_url(home_url('/404-preview')); ?>"><?php esc_html_e('404 Page', 'nerdywithme'); ?></a></li>
						<li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Archive Layout', 'nerdywithme'); ?></a></li>
						<li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Single Story', 'nerdywithme'); ?></a></li>
					</ul>
				</div>
				<div>
					<h3><?php esc_html_e('Follow Me', 'nerdywithme'); ?></h3>
					<div class="social-links">
						<a href="#" aria-label="<?php esc_attr_e('Facebook', 'nerdywithme'); ?>">f</a>
						<a href="#" aria-label="<?php esc_attr_e('X', 'nerdywithme'); ?>">x</a>
						<a href="#" aria-label="<?php esc_attr_e('Instagram', 'nerdywithme'); ?>">ig</a>
					</div>
				</div>
			</div>
			<div class="site-info">
				<ul>
					<li><a href="#"><?php esc_html_e('Privacy Policy', 'nerdywithme'); ?></a></li>
					<li><a href="#"><?php esc_html_e('Terms of Use', 'nerdywithme'); ?></a></li>
				</ul>
				<div>&copy; <?php echo esc_html(wp_date('Y')); ?> <?php bloginfo('name'); ?></div>
			</div>
		</div>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
