<?php
/**
 * Archive template.
 *
 * @package NerdyWithMe
 */

get_header();
?>

<section class="page-section archive-hero">
	<p class="section-intro"><?php esc_html_e('Browse the archive', 'nerdywithme'); ?></p>
	<h1 class="archive-headline"><?php the_archive_title(); ?></h1>
	<div class="archive-description"><?php the_archive_description(); ?></div>
</section>

<section class="page-section nwm-deferred-render">
	<div class="archive-grid">
		<div class="post-grid">
			<h2 class="screen-reader-text"><?php esc_html_e('Archive posts', 'nerdywithme'); ?></h2>
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<?php nerdywithme_card(get_the_ID()); ?>
				<?php endwhile; ?>
			<?php else : ?>
				<p><?php esc_html_e('Nothing here yet, but this section is ready for your next trading breakdown.', 'nerdywithme'); ?></p>
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
