<?php
/**
 * Search results template.
 *
 * @package NerdyWithMe
 */

get_header();

$search_query = get_search_query();
global $wp_query;
$results_count = (int) $wp_query->found_posts;
?>

<section class="page-section archive-hero search-hero">
	<p class="section-intro"><?php esc_html_e('Search Results', 'nerdywithme'); ?></p>
	<h1 class="archive-headline">
		<?php
		printf(
			/* translators: %s: search query. */
			esc_html__('Results for "%s"', 'nerdywithme'),
			esc_html($search_query)
		);
		?>
	</h1>
	<p class="archive-description">
		<?php
		printf(
			/* translators: %d: number of search results. */
			esc_html(_n('%d article matched your search.', '%d articles matched your search.', $results_count, 'nerdywithme')),
			esc_html($results_count)
		);
		?>
	</p>
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
					<h2><?php esc_html_e('Nothing matched that search yet.', 'nerdywithme'); ?></h2>
					<p><?php esc_html_e('Try a broader keyword, or explore the trading topics in the sidebar while we keep building the library.', 'nerdywithme'); ?></p>
					<?php get_search_form(); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</section>

<?php
get_footer();
