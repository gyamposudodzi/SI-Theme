<?php
/**
 * 404 template.
 *
 * @package NerdyWithMe
 */

get_header();
?>

<section class="page-section">
	<?php nerdywithme_section_heading(__('Explore Categories', 'nerdywithme')); ?>
	<div class="category-tiles">
		<?php foreach (nerdywithme_get_primary_categories() as $category) : ?>
			<?php nerdywithme_category_card($category); ?>
		<?php endforeach; ?>
	</div>
</section>

<section class="page-section error-404">
	<div class="error-404__code">404</div>
	<h1 class="error-404__title"><?php esc_html_e('Oops! We Lost This Page', 'nerdywithme'); ?></h1>
	<p class="error-404__message"><?php esc_html_e('The page you were looking for does not exist anymore, but there is plenty of good stuff to explore from here.', 'nerdywithme'); ?></p>
	<a class="button" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Back to Home', 'nerdywithme'); ?></a>
</section>

<section class="page-section">
	<?php nerdywithme_section_heading(__('You May Be Interested', 'nerdywithme')); ?>
	<div class="subgrid">
		<?php
		$suggestions = nerdywithme_get_featured_posts(3);
		while ($suggestions->have_posts()) :
			$suggestions->the_post();
			nerdywithme_card(get_the_ID(), 'compact');
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>

<?php
get_footer();
