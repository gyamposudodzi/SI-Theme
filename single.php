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
		<?php
		$single_style = nerdywithme_get_single_post_style(get_the_ID());
		$summary      = get_the_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 28);
		$previous_post = get_previous_post();
		$next_post     = get_next_post();
		$prepared      = nerdywithme_prepare_single_content(get_the_ID());
		?>
		<article <?php post_class('single-post single-post--' . $single_style); ?> data-reading-article>
			<div class="reading-bar" data-reading-bar aria-label="<?php esc_attr_e('Article navigation', 'nerdywithme'); ?>">
				<div class="reading-bar__slot reading-bar__slot--prev">
					<?php if ($previous_post) : ?>
						<a class="reading-bar__link reading-bar__link--prev" href="<?php echo esc_url(get_permalink($previous_post)); ?>">
							<span class="reading-bar__arrow" aria-hidden="true">&lsaquo;</span>
							<span class="reading-bar__tooltip" role="tooltip"><?php echo esc_html__('Previous article:', 'nerdywithme'); ?> <?php echo esc_html(get_the_title($previous_post)); ?></span>
							<span class="reading-bar__thumb">
								<img src="<?php echo esc_url(nerdywithme_get_post_image($previous_post->ID, 'thumbnail')); ?>" alt="<?php echo esc_attr(get_the_title($previous_post)); ?>">
							</span>
							<span class="reading-bar__text">
								<strong><?php echo esc_html(get_the_title($previous_post)); ?></strong>
							</span>
						</a>
					<?php endif; ?>
				</div>
				<div class="reading-bar__current">
					<strong><?php the_title(); ?></strong>
				</div>
				<div class="reading-bar__slot reading-bar__slot--next">
					<?php if ($next_post) : ?>
						<a class="reading-bar__link reading-bar__link--next" href="<?php echo esc_url(get_permalink($next_post)); ?>">
							<span class="reading-bar__text">
								<strong><?php echo esc_html(get_the_title($next_post)); ?></strong>
							</span>
							<span class="reading-bar__thumb">
								<img src="<?php echo esc_url(nerdywithme_get_post_image($next_post->ID, 'thumbnail')); ?>" alt="<?php echo esc_attr(get_the_title($next_post)); ?>">
							</span>
							<span class="reading-bar__arrow" aria-hidden="true">&rsaquo;</span>
							<span class="reading-bar__tooltip" role="tooltip"><?php echo esc_html__('Next article:', 'nerdywithme'); ?> <?php echo esc_html(get_the_title($next_post)); ?></span>
						</a>
					<?php endif; ?>
				</div>
				<div class="reading-bar__progress" aria-hidden="true">
					<span class="reading-bar__progress-fill" data-reading-progress></span>
				</div>
			</div>
			<?php if ('feature' === $single_style) : ?>
				<header class="single-hero single-hero--feature">
					<?php nerdywithme_post_meta(get_the_ID()); ?>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<p class="entry-summary"><?php echo esc_html($summary); ?></p>
					<div class="single-share">
						<span><?php esc_html_e('Share:', 'nerdywithme'); ?></span>
						<div class="social-links">
							<a href="#" aria-label="<?php esc_attr_e('Facebook', 'nerdywithme'); ?>">f</a>
							<a href="#" aria-label="<?php esc_attr_e('X', 'nerdywithme'); ?>">x</a>
							<a href="#" aria-label="<?php esc_attr_e('Copy link', 'nerdywithme'); ?>">&#8599;</a>
						</div>
					</div>
					<div class="single-hero__image">
						<img src="<?php echo esc_url(nerdywithme_get_post_image(get_the_ID(), 'full')); ?>" alt="<?php the_title_attribute(); ?>">
					</div>
				</header>
				<div class="single-layout">
					<div class="single-content">
						<?php echo str_replace('<nav class="toc"', '<nav class="toc" data-toc', $prepared['toc']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<div class="entry-content">
							<?php echo $prepared['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<?php if (function_exists('nerdywithme_tools_render_ad_slot')) : ?>
							<div class="theme-ad-slot theme-ad-slot--single-inline">
								<?php nerdywithme_tools_render_ad_slot('single_inline'); ?>
							</div>
						<?php endif; ?>
						<footer class="entry-footer">
							<?php the_tags('<span class="post-categories">', '', '</span>'); ?>
						</footer>
						<section class="read-next">
							<?php nerdywithme_section_heading(__('Read Next', 'nerdywithme'), __('Keep building from fundamentals into tools, automation, and smarter decision-making.', 'nerdywithme')); ?>
							<div class="read-next__grid">
								<?php
								$related = nerdywithme_get_related_posts(get_the_ID(), 3);
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
			<?php else : ?>
				<div class="single-layout single-layout--standard">
					<div class="single-content">
						<header class="single-hero single-hero--standard">
							<div class="single-hero__intro">
								<?php nerdywithme_post_meta(get_the_ID()); ?>
								<h1 class="entry-title"><?php the_title(); ?></h1>
								<p class="entry-summary"><?php echo esc_html($summary); ?></p>
								<div class="single-share">
									<span><?php esc_html_e('Share:', 'nerdywithme'); ?></span>
									<div class="social-links">
										<a href="#" aria-label="<?php esc_attr_e('Facebook', 'nerdywithme'); ?>">f</a>
										<a href="#" aria-label="<?php esc_attr_e('X', 'nerdywithme'); ?>">x</a>
										<a href="#" aria-label="<?php esc_attr_e('Copy link', 'nerdywithme'); ?>">&#8599;</a>
									</div>
								</div>
							</div>
							<div class="single-hero__image single-hero__image--standard">
								<img src="<?php echo esc_url(nerdywithme_get_post_image(get_the_ID(), 'full')); ?>" alt="<?php the_title_attribute(); ?>">
							</div>
						</header>
						<?php echo str_replace('<nav class="toc"', '<nav class="toc" data-toc', $prepared['toc']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<div class="entry-content">
							<?php echo $prepared['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<?php if (function_exists('nerdywithme_tools_render_ad_slot')) : ?>
							<div class="theme-ad-slot theme-ad-slot--single-inline">
								<?php nerdywithme_tools_render_ad_slot('single_inline'); ?>
							</div>
						<?php endif; ?>
						<footer class="entry-footer">
							<?php the_tags('<span class="post-categories">', '', '</span>'); ?>
						</footer>
						<section class="read-next">
							<?php nerdywithme_section_heading(__('Read Next', 'nerdywithme'), __('Keep building from fundamentals into tools, automation, and smarter decision-making.', 'nerdywithme')); ?>
							<div class="read-next__grid">
								<?php
								$related = nerdywithme_get_related_posts(get_the_ID(), 3);
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
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
<?php endif; ?>

<?php
get_footer();
