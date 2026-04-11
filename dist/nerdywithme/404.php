<?php
/**
 * 404 template.
 *
 * @package NerdyWithMe
 */

get_header();
?>

<section class="page-section error-404">
	<p class="error-404__eyebrow"><?php esc_html_e('Seems you are lost, Buddy', 'nerdywithme'); ?></p>
	<div class="error-404__code">404</div>
	<h1 class="error-404__title"><?php esc_html_e('Oops! We Lost This Page', 'nerdywithme'); ?></h1>
	<p class="error-404__message"><?php esc_html_e('The page you were looking for does not exist anymore, but there is plenty of good stuff to explore from here.', 'nerdywithme'); ?></p>
	<div class="error-404__actions">
		<a class="button" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Back to Home', 'nerdywithme'); ?></a>
		<a class="button button--ghost" href="<?php echo esc_url(home_url('/blog')); ?>"><?php esc_html_e('Browse Articles', 'nerdywithme'); ?></a>
	</div>
</section>

<section class="page-section">
	<?php nerdywithme_section_heading(__('You May Be Interested', 'nerdywithme')); ?>
	<div class="subgrid">
		<?php
		$suggestions = nerdywithme_get_404_suggestion_posts(3);
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
