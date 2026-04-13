<?php
/**
 * Front page template.
 *
 * @package NerdyWithMe
 */

get_header();

$featured = nerdywithme_get_featured_posts(18);
$ids      = wp_list_pluck($featured->posts, 'ID');
$home_social_cards = nerdywithme_get_social_cards('home');
$hero_source_slug   = nerdywithme_get_content_source_slug('hero_category', 'featured');
$editor_source_slug = nerdywithme_get_content_source_slug('editors_pick_category', 'editors-pick');
$hero_query = $hero_source_slug ? nerdywithme_get_posts_by_category_name($hero_source_slug, 1) : nerdywithme_get_featured_posts(1);
$hero_ids   = wp_list_pluck($hero_query->posts, 'ID');
$editor_query = $editor_source_slug ? nerdywithme_get_posts_by_category_name($editor_source_slug, 3) : nerdywithme_get_featured_posts(3);
$editor_ids   = wp_list_pluck($editor_query->posts, 'ID');

$hero_id        = $hero_ids[0] ?? ($ids[0] ?? 0);
$reserved_ids   = array_filter(array_merge(array($hero_id), $editor_ids));
$remaining_ids  = array_values(array_diff($ids, $reserved_ids));
$hero_side_ids  = array_slice($remaining_ids, 0, 4);
$hot_pair_ids   = array_slice($remaining_ids, 4, 2);
$hot_strip_ids  = array_slice($remaining_ids, 6, 4);
$popular_ids    = array_slice($remaining_ids, 2, 7);
$starter_query  = nerdywithme_get_featured_posts(3, array_merge(array($hero_id), $editor_ids));
$starter_ids    = wp_list_pluck($starter_query->posts, 'ID');
$builder_query  = nerdywithme_get_featured_posts(3, array_merge(array($hero_id, $hot_pair_ids[0] ?? 0), $editor_ids, $starter_ids));
$builder_ids    = wp_list_pluck($builder_query->posts, 'ID');
$quick_query    = nerdywithme_get_featured_posts(3, array_merge(array($hero_id, $hot_pair_ids[0] ?? 0, $hot_pair_ids[1] ?? 0), $editor_ids, $starter_ids, $builder_ids));

if (count($editor_ids) < 3) {
	$fallback_editor_ids = array_slice(array_values(array_diff($ids, array($hero_id), $hero_side_ids, $hot_pair_ids, $hot_strip_ids)), 0, 3 - count($editor_ids));
	$editor_ids          = array_values(array_unique(array_merge($editor_ids, $fallback_editor_ids)));
	$editor_ids          = array_slice($editor_ids, 0, 3);
}
?>

<section class="page-section homepage-compact-hero">
	<div class="compact-hero-grid">
		<?php if ($hero_id) : ?>
			<article class="compact-hero-card">
				<a class="compact-hero-card__thumb" href="<?php echo esc_url(get_permalink($hero_id)); ?>">
					<?php echo nerdywithme_get_post_image_tag($hero_id, 'nwm-hero', array('alt' => get_the_title($hero_id), 'loading' => 'eager', 'fetchpriority' => 'high'), '100vw'); ?>
				</a>
				<div class="compact-hero-card__content">
					<?php nerdywithme_post_meta($hero_id); ?>
					<h1 class="entry-title"><a href="<?php echo esc_url(get_permalink($hero_id)); ?>"><?php echo esc_html(get_the_title($hero_id)); ?></a></h1>
					<p class="entry-summary"><?php echo esc_html(wp_trim_words(get_the_excerpt($hero_id), 18)); ?></p>
				</div>
			</article>
		<?php endif; ?>

		<div class="compact-hero-side">
			<?php foreach ($hero_side_ids as $side_id) : ?>
				<?php nerdywithme_card($side_id, 'compact'); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php if (function_exists('nerdywithme_tools_render_ad_slot')) : ?>
	<section class="page-section theme-ad-slot theme-ad-slot--homepage">
		<?php nerdywithme_tools_render_ad_slot('homepage_after_hero'); ?>
	</section>
<?php endif; ?>

