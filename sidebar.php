<?php
/**
 * Sidebar template.
 *
 * @package NerdyWithMe
 */
?>

<aside class="sidebar">
	<?php if (is_active_sidebar('sidebar-1') && ! is_front_page()) : ?>
		<?php dynamic_sidebar('sidebar-1'); ?>
	<?php else : ?>
		<section class="widget-card widget-card--plain">
			<h2 class="widget-card__title"><?php esc_html_e('Top Picks', 'nerdywithme'); ?></h2>
			<?php nerdywithme_ranked_posts(array('post__not_in' => array()), 3); ?>
		</section>

		<section class="widget-card widget-card--featured">
			<h2 class="widget-card__title"><?php esc_html_e('Featured', 'nerdywithme'); ?></h2>
			<?php nerdywithme_featured_slider_posts(array('category_name' => 'featured'), 3); ?>
		</section>

		<section class="widget-card widget-card--plain">
			<h2 class="widget-card__title"><?php esc_html_e('Favorite Apps', 'nerdywithme'); ?></h2>
			<div class="sidebar-stack">
				<a class="app-link" href="#">
					<span class="app-link__service">TV</span>
					<span class="app-link__body"><strong>@nerdywithme</strong><span>Track setups in TradingView</span></span>
					<span class="app-link__arrow">></span>
				</a>
				<a class="app-link" href="#">
					<span class="app-link__service">X</span>
					<span class="app-link__body"><strong>@nerdywithme</strong><span>Follow market notes on X</span></span>
					<span class="app-link__arrow">></span>
				</a>
				<a class="app-link" href="#">
					<span class="app-link__service">GH</span>
					<span class="app-link__body"><strong>@nerdywithme</strong><span>See bots and build logs</span></span>
					<span class="app-link__arrow">></span>
				</a>
			</div>
		</section>

		<section class="widget-card widget-card--profile">
			<?php
			$profile_stack_query = nerdywithme_get_featured_posts(3);
			$profile_stack_urls  = array();

			if ($profile_stack_query->have_posts()) {
				while ($profile_stack_query->have_posts()) {
					$profile_stack_query->the_post();
					$profile_stack_urls[] = nerdywithme_get_post_image(get_the_ID(), 'medium_large');
				}
				wp_reset_postdata();
			}

			while (count($profile_stack_urls) < 3) {
				$profile_stack_urls[] = nerdywithme_fallback_image();
			}
			?>
			<div class="profile-card">
				<div class="profile-card__stack" aria-hidden="true">
					<span class="profile-card__layer profile-card__layer--back">
						<img src="<?php echo esc_url($profile_stack_urls[0]); ?>" alt="">
					</span>
					<span class="profile-card__layer profile-card__layer--mid">
						<img src="<?php echo esc_url($profile_stack_urls[1]); ?>" alt="">
					</span>
					<span class="profile-card__post">
						<span class="profile-card__post-image">
							<img src="<?php echo esc_url($profile_stack_urls[2]); ?>" alt="">
						</span>
						<span class="profile-card__post-ui">
							<span class="profile-card__post-actions">
								<span>&hearts;</span>
								<span>&#9675;</span>
								<span>&#9993;</span>
								<span class="profile-card__post-dots">....</span>
								<span>&#128278;</span>
							</span>
						</span>
					</span>
				</div>
				<div class="profile-card__footer">
					<div class="profile-card__identity">
						<h2 class="widget-card__title"><?php esc_html_e('@nerdywithme', 'nerdywithme'); ?></h2>
						<p><?php esc_html_e('127K followers', 'nerdywithme'); ?></p>
					</div>
					<a class="profile-card__button" href="#"><?php esc_html_e('Follow', 'nerdywithme'); ?> &rsaquo;</a>
				</div>
			</div>
		</section>
	<?php endif; ?>
</aside>
