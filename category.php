<?php
/**
 * Category template.
 *
 * @package NerdyWithMe
 */

get_header();
?>

<section class="page-section archive-hero category-hero">
	<p class="section-intro"><?php esc_html_e('Category', 'nerdywithme'); ?></p>
	<h1 class="archive-headline"><?php single_cat_title(); ?></h1>
	<div class="archive-description term-description">
		<?php
		echo wp_kses_post(
			category_description()
				? category_description()
				: __('A focused collection of posts from this trading track, with the latest breakdowns, tools, and lessons ready to explore.', 'nerdywithme')
		);
		?>
	</div>
</section>

<section class="page-section">
	<div class="archive-grid results-layout">
		<div class="post-grid results-grid">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<?php nerdywithme_card(get_the_ID()); ?>
				<?php endwhile; ?>
			<?php else : ?>
				<div class="results-empty">
					<h2><?php esc_html_e('No posts in this category yet.', 'nerdywithme'); ?></h2>
					<p><?php esc_html_e('This section is ready for future breakdowns. For now, try another track from the sidebar.', 'nerdywithme'); ?></p>
				</div>
			<?php endif; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</section>

<?php
get_footer();
