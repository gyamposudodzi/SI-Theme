<?php
/**
 * Front page template.
 *
 * @package NerdyWithMe
 */

get_header();

$featured = nerdywithme_get_featured_posts(12);
$ids      = wp_list_pluck($featured->posts, 'ID');

$hero_id       = $ids[0] ?? 0;
$hero_side_ids = array_slice($ids, 1, 4);
$lead_id       = $ids[5] ?? $hero_id;
$hot_ids       = array_slice($ids, 6, 3);
$latest_ids    = array_slice($ids, 0, 8);
?>

<section class="page-section">
	<div class="hero-grid">
		<?php if ($hero_id) : ?>
			<article class="hero-story">
				<div class="hero-story__grid">
					<div class="hero-copy">
						<?php nerdywithme_post_meta($hero_id); ?>
						<h1 class="entry-title"><a href="<?php echo esc_url(get_permalink($hero_id)); ?>"><?php echo esc_html(get_the_title($hero_id)); ?></a></h1>
						<p class="entry-summary"><?php echo esc_html(wp_trim_words(get_the_excerpt($hero_id), 28)); ?></p>
						<a class="button" href="<?php echo esc_url(get_permalink($hero_id)); ?>"><?php esc_html_e('Read Story', 'nerdywithme'); ?></a>
					</div>
					<a class="hero-media" href="<?php echo esc_url(get_permalink($hero_id)); ?>">
						<img src="<?php echo esc_url(nerdywithme_get_post_image($hero_id, 'large')); ?>" alt="<?php echo esc_attr(get_the_title($hero_id)); ?>">
					</a>
				</div>
			</article>
		<?php endif; ?>

		<div class="hero-sidebar">
			<?php foreach ($hero_side_ids as $side_id) : ?>
				<?php nerdywithme_mini_post($side_id); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="page-section">
	<?php nerdywithme_section_heading(__('Only Top Content', 'nerdywithme'), __('A playful map of the topics that shape your blog.', 'nerdywithme')); ?>
	<div class="category-tiles">
		<?php foreach (nerdywithme_get_primary_categories() as $category) : ?>
			<?php nerdywithme_category_card($category); ?>
		<?php endforeach; ?>
	</div>
</section>

<section class="page-section">
	<?php nerdywithme_section_heading(__('What’s Hot Right Now', 'nerdywithme'), __('The stories getting the most love today.', 'nerdywithme'), get_permalink($lead_id), __('View all', 'nerdywithme')); ?>
	<div class="split-grid">
		<div class="lead-card">
			<?php nerdywithme_card($lead_id); ?>
		</div>
		<div class="sidebar-stack">
			<?php foreach ($hot_ids as $hot_id) : ?>
				<?php nerdywithme_mini_post($hot_id); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="page-section">
	<?php nerdywithme_section_heading(__('Editor Picks', 'nerdywithme'), __('Curated stories chosen for the NerdyWithMe vibe.', 'nerdywithme'), home_url('/'), __('View all', 'nerdywithme')); ?>
	<div class="subgrid">
		<?php foreach (array_slice($ids, 2, 3) as $pick_id) : ?>
			<?php nerdywithme_card($pick_id, 'compact'); ?>
		<?php endforeach; ?>
	</div>
</section>

<section class="page-section">
	<?php nerdywithme_section_heading(__('Latest Posts', 'nerdywithme'), __('Fresh updates, trends, and cozy internet finds.', 'nerdywithme')); ?>
	<div class="latest-grid">
		<div class="post-grid">
			<?php foreach ($latest_ids as $latest_id) : ?>
				<?php nerdywithme_card($latest_id); ?>
			<?php endforeach; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</section>

<section class="page-section">
	<div class="triple-columns">
		<div>
			<?php nerdywithme_section_heading(__('User Favourites', 'nerdywithme')); ?>
			<div class="list-posts">
				<?php
				$fave_query = nerdywithme_get_featured_posts(3, array($hero_id));
				$index      = 1;
				while ($fave_query->have_posts()) :
					$fave_query->the_post();
					?>
					<a class="list-post" href="<?php the_permalink(); ?>">
						<span class="list-post__number"><?php echo esc_html((string) $index); ?></span>
						<span>
							<?php nerdywithme_post_meta(get_the_ID()); ?>
							<span class="list-post__title"><?php the_title(); ?></span>
						</span>
					</a>
					<?php
					$index++;
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
		<div>
			<?php nerdywithme_section_heading(__('You Might Like', 'nerdywithme')); ?>
			<div class="list-posts">
				<?php
				$like_query = nerdywithme_get_featured_posts(3, array($hero_id, $lead_id));
				while ($like_query->have_posts()) :
					$like_query->the_post();
					?>
					<a class="list-post" href="<?php the_permalink(); ?>">
						<span class="compact-list__thumb">
							<img src="<?php echo esc_url(nerdywithme_get_post_image(get_the_ID(), 'thumbnail')); ?>" alt="<?php the_title_attribute(); ?>">
						</span>
						<span>
							<?php nerdywithme_post_meta(get_the_ID()); ?>
							<span class="list-post__title"><?php the_title(); ?></span>
						</span>
					</a>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
		<div>
			<?php nerdywithme_section_heading(__('Short Reads', 'nerdywithme')); ?>
			<div class="list-posts">
				<?php
				$short_query = nerdywithme_get_featured_posts(3, array($hero_id, $lead_id));
				while ($short_query->have_posts()) :
					$short_query->the_post();
					?>
					<a class="list-post" href="<?php the_permalink(); ?>">
						<span class="compact-list__thumb">
							<img src="<?php echo esc_url(nerdywithme_get_post_image(get_the_ID(), 'thumbnail')); ?>" alt="<?php the_title_attribute(); ?>">
						</span>
						<span>
							<?php nerdywithme_post_meta(get_the_ID()); ?>
							<span class="list-post__title"><?php the_title(); ?></span>
						</span>
					</a>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
	</div>
</section>

<?php
wp_reset_postdata();
get_footer();
