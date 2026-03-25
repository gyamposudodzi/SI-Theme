<?php
/**
 * Site header.
 *
 * @package NerdyWithMe
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<header class="site-header">
		<div class="nwm-shell">
			<?php
			$mega_posts = nerdywithme_get_featured_posts(2);
			?>
			<div class="site-header__top">
				<?php nerdywithme_render_social_links(); ?>
				<?php nerdywithme_branding(false); ?>
				<div class="site-header__cta" aria-hidden="true"></div>
			</div>
			<div class="site-header__nav">
				<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-navigation">
					<span class="screen-reader-text"><?php esc_html_e('Toggle menu', 'nerdywithme'); ?></span>
					<span class="nav-toggle__bars"></span>
				</button>
				<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e('Primary menu', 'nerdywithme'); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'menu_class'     => 'menu',
							'fallback_cb'    => 'nerdywithme_primary_menu_fallback',
						)
					);
					?>
				</nav>
				<div class="header-tools">
					<button class="header-tools__button search-toggle" type="button" aria-expanded="false" aria-controls="search-panel">
						<span class="screen-reader-text"><?php esc_html_e('Open search', 'nerdywithme'); ?></span>
						<span class="header-tools__icon">?</span>
					</button>
				</div>
			</div>
			<div class="mega-panel" aria-hidden="true">
				<div class="mega-panel__grid">
					<div class="mega-panel__group">
						<h2><?php esc_html_e('Core Pages', 'nerdywithme'); ?></h2>
						<ul class="mega-panel__links">
							<li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'nerdywithme'); ?></a></li>
							<li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php esc_html_e('About', 'nerdywithme'); ?></a></li>
							<li><a href="<?php echo esc_url(home_url('/blog')); ?>"><?php esc_html_e('Blog', 'nerdywithme'); ?></a></li>
							<li><a href="<?php echo esc_url(home_url('/tools')); ?>"><?php esc_html_e('Tools', 'nerdywithme'); ?></a></li>
						</ul>
					</div>
					<div class="mega-panel__group">
						<h2><?php esc_html_e('Categories', 'nerdywithme'); ?></h2>
						<ul class="mega-panel__links">
							<?php foreach (nerdywithme_get_primary_categories() as $category) : ?>
								<li><a href="<?php echo esc_url(get_category_link($category)); ?>"><?php echo esc_html($category->name); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="mega-panel__group">
						<h2><?php esc_html_e('Builder Tracks', 'nerdywithme'); ?></h2>
						<ul class="mega-panel__links">
							<li><a href="<?php echo esc_url(home_url('/projects')); ?>"><?php esc_html_e('Projects', 'nerdywithme'); ?></a></li>
							<li><a href="<?php echo esc_url(home_url('/resources')); ?>"><?php esc_html_e('Resources', 'nerdywithme'); ?></a></li>
							<li><a href="<?php echo esc_url(home_url('/category/automation-bots')); ?>"><?php esc_html_e('Automation & Bots', 'nerdywithme'); ?></a></li>
							<li><a href="<?php echo esc_url(home_url('/category/ai-quant-trading')); ?>"><?php esc_html_e('AI & Quant Trading', 'nerdywithme'); ?></a></li>
						</ul>
					</div>
					<div class="mega-panel__posts">
						<h2><?php esc_html_e('Latest Breakdowns', 'nerdywithme'); ?></h2>
						<div class="mega-panel__post-grid">
							<?php while ($mega_posts->have_posts()) : $mega_posts->the_post(); ?>
								<a class="mega-panel__post" href="<?php the_permalink(); ?>">
									<span class="mega-panel__thumb">
										<img src="<?php echo esc_url(nerdywithme_get_post_image(get_the_ID(), 'medium')); ?>" alt="<?php the_title_attribute(); ?>">
									</span>
									<span class="mega-panel__meta"><?php nerdywithme_post_meta(get_the_ID()); ?></span>
									<strong><?php the_title(); ?></strong>
								</a>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>
					</div>
				</div>
			</div>
			<div id="search-panel" class="search-panel">
				<?php get_search_form(); ?>
			</div>
		</div>
	</header>
	<main id="primary" class="site-main">
		<div class="nwm-shell">
