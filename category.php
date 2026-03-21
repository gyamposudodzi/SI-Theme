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
			<article class="category-pill">
				<a class="category-pill__thumb" href="<?php echo esc_url(get_category_link($category)); ?>">
					<?php
					$cat_post = get_posts(
						array(
							'posts_per_page' => 1,
							'cat'            => $category->term_id,
						)
					);
					$image_id = $cat_post[0]->ID ?? 0;
					?>
					<img src="<?php echo esc_url(nerdywithme_get_post_image($image_id, 'medium_large')); ?>" alt="<?php echo esc_attr($category->name); ?>">
				</a>
				<div class="category-pill__name"><?php echo esc_html($category->name); ?></div>
				<p class="section-intro"><?php echo esc_html(wp_trim_words(strip_tags(category_description($category)), 12, '...')); ?></p>
			</article>
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
