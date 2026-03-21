<?php
/**
 * Category template.
 *
 * @package NerdyWithMe
 */

get_header();
?>

<section class="page-section archive-hero">
	<p class="section-intro"><?php esc_html_e('Category', 'nerdywithme'); ?></p>
	<h1 class="archive-headline"><?php single_cat_title(); ?></h1>
	<div class="archive-description term-description"><?php echo category_description(); ?></div>
</section>

<section class="page-section">
	<?php nerdywithme_section_heading(__('Explore More Categories', 'nerdywithme')); ?>
	<div class="category-tiles">
		<?php foreach (nerdywithme_get_primary_categories() as $category) : ?>
			<?php nerdywithme_category_card($category); ?>
		<?php endforeach; ?>
	</div>
</section>

<section class="page-section">
	<div class="archive-grid">
		<div class="post-grid">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<?php nerdywithme_card(get_the_ID()); ?>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
	<div class="pagination-wrap">
		<?php the_posts_pagination(); ?>
	</div>
</section>

<?php
get_footer();
