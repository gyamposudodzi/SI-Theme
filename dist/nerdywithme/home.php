<?php
/**
 * Blog index template.
 *
 * @package NerdyWithMe
 */

get_header();

$posts_page_id = (int) get_option('page_for_posts');
$blog_title    = $posts_page_id ? get_the_title($posts_page_id) : __('Blog', 'nerdywithme');
$blog_intro    = $posts_page_id ? get_post_field('post_excerpt', $posts_page_id) : '';

$featured_post = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => true,
	)
);

$featured_id = $featured_post->posts[0]->ID ?? 0;
$grid_ids    = array();

if ($featured_id) {
	$grid_query = nerdywithme_get_featured_posts(4, array($featured_id));
	$grid_ids   = wp_list_pluck($grid_query->posts, 'ID');
	wp_reset_postdata();
}
?>

<section class="page-section archive-hero">
	<div class="archive-hero__layout">
		<div class="archive-hero__content">
			<p class="section-intro"><?php esc_html_e('Latest market education from NerdyWithMe', 'nerdywithme'); ?></p>
			<h1 class="archive-headline"><?php echo esc_html($blog_title); ?></h1>
			<p class="archive-description">
				<?php
				echo esc_html(
					$blog_intro
						? $blog_intro
						: __('Practical trading education, technical analysis, automation ideas, and AI-assisted systems for modern traders.', 'nerdywithme')
				);
				?>
			</p>
		</div>
		<?php if (function_exists('nerdywithme_tools_render_ad_slot')) : ?>
			<div class="theme-ad-slot theme-ad-slot--archive-header">
				<?php nerdywithme_tools_render_ad_slot('archive_header'); ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="page-section nwm-deferred-render">
	<div class="archive-grid archive-grid--blog">
		<div class="blog-page">
			<h2 class="screen-reader-text"><?php esc_html_e('Latest blog posts', 'nerdywithme'); ?></h2>
			<?php if ($featured_id) : ?>
				<div class="blog-page__feature">
					<?php nerdywithme_card($featured_id, 'lead'); ?>
				</div>
			<?php endif; ?>

			<div class="blog-page__grid">
				<?php if (! empty($grid_ids)) : ?>
					<?php foreach ($grid_ids as $grid_id) : ?>
						<?php nerdywithme_card($grid_id); ?>
					<?php endforeach; ?>
				<?php elseif (have_posts()) : ?>
					<?php while (have_posts()) : the_post(); ?>
						<?php if (get_the_ID() !== $featured_id) : ?>
							<?php nerdywithme_card(get_the_ID()); ?>
						<?php endif; ?>
					<?php endwhile; ?>
				<?php else : ?>
					<p><?php esc_html_e('No posts yet. Add your first post and this grid will come alive.', 'nerdywithme'); ?></p>
				<?php endif; ?>
			</div>

			<div class="pagination-wrap">
				<?php the_posts_pagination(); ?>
			</div>
		</div>
		<?php get_sidebar(); ?>
	</div>
</section>

<?php
wp_reset_postdata();
get_footer();
