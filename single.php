<?php
/**
 * Single post template.
 *
 * @package NerdyWithMe
 */

get_header();
?>

<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		<article <?php post_class(); ?>>
			<header class="single-hero">
				<?php nerdywithme_post_meta(get_the_ID()); ?>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<p class="entry-summary"><?php echo esc_html(get_the_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 28)); ?></p>
				<div class="single-share">
					<span><?php esc_html_e('Share:', 'nerdywithme'); ?></span>
					<div class="social-links">
						<a href="#" aria-label="<?php esc_attr_e('Facebook', 'nerdywithme'); ?>">f</a>
						<a href="#" aria-label="<?php esc_attr_e('X', 'nerdywithme'); ?>">x</a>
						<a href="#" aria-label="<?php esc_attr_e('Copy link', 'nerdywithme'); ?>">↗</a>
					</div>
				</div>
				<div class="single-hero__image">
					<img src="<?php echo esc_url(nerdywithme_get_post_image(get_the_ID(), 'full')); ?>" alt="<?php the_title_attribute(); ?>">
				</div>
			</header>

			<div class="single-layout">
				<div class="single-content">
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
					<footer class="entry-footer">
						<?php the_tags('<span class="post-categories">', '', '</span>'); ?>
					</footer>
					<section class="read-next">
						<?php nerdywithme_section_heading(__('Read Next', 'nerdywithme')); ?>
						<div class="read-next__grid">
							<?php
							$related = nerdywithme_get_featured_posts(3, array(get_the_ID()));
							while ($related->have_posts()) :
								$related->the_post();
								?>
								<article class="read-next__item">
									<a class="read-next__thumb" href="<?php the_permalink(); ?>">
										<img src="<?php echo esc_url(nerdywithme_get_post_image(get_the_ID(), 'medium_large')); ?>" alt="<?php the_title_attribute(); ?>">
									</a>
									<?php nerdywithme_post_meta(get_the_ID()); ?>
									<h3 class="read-next__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								</article>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>
					</section>
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