<section class="page-section">
	<?php nerdywithme_section_heading(__('Connect With My Favorite Apps', 'nerdywithme'), __('Chosen by the editor.', 'nerdywithme')); ?>
	<div class="app-strip app-strip--social">
		<?php foreach ($home_social_cards as $card) : ?>
			<a class="app-chip app-chip--<?php echo esc_attr($card['tone']); ?>" href="<?php echo esc_url($card['url']); ?>">
				<?php echo nerdywithme_render_card_icon($card, 'app-chip__icon'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="app-chip__body"><span><?php echo esc_html($card['handle']); ?></span><strong><?php echo esc_html($card['description']); ?></strong></span>
			</a>
		<?php endforeach; ?>
	</div>
</section>

<section class="page-section">
	<?php nerdywithme_section_heading(__('What\'s Hot Right Now', 'nerdywithme'), __('Timely reads on markets, trader workflow, and the tech that sharpens decision-making.', 'nerdywithme'), home_url('/blog'), __('View all', 'nerdywithme')); ?>
	<div class="hot-grid">
		<div class="hot-grid__feature">
			<?php foreach ($hot_pair_ids as $hot_id) : ?>
				<?php nerdywithme_card($hot_id); ?>
			<?php endforeach; ?>
		</div>
		<div class="hot-grid__strip">
			<?php foreach ($hot_strip_ids as $hot_id) : ?>
				<?php nerdywithme_card($hot_id, 'compact'); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="page-section">
	<?php nerdywithme_section_heading(__('Editor Picks', 'nerdywithme'), __('The clearest beginner guides, systematic trading lessons, and tech-first reads to start with.', 'nerdywithme'), home_url('/blog'), __('View all', 'nerdywithme')); ?>
	<div class="subgrid">
		<?php foreach ($editor_ids as $pick_id) : ?>
			<?php nerdywithme_card($pick_id, 'compact'); ?>
		<?php endforeach; ?>
	</div>
</section>

<section class="page-section">
	<?php nerdywithme_section_heading(__('Popular Articles', 'nerdywithme'), __('Longer reads on execution, technical analysis, automation, and AI-assisted trading systems.', 'nerdywithme')); ?>
	<div class="latest-grid">
		<div class="row-posts">
			<?php foreach ($popular_ids as $popular_id) : ?>
				<?php nerdywithme_row_post($popular_id); ?>
			<?php endforeach; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</section>

<section class="page-section">
	<div class="triple-columns">
		<div>
			<?php nerdywithme_section_heading(__('Starter Reads', 'nerdywithme')); ?>
			<div class="list-posts">
				<?php
				while ($starter_query->have_posts()) :
					$starter_query->the_post();
					?>
					<article class="list-post list-post--circle">
						<a class="list-post__media" href="<?php the_permalink(); ?>">
							<span class="compact-list__thumb compact-list__thumb--circle">
								<?php echo nerdywithme_get_post_image_tag(get_the_ID(), 'nwm-thumb', array('alt' => get_the_title()), '(max-width: 1100px) 18vw, 104px'); ?>
							</span>
						</a>
						<div class="list-post__body">
							<div class="list-post__content">
								<?php nerdywithme_post_meta(get_the_ID()); ?>
								<a class="list-post__title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</div>
						</div>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
		<div>
			<?php nerdywithme_section_heading(__('Build Your Edge', 'nerdywithme')); ?>
			<div class="list-posts">
				<?php while ($builder_query->have_posts()) : $builder_query->the_post(); ?>
					<article class="list-post list-post--circle">
						<a class="list-post__media" href="<?php the_permalink(); ?>">
							<span class="compact-list__thumb compact-list__thumb--circle">
								<?php echo nerdywithme_get_post_image_tag(get_the_ID(), 'nwm-thumb', array('alt' => get_the_title()), '(max-width: 1100px) 18vw, 104px'); ?>
							</span>
						</a>
						<div class="list-post__body">
							<div class="list-post__content">
								<?php nerdywithme_post_meta(get_the_ID()); ?>
								<a class="list-post__title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</div>
						</div>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
		<div>
			<?php nerdywithme_section_heading(__('Quick Lessons', 'nerdywithme')); ?>
			<div class="list-posts">
				<?php while ($quick_query->have_posts()) : $quick_query->the_post(); ?>
					<article class="list-post list-post--circle">
						<a class="list-post__media" href="<?php the_permalink(); ?>">
							<span class="compact-list__thumb compact-list__thumb--circle">
								<?php echo nerdywithme_get_post_image_tag(get_the_ID(), 'nwm-thumb', array('alt' => get_the_title()), '(max-width: 1100px) 18vw, 104px'); ?>
							</span>
						</a>
						<div class="list-post__body">
							<div class="list-post__content">
								<?php nerdywithme_post_meta(get_the_ID()); ?>
								<a class="list-post__title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</div>
						</div>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
	</div>
</section>

<?php
wp_reset_postdata();
get_footer();
