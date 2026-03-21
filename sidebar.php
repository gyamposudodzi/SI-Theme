<?php
/**
 * Sidebar template.
 *
 * @package NerdyWithMe
 */
?>

<aside class="sidebar">
	<?php if (is_active_sidebar('sidebar-1')) : ?>
		<?php dynamic_sidebar('sidebar-1'); ?>
	<?php else : ?>
		<section class="widget-card">
			<h2 class="widget-card__title"><?php esc_html_e('Top Picks', 'nerdywithme'); ?></h2>
			<?php nerdywithme_ranked_posts(array(), 3); ?>
		</section>

		<section class="widget-card">
			<h2 class="widget-card__title"><?php esc_html_e('Featured', 'nerdywithme'); ?></h2>
			<?php nerdywithme_compact_posts(array(), 1); ?>
		</section>

		<section class="widget-card">
			<h2 class="widget-card__title"><?php esc_html_e('Favorite Apps', 'nerdywithme'); ?></h2>
			<div class="sidebar-stack">
				<a class="app-link" href="#"><span>@nerdywithme</span><span>Spotify</span></a>
				<a class="app-link" href="#"><span>@nerdywithme</span><span>Instagram</span></a>
				<a class="app-link" href="#"><span>@nerdywithme</span><span>Discord</span></a>
			</div>
		</section>

		<section class="widget-card">
			<div class="profile-card">
				<span class="profile-card__avatar">
					<img src="<?php echo esc_url(nerdywithme_fallback_image()); ?>" alt="<?php esc_attr_e('NerdyWithMe avatar', 'nerdywithme'); ?>">
				</span>
				<div>
					<h2 class="widget-card__title"><?php esc_html_e('NerdyWithMe', 'nerdywithme'); ?></h2>
					<p><?php esc_html_e('A bright little corner for nerdy culture, internet finds, and playful storytelling.', 'nerdywithme'); ?></p>
				</div>
			</div>
		</section>
	<?php endif; ?>
</aside>
