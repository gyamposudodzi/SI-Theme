<?php
/**
 * Sidebar template.
 *
 * @package NerdyWithMe
 */
?>

<aside class="sidebar">
	<?php
	$sidebar_social_cards = nerdywithme_get_social_cards('sidebar');
	$profile_stack_urls   = nerdywithme_get_profile_stack_urls();
	$profile_card         = nerdywithme_get_profile_card_settings();
	$top_picks_slug       = nerdywithme_get_content_source_slug('top_picks_category', '');
	$sidebar_featured_slug = nerdywithme_get_content_source_slug('sidebar_featured_category', 'featured');
	?>
	<section class="widget-card widget-card--plain">
		<h2 class="widget-card__title"><?php esc_html_e('Top Picks', 'nerdywithme'); ?></h2>
		<?php nerdywithme_ranked_posts($top_picks_slug ? array('category_name' => $top_picks_slug) : array('post__not_in' => array()), 3); ?>
	</section>

	<section class="widget-card widget-card--featured">
		<h2 class="widget-card__title"><?php esc_html_e('Featured', 'nerdywithme'); ?></h2>
		<?php nerdywithme_featured_slider_posts($sidebar_featured_slug ? array('category_name' => $sidebar_featured_slug) : array(), 3); ?>
	</section>

	<section class="widget-card widget-card--plain">
		<h2 class="widget-card__title"><?php esc_html_e('Favorite Apps', 'nerdywithme'); ?></h2>
		<div class="sidebar-stack">
			<?php foreach ($sidebar_social_cards as $card) : ?>
				<a class="app-link app-link--<?php echo esc_attr($card['tone']); ?>" href="<?php echo esc_url($card['url']); ?>">
					<?php echo nerdywithme_render_card_icon($card, 'app-link__service'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="app-link__body"><strong><?php echo esc_html($card['handle']); ?></strong><span><?php echo esc_html($card['description']); ?></span></span>
					<span class="app-link__arrow">></span>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="widget-card widget-card--profile">
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
					<h2 class="widget-card__title"><?php echo esc_html($profile_card['handle']); ?></h2>
					<p><?php echo esc_html($profile_card['followers']); ?></p>
				</div>
				<a class="profile-card__button" href="<?php echo esc_url($profile_card['button_url']); ?>"><?php echo esc_html($profile_card['button_label']); ?> &rsaquo;</a>
			</div>
		</div>
	</section>
</aside>
