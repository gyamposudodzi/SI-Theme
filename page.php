<?php
/**
 * Page template.
 *
 * @package NerdyWithMe
 */

get_header();
?>

<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		<article <?php post_class(); ?>>
			<header class="archive-hero">
				<p class="section-intro"><?php esc_html_e('Page', 'nerdywithme'); ?></p>
				<h1 class="archive-headline"><?php the_title(); ?></h1>
				<?php if (has_excerpt()) : ?>
					<p class="archive-description"><?php echo esc_html(get_the_excerpt()); ?></p>
				<?php endif; ?>
			</header>
			<div class="single-layout">
				<div class="single-content">
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
					<?php if (comments_open() || get_comments_number()) : ?>
						<?php comments_template(); ?>
					<?php endif; ?>
				</div>
				<?php get_sidebar(); ?>
			</div>
		</article>
	<?php endwhile; ?>
<?php endif; ?>

<?php
get_footer();
