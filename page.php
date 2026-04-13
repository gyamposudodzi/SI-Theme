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
		<?php $is_tools_page = is_page('tools'); ?>
		<?php $active_tool = ($is_tools_page && function_exists('nerdywithme_tools_get_active_tool_data')) ? nerdywithme_tools_get_active_tool_data() : null; ?>
		<article <?php post_class(); ?>>
			<header class="archive-hero">
				<div class="archive-hero__layout">
					<div class="archive-hero__content">
						<p class="section-intro">
							<?php
							echo esc_html(
								$is_tools_page && $active_tool
									? __('Tool', 'nerdywithme')
									: ($is_tools_page ? __('Tool Hub', 'nerdywithme') : __('Page', 'nerdywithme'))
							);
							?>
						</p>
						<h1 class="archive-headline">
							<?php
							echo esc_html(
								$is_tools_page && $active_tool
									? $active_tool['label']
									: get_the_title()
							);
							?>
						</h1>
						<?php if ($is_tools_page && $active_tool) : ?>
							<p class="archive-description"><?php echo esc_html($active_tool['summary'] ?: $active_tool['description']); ?></p>
						<?php elseif (has_excerpt()) : ?>
							<p class="archive-description"><?php echo esc_html(get_the_excerpt()); ?></p>
						<?php endif; ?>
					</div>
					<?php if (function_exists('nerdywithme_tools_render_ad_slot')) : ?>
						<div class="theme-ad-slot theme-ad-slot--archive-header">
							<?php nerdywithme_tools_render_ad_slot('archive_header'); ?>
						</div>
					<?php endif; ?>
				</div>
			</header>
			<?php if ($is_tools_page) : ?>
				<div class="tools-page-layout">
					<?php if (function_exists('nerdywithme_tools_render_ad_slot')) : ?>
						<div class="theme-ad-slot theme-ad-slot--tools-top">
							<?php nerdywithme_tools_render_ad_slot('homepage_after_hero'); ?>
						</div>
					<?php endif; ?>
					<div class="tools-page-main">
						<div class="single-content">
							<div class="entry-content entry-content--tools">
								<?php the_content(); ?>
							</div>
							<section class="read-next read-next--tools">
								<?php nerdywithme_section_heading(__('Read Next', 'nerdywithme'), __('Keep learning with practical trade setups, platform breakdowns, and system ideas.', 'nerdywithme')); ?>
								<div class="read-next__grid">
									<?php
									$tool_reads = nerdywithme_get_related_posts(get_the_ID(), 3);

									if ($tool_reads->have_posts()) :
										while ($tool_reads->have_posts()) :
											$tool_reads->the_post();
											?>
											<article class="read-next__item">
												<a class="read-next__thumb" href="<?php the_permalink(); ?>">
													<?php echo nerdywithme_get_post_image_tag(get_the_ID(), 'large', array('alt' => get_the_title()), '(max-width: 1100px) 45vw, 320px'); ?>
												</a>
												<?php nerdywithme_post_meta(); ?>
												<h3 class="read-next__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
											</article>
											<?php
										endwhile;
										wp_reset_postdata();
									endif;
									?>
								</div>
							</section>
						</div>
						<aside class="tools-page-aside">
							<?php if (function_exists('nerdywithme_tools_render_ad_slot')) : ?>
								<div class="theme-ad-slot theme-ad-slot--tools-side">
									<?php nerdywithme_tools_render_ad_slot('sidebar'); ?>
								</div>
							<?php endif; ?>
						</aside>
					</div>
					<?php if (function_exists('nerdywithme_tools_render_ad_slot')) : ?>
						<div class="theme-ad-slot theme-ad-slot--tools-bottom">
							<?php nerdywithme_tools_render_ad_slot('footer'); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php else : ?>
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
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
<?php endif; ?>

<?php
get_footer();
