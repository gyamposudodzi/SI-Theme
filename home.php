<?php
/**
 * Blog index template.
 *
 * @package NerdyWithMe
 */

get_header();

$featured_post = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => true,
	)
);

$featured_id = $featured_post->posts[0]->ID ?? 0;
?>

<section class="page-section archive-hero">
	<p class="section-intro"><?php esc_html_e('Latest market education from NerdyWithMe', 'nerdywithme'); ?></p>
	<h1 class="archive-headline"><?php bloginfo('name'); ?> <?php esc_html_e('Journal', 'nerdywithme'); ?></h1>
	<p class="archive-description"><?php esc_html_e('Practical trading education, technical analysis, automation ideas, and AI-assisted systems for modern traders.', 'nerdywithme'); ?></p>
</section>

<?php if ($featured_id) : ?>
	<section class="page-section">
		<div class="split-grid">
			<div class="lead-card">
				<?php nerdywithme_card($featured_id); ?>
			</div>
			<?php get_sidebar(); ?>
		</div>
	</section>
<?php endif; ?>

<section class="page-section">
	<?php nerdywithme_section_heading(__('Latest Posts', 'nerdywithme'), __('A rolling feed of trading breakdowns, platform comparisons, and builder-focused market lessons.', 'nerdywithme')); ?>
	<div class="archive-grid">
		<div class="post-grid">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<?php if (get_the_ID() !== $featured_id) : ?>
						<?php nerdywithme_card(get_the_ID()); ?>
					<?php endif; ?>
				<?php endwhile; ?>
			<?php else : ?>
				<p><?php esc_html_e('No posts yet. Add your first post and this grid will come alive.', 'nerdywithme'); ?></p>
			<?php endif; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
	<div class="pagination-wrap">
		<?php the_posts_pagination(); ?>
	</div>
</section>

<section class="page-section">
	<div class="triple-columns">
		<div>
			<?php nerdywithme_section_heading(__('Starter Reads', 'nerdywithme')); ?>
			<?php nerdywithme_ranked_posts(array(), 3); ?>
		</div>
		<div>
			<?php nerdywithme_section_heading(__('Systems To Explore', 'nerdywithme')); ?>
			<?php nerdywithme_compact_posts(array(), 3); ?>
		</div>
		<div>
			<?php nerdywithme_section_heading(__('Quick Lessons', 'nerdywithme')); ?>
			<?php nerdywithme_compact_posts(array('orderby' => 'rand'), 3); ?>
		</div>
	</div>
</section>

<?php
wp_reset_postdata();
get_footer();
