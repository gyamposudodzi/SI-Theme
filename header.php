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
			<div class="site-header__top">
				<div class="social-links" aria-label="<?php esc_attr_e('Social links', 'nerdywithme'); ?>">
					<a href="#" aria-label="<?php esc_attr_e('Facebook', 'nerdywithme'); ?>">f</a>
					<a href="#" aria-label="<?php esc_attr_e('X', 'nerdywithme'); ?>">x</a>
					<a href="#" aria-label="<?php esc_attr_e('Instagram', 'nerdywithme'); ?>">ig</a>
				</div>
				<?php nerdywithme_branding(); ?>
				<div class="site-header__cta">
					<a class="button" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Read NerdyWithMe', 'nerdywithme'); ?></a>
				</div>
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
							'fallback_cb'    => false,
						)
					);
					?>
				</nav>
				<div class="header-tools">
					<button class="header-tools__button search-toggle" type="button" aria-expanded="false" aria-controls="search-panel">⌕</button>
				</div>
			</div>
			<div id="search-panel" class="search-panel">
				<?php get_search_form(); ?>
			</div>
		</div>
	</header>
	<main id="primary" class="site-main">
		<div class="nwm-shell">
