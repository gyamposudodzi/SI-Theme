<?php
/**
 * NerdyWithMe theme functions.
 *
 * @package NerdyWithMe
 */

if (! defined('NERDYWITHME_VERSION')) {
	define('NERDYWITHME_VERSION', '1.0.6');
}

function nerdywithme_setup() {
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 512,
			'width'       => 512,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support('automatic-feed-links');
	add_theme_support('custom-spacing');
	add_theme_support('wp-block-styles');
	add_theme_support('responsive-embeds');
	add_theme_support('editor-styles');

	add_image_size('nwm-hero', 1600, 900, true);
	add_image_size('nwm-hero-mobile', 820, 620, true);
	add_image_size('nwm-card', 900, 650, true);
	add_image_size('nwm-card-compact', 640, 460, true);
	add_image_size('nwm-mini', 600, 420, true);
	add_image_size('nwm-thumb', 320, 320, true);
	add_image_size('nwm-square', 220, 220, true);

	register_nav_menus(
		array(
			'primary'       => __('Primary Menu', 'nerdywithme'),
			'drawer_menu'   => __('Drawer Menu', 'nerdywithme'),
			'footer'        => __('Footer Menu', 'nerdywithme'),
			'footer_col_1'  => __('Footer Column 1', 'nerdywithme'),
			'footer_col_2'  => __('Footer Column 2', 'nerdywithme'),
			'footer_col_3'  => __('Footer Column 3', 'nerdywithme'),
			'footer_col_4'  => __('Footer Column 4', 'nerdywithme'),
			'footer_legal'  => __('Footer Legal Menu', 'nerdywithme'),
		)
	);
}
add_action('after_setup_theme', 'nerdywithme_setup');

function nerdywithme_enqueue_assets() {
	$fonts_url = 'https://fonts.googleapis.com/css2?family=Fredoka:wght@700&family=Outfit:wght@400;500;600;700;800&display=swap';
	$style_file = nerdywithme_get_preferred_asset('/style.css', '/assets/css/theme.min.css');
	$tools_style_file = nerdywithme_get_preferred_asset('/assets/css/tools-page.css', '/assets/css/tools-page.min.css');
	$nav_file    = file_exists(get_template_directory() . '/assets/js/nav-search.min.js') ? '/assets/js/nav-search.min.js' : '/assets/js/nav-search.js';
	$search_file = file_exists(get_template_directory() . '/assets/js/search-modal.min.js') ? '/assets/js/search-modal.min.js' : '/assets/js/search-modal.js';
	$slider_file = file_exists(get_template_directory() . '/assets/js/featured-slider.min.js') ? '/assets/js/featured-slider.min.js' : '/assets/js/featured-slider.js';
	$reading_file = file_exists(get_template_directory() . '/assets/js/reading-bar.min.js') ? '/assets/js/reading-bar.min.js' : '/assets/js/reading-bar.js';
	$toc_file    = file_exists(get_template_directory() . '/assets/js/toc.min.js') ? '/assets/js/toc.min.js' : '/assets/js/toc.js';
	$single_file = file_exists(get_template_directory() . '/assets/js/single-cleanup.min.js') ? '/assets/js/single-cleanup.min.js' : '/assets/js/single-cleanup.js';
	$style_version = file_exists(get_template_directory() . $style_file) ? (string) filemtime(get_template_directory() . $style_file) : NERDYWITHME_VERSION;
	$tools_style_version = file_exists(get_template_directory() . $tools_style_file) ? (string) filemtime(get_template_directory() . $tools_style_file) : NERDYWITHME_VERSION;
	$nav_version    = file_exists(get_template_directory() . $nav_file) ? (string) filemtime(get_template_directory() . $nav_file) : NERDYWITHME_VERSION;
	$search_version = file_exists(get_template_directory() . $search_file) ? (string) filemtime(get_template_directory() . $search_file) : NERDYWITHME_VERSION;
	$slider_version = file_exists(get_template_directory() . $slider_file) ? (string) filemtime(get_template_directory() . $slider_file) : NERDYWITHME_VERSION;
	$reading_version = file_exists(get_template_directory() . $reading_file) ? (string) filemtime(get_template_directory() . $reading_file) : NERDYWITHME_VERSION;
	$toc_version    = file_exists(get_template_directory() . $toc_file) ? (string) filemtime(get_template_directory() . $toc_file) : NERDYWITHME_VERSION;
	$single_version = file_exists(get_template_directory() . $single_file) ? (string) filemtime(get_template_directory() . $single_file) : NERDYWITHME_VERSION;

	wp_enqueue_style('nerdywithme-fonts', esc_url($fonts_url), array(), null);
	wp_enqueue_style('nerdywithme-style', get_template_directory_uri() . $style_file, array('nerdywithme-fonts'), $style_version);
	wp_add_inline_style('nerdywithme-style', nerdywithme_reader_bar_custom_css());
	if (is_page('tools') || get_query_var('nwm_tool')) {
		wp_enqueue_style(
			'nerdywithme-tools-page',
			get_template_directory_uri() . $tools_style_file,
			array('nerdywithme-style'),
			$tools_style_version
		);
	}
	wp_enqueue_script(
		'nerdywithme-nav-search',
		get_template_directory_uri() . $nav_file,
		array(),
		$nav_version,
		true
	);
	wp_script_add_data('nerdywithme-nav-search', 'defer', true);

	if (nerdywithme_has_search_modal()) {
		wp_enqueue_script(
			'nerdywithme-search-modal',
			get_template_directory_uri() . $search_file,
			array(),
			$search_version,
			true
		);
		wp_script_add_data('nerdywithme-search-modal', 'defer', true);
	}

	if (! is_page('tools') && ! is_404() && (is_front_page() || is_home() || is_archive() || is_search() || is_single())) {
		wp_enqueue_script(
			'nerdywithme-featured-slider',
			get_template_directory_uri() . $slider_file,
			array(),
			$slider_version,
			true
		);
		wp_script_add_data('nerdywithme-featured-slider', 'defer', true);
	}

	if (is_single()) {
		wp_enqueue_script(
			'nerdywithme-reading-bar',
			get_template_directory_uri() . $reading_file,
			array(),
			$reading_version,
			true
		);
		wp_script_add_data('nerdywithme-reading-bar', 'defer', true);

		wp_enqueue_script(
			'nerdywithme-toc',
			get_template_directory_uri() . $toc_file,
			array(),
			$toc_version,
			true
		);
		wp_script_add_data('nerdywithme-toc', 'defer', true);

		wp_enqueue_script(
			'nerdywithme-single-cleanup',
			get_template_directory_uri() . $single_file,
			array(),
			$single_version,
			true
		);
		wp_script_add_data('nerdywithme-single-cleanup', 'defer', true);
	}
}
add_action('wp_enqueue_scripts', 'nerdywithme_enqueue_assets');

function nerdywithme_get_preferred_asset($source_file, $minified_file) {
	$source_path   = get_template_directory() . $source_file;
	$minified_path = get_template_directory() . $minified_file;

	if (! file_exists($minified_path)) {
		return $source_file;
	}

	if (! file_exists($source_path)) {
		return $minified_file;
	}

	return filemtime($minified_path) >= filemtime($source_path) ? $minified_file : $source_file;
}

function nerdywithme_trim_frontend_assets() {
	if (is_admin()) {
		return;
	}

	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('wp-block-library-theme');
	wp_dequeue_style('global-styles');
	wp_dequeue_style('classic-theme-styles');
	wp_dequeue_style('wc-block-style');
	wp_dequeue_style('core-block-supports');

	if (! is_user_logged_in()) {
		wp_dequeue_style('dashicons');
	}

	wp_deregister_script('wp-embed');

	if (! is_singular() || ! comments_open() || ! get_option('thread_comments')) {
		wp_dequeue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'nerdywithme_trim_frontend_assets', 100);

function nerdywithme_disable_frontend_overhead() {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	add_filter('emoji_svg_url', '__return_false');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wp_shortlink_wp_head');
	remove_action('wp_head', 'rest_output_link_wp_head');
	remove_action('wp_head', 'wp_oembed_add_discovery_links');
}
add_action('init', 'nerdywithme_disable_frontend_overhead');

function nerdywithme_async_font_stylesheet($html, $handle, $href, $media) {
	if ('nerdywithme-fonts' !== $handle) {
		return $html;
	}

	$media_attr = $media ? $media : 'all';
	$preload    = '<link rel="preload" as="style" href="' . esc_url($href) . '" media="' . esc_attr($media_attr) . '" onload="this.onload=null;this.rel=\'stylesheet\'">';
	$noscript   = '<noscript><link rel="stylesheet" href="' . esc_url($href) . '" media="' . esc_attr($media_attr) . '"></noscript>';

	return $preload . $noscript;
}
add_filter('style_loader_tag', 'nerdywithme_async_font_stylesheet', 10, 4);

function nerdywithme_has_search_modal() {
	return apply_filters('nerdywithme_enable_search_modal', true);
}

function nerdywithme_resource_hints($urls, $relation_type) {
	if ('preconnect' === $relation_type) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}

	return $urls;
}
add_filter('wp_resource_hints', 'nerdywithme_resource_hints', 10, 2);

function nerdywithme_get_home_hero_post_id() {
	static $hero_id = null;

	if (null !== $hero_id) {
		return $hero_id;
	}

	$hero_source_slug = nerdywithme_get_content_source_slug('hero_category', 'featured');
	$hero_query       = $hero_source_slug ? nerdywithme_get_posts_by_category_name($hero_source_slug, 1) : nerdywithme_get_featured_posts(1);
	$hero_id          = 0;

	if (! empty($hero_query->posts)) {
		$hero_id = (int) $hero_query->posts[0]->ID;
	}

	wp_reset_postdata();

	return $hero_id;
}

function nerdywithme_preload_home_hero_image() {
	if (! is_front_page()) {
		return;
	}

	$hero_id = nerdywithme_get_home_hero_post_id();

	if (! $hero_id || ! has_post_thumbnail($hero_id)) {
		return;
	}

	$thumbnail_id = get_post_thumbnail_id($hero_id);
	$image        = wp_get_attachment_image_src($thumbnail_id, 'nwm-hero');
	$mobile_image = wp_get_attachment_image_src($thumbnail_id, 'nwm-hero-mobile');

	if (! $image || empty($image[0])) {
		return;
	}

	$srcset = wp_get_attachment_image_srcset($thumbnail_id, 'nwm-hero');
	$sizes  = nerdywithme_get_image_sizes_hint('home-hero');
	?>
	<?php if ($mobile_image && ! empty($mobile_image[0])) : ?>
		<link rel="preload" as="image" href="<?php echo esc_url($mobile_image[0]); ?>"<?php echo $srcset ? ' imagesrcset="' . esc_attr($srcset) . '"' : ''; ?> imagesizes="<?php echo esc_attr($sizes); ?>" media="(max-width: 700px)" fetchpriority="high">
	<?php endif; ?>
	<link rel="preload" as="image" href="<?php echo esc_url($image[0]); ?>"<?php echo $srcset ? ' imagesrcset="' . esc_attr($srcset) . '"' : ''; ?> imagesizes="<?php echo esc_attr($sizes); ?>" media="(min-width: 701px)" fetchpriority="high">
	<?php
}
add_action('wp_head', 'nerdywithme_preload_home_hero_image', 1);

function nerdywithme_customize_register($wp_customize) {
	$wp_customize->add_section(
		'nerdywithme_theme_options',
		array(
			'title'    => __('NerdyWithMe Theme Options', 'nerdywithme'),
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'nerdywithme_brand_style',
		array(
			'default'           => 'lockup',
			'sanitize_callback' => 'nerdywithme_sanitize_brand_style',
		)
	);

	$wp_customize->add_control(
		'nerdywithme_brand_style',
		array(
			'label'   => __('Brand Style', 'nerdywithme'),
			'section' => 'nerdywithme_theme_options',
			'type'    => 'select',
			'choices' => array(
				'refined' => __('Option 1: Refined Wordmark', 'nerdywithme'),
				'lockup'  => __('Option D: Head-In-Middle Lockup', 'nerdywithme'),
			),
		)
	);

	$options = array(
		'header_cta_label' => array(
			'label'   => __('Header CTA Label', 'nerdywithme'),
			'default' => __('Read NerdyWithMe', 'nerdywithme'),
		),
		'header_cta_url'   => array(
			'label'   => __('Header CTA URL', 'nerdywithme'),
			'default' => home_url('/'),
		),
		'facebook_url'     => array(
			'label'   => __('Facebook URL', 'nerdywithme'),
			'default' => '#',
		),
		'x_url'            => array(
			'label'   => __('X URL', 'nerdywithme'),
			'default' => '#',
		),
		'instagram_url'    => array(
			'label'   => __('Instagram URL', 'nerdywithme'),
			'default' => '#',
		),
		'footer_blurb'     => array(
			'label'   => __('Footer Blurb', 'nerdywithme'),
			'default' => __('Where trading meets technology: practical market education, automation, AI, and tools for modern traders.', 'nerdywithme'),
		),
	);

	foreach ($options as $id => $option) {
		$wp_customize->add_setting(
			'nerdywithme_' . $id,
			array(
				'default'           => $option['default'],
				'sanitize_callback' => ('footer_blurb' === $id || 'header_cta_label' === $id) ? 'sanitize_text_field' : 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			'nerdywithme_' . $id,
			array(
				'label'   => $option['label'],
				'section' => 'nerdywithme_theme_options',
				'type'    => ('footer_blurb' === $id || 'header_cta_label' === $id) ? 'text' : 'url',
			)
		);
	}

	$social_link_defaults = array(
		array('platform' => 'facebook', 'url' => '#'),
		array('platform' => 'x', 'url' => '#'),
		array('platform' => 'instagram', 'url' => '#'),
	);

	for ($i = 1; $i <= 3; $i++) {
		$default = $social_link_defaults[ $i - 1 ];

		$wp_customize->add_setting(
			'nerdywithme_social_link_' . $i . '_platform',
			array(
				'default'           => $default['platform'],
				'sanitize_callback' => 'nerdywithme_sanitize_social_platform',
			)
		);
		$wp_customize->add_control(
			'nerdywithme_social_link_' . $i . '_platform',
			array(
				'label'   => sprintf(__('Social Link %d Platform', 'nerdywithme'), $i),
				'section' => 'nerdywithme_theme_options',
				'type'    => 'select',
				'choices' => nerdywithme_social_platform_choices(),
			)
		);

		$wp_customize->add_setting(
			'nerdywithme_social_link_' . $i . '_url',
			array(
				'default'           => $default['url'],
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			'nerdywithme_social_link_' . $i . '_url',
			array(
				'label'   => sprintf(__('Social Link %d URL', 'nerdywithme'), $i),
				'section' => 'nerdywithme_theme_options',
				'type'    => 'url',
			)
		);
	}

	$wp_customize->add_section(
		'nerdywithme_home_social_cards',
		array(
			'title'    => __('Homepage Social Cards', 'nerdywithme'),
			'priority' => 31,
		)
	);

	$wp_customize->add_section(
		'nerdywithme_sidebar_social_cards',
		array(
			'title'    => __('Sidebar Social Cards', 'nerdywithme'),
			'priority' => 32,
		)
	);

	$wp_customize->add_section(
		'nerdywithme_sidebar_profile_card',
		array(
			'title'    => __('Sidebar Follow Card', 'nerdywithme'),
			'priority' => 33,
		)
	);

	$wp_customize->add_section(
		'nerdywithme_content_sources',
		array(
			'title'    => __('Curated Content Sources', 'nerdywithme'),
			'priority' => 34,
		)
	);

	$wp_customize->add_section(
		'nerdywithme_reader_bar',
		array(
			'title'    => __('Reader Bar', 'nerdywithme'),
			'priority' => 35,
		)
	);

	$content_sources = array(
		'hero_category'            => array(
			'label'   => __('Homepage Hero Category Slug', 'nerdywithme'),
			'default' => 'featured',
		),
		'editors_pick_category'    => array(
			'label'   => __('Editors Pick Category Slug', 'nerdywithme'),
			'default' => 'editors-pick',
		),
		'top_picks_category'       => array(
			'label'   => __('Sidebar Top Picks Category Slug', 'nerdywithme'),
			'default' => '',
		),
		'sidebar_featured_category' => array(
			'label'   => __('Sidebar Featured Category Slug', 'nerdywithme'),
			'default' => 'featured',
		),
		'search_recommended_category' => array(
			'label'   => __('Search Modal Recommended Category Slug', 'nerdywithme'),
			'default' => '',
		),
		'drawer_posts_category'    => array(
			'label'   => __('Drawer Latest Posts Category Slug', 'nerdywithme'),
			'default' => '',
		),
		'read_next_category'       => array(
			'label'   => __('Single Post Read Next Category Slug', 'nerdywithme'),
			'default' => '',
		),
		'404_suggestions_category' => array(
			'label'   => __('404 Suggestions Category Slug', 'nerdywithme'),
			'default' => '',
		),
	);

	foreach ($content_sources as $id => $source) {
		$wp_customize->add_setting(
			'nerdywithme_' . $id,
			array(
				'default'           => $source['default'],
				'sanitize_callback' => 'sanitize_title',
			)
		);

		$wp_customize->add_control(
			'nerdywithme_' . $id,
			array(
				'label'       => $source['label'],
				'description' => __('Leave blank to fall back to recent posts.', 'nerdywithme'),
				'section'     => 'nerdywithme_content_sources',
				'type'        => 'text',
			)
		);
	}

	$card_sections = array(
		'home'    => array(
			'section' => 'nerdywithme_home_social_cards',
			'count'   => 4,
			'defaults' => nerdywithme_default_home_social_cards(),
		),
		'sidebar' => array(
			'section' => 'nerdywithme_sidebar_social_cards',
			'count'   => 3,
			'defaults' => nerdywithme_default_sidebar_social_cards(),
		),
	);

	foreach ($card_sections as $prefix => $config) {
		for ($i = 1; $i <= $config['count']; $i++) {
			$defaults = $config['defaults'][ $i - 1 ];
			$base     = 'nerdywithme_' . $prefix . '_social_' . $i . '_';

			$wp_customize->add_setting(
				$base . 'tone',
				array(
					'default'           => $defaults['tone'],
					'sanitize_callback' => 'nerdywithme_sanitize_card_tone',
				)
			);
			$wp_customize->add_control(
				$base . 'tone',
				array(
					'label'   => sprintf(__('Card %d Color', 'nerdywithme'), $i),
					'section' => $config['section'],
					'type'    => 'select',
					'choices' => nerdywithme_card_tone_choices(),
				)
			);

			$text_fields = array(
				'platform' => __('Platform', 'nerdywithme'),
				'icon_text' => __('Fallback Icon Text', 'nerdywithme'),
				'handle'    => __('Handle', 'nerdywithme'),
				'description' => __('Description', 'nerdywithme'),
			);

			foreach ($text_fields as $field => $label) {
				$wp_customize->add_setting(
					$base . $field,
					array(
						'default'           => $defaults[ $field ],
						'sanitize_callback' => 'platform' === $field ? 'nerdywithme_sanitize_social_platform' : 'sanitize_text_field',
					)
				);
				$wp_customize->add_control(
					$base . $field,
					array(
						'label'   => sprintf(__('Card %1$d %2$s', 'nerdywithme'), $i, $label),
						'section' => $config['section'],
						'type'    => 'platform' === $field ? 'select' : 'text',
						'choices' => 'platform' === $field ? nerdywithme_social_platform_choices() : array(),
					)
				);
			}

			$wp_customize->add_setting(
				$base . 'url',
				array(
					'default'           => $defaults['url'],
					'sanitize_callback' => 'esc_url_raw',
				)
			);
			$wp_customize->add_control(
				$base . 'url',
				array(
					'label'   => sprintf(__('Card %d Link URL', 'nerdywithme'), $i),
					'section' => $config['section'],
					'type'    => 'url',
				)
			);

			$wp_customize->add_setting(
				$base . 'icon_image',
				array(
					'default'           => 0,
					'sanitize_callback' => 'absint',
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					$base . 'icon_image',
					array(
						'label'   => sprintf(__('Card %d Logo/Image', 'nerdywithme'), $i),
						'section' => $config['section'],
					)
				)
			);
		}
	}

	$profile_defaults = array(
		'handle'       => '@nerdywithme',
		'followers'    => __('127K followers', 'nerdywithme'),
		'button_label' => __('Follow', 'nerdywithme'),
		'button_url'   => '#',
	);

	foreach ($profile_defaults as $field => $default) {
		$wp_customize->add_setting(
			'nerdywithme_profile_' . $field,
			array(
				'default'           => $default,
				'sanitize_callback' => ('button_url' === $field) ? 'esc_url_raw' : 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'nerdywithme_profile_' . $field,
			array(
				'label'   => ucwords(str_replace('_', ' ', __('Profile ' . $field, 'nerdywithme'))),
				'section' => 'nerdywithme_sidebar_profile_card',
				'type'    => ('button_url' === $field) ? 'url' : 'text',
			)
		);
	}

	for ($i = 1; $i <= 3; $i++) {
		$wp_customize->add_setting(
			'nerdywithme_profile_stack_image_' . $i,
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'nerdywithme_profile_stack_image_' . $i,
				array(
					'label'   => sprintf(__('Follow Card Image %d', 'nerdywithme'), $i),
					'section' => 'nerdywithme_sidebar_profile_card',
				)
			)
		);
	}

	$reader_bar_colors = array(
		'reader_bar_bg'             => array(
			'label'   => __('Reader Bar Background', 'nerdywithme'),
			'default' => '#ffffff',
		),
		'reader_bar_text'           => array(
			'label'   => __('Reader Bar Text', 'nerdywithme'),
			'default' => '#132a78',
		),
		'reader_bar_accent'         => array(
			'label'   => __('Reader Bar Accent', 'nerdywithme'),
			'default' => '#ff5f36',
		),
		'reader_bar_progress_start' => array(
			'label'   => __('Progress Start Color', 'nerdywithme'),
			'default' => '#ff5f36',
		),
		'reader_bar_progress_end'   => array(
			'label'   => __('Progress End Color', 'nerdywithme'),
			'default' => '#ff6aa2',
		),
		'reader_bar_progress_track' => array(
			'label'   => __('Progress Track Color', 'nerdywithme'),
			'default' => '#eef1f7',
		),
	);

	foreach ($reader_bar_colors as $id => $config) {
		$wp_customize->add_setting(
			'nerdywithme_' . $id,
			array(
				'default'           => $config['default'],
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'nerdywithme_' . $id,
				array(
					'label'   => $config['label'],
					'section' => 'nerdywithme_reader_bar',
				)
			)
		);
	}

	$wp_customize->add_setting(
		'nerdywithme_reader_bar_size',
		array(
			'default'           => 'standard',
			'sanitize_callback' => 'nerdywithme_sanitize_reader_bar_size',
		)
	);

	$wp_customize->add_control(
		'nerdywithme_reader_bar_size',
		array(
			'label'   => __('Reader Bar Size', 'nerdywithme'),
			'section' => 'nerdywithme_reader_bar',
			'type'    => 'select',
			'choices' => array(
				'compact'  => __('Compact', 'nerdywithme'),
				'standard' => __('Standard', 'nerdywithme'),
				'large'    => __('Large', 'nerdywithme'),
			),
		)
	);

	$wp_customize->add_setting(
		'nerdywithme_reader_bar_scope',
		array(
			'default'           => 'full',
			'sanitize_callback' => 'nerdywithme_sanitize_reader_bar_scope',
		)
	);

	$wp_customize->add_control(
		'nerdywithme_reader_bar_scope',
		array(
			'label'   => __('Reader Bar Width', 'nerdywithme'),
			'section' => 'nerdywithme_reader_bar',
			'type'    => 'select',
			'choices' => array(
				'full'    => __('Full Width', 'nerdywithme'),
				'content' => __('Match Content Width', 'nerdywithme'),
			),
		)
	);
}
add_action('customize_register', 'nerdywithme_customize_register');

/**
 * Prevent WordPress from canonical-redirecting missing URLs to the homepage.
 *
 * This keeps genuine missing routes on the 404 template instead of letting
 * local preview/server quirks bounce them somewhere "close enough".
 */
function nerdywithme_preserve_404_requests($redirect_url, $requested_url) {
	if (is_404()) {
		return false;
	}

	return $redirect_url;
}
add_filter('redirect_canonical', 'nerdywithme_preserve_404_requests', 10, 2);

function nerdywithme_increase_upload_limit($size) {
	$limit = 32 * 1024 * 1024;
	return $limit;
}
add_filter('upload_size_limit', 'nerdywithme_increase_upload_limit');

function nerdywithme_sanitize_brand_style($value) {
	$allowed = array('refined', 'lockup');
	return in_array($value, $allowed, true) ? $value : 'refined';
}

function nerdywithme_sanitize_reader_bar_size($value) {
	$allowed = array('compact', 'standard', 'large');
	return in_array($value, $allowed, true) ? $value : 'standard';
}

function nerdywithme_sanitize_reader_bar_scope($value) {
	$allowed = array('full', 'content');
	return in_array($value, $allowed, true) ? $value : 'full';
}

function nerdywithme_reader_bar_body_class($classes) {
	$scope = nerdywithme_sanitize_reader_bar_scope(get_theme_mod('nerdywithme_reader_bar_scope', 'full'));
	if ('content' === $scope) {
		$classes[] = 'reader-bar--content';
	}
	return $classes;
}
add_filter('body_class', 'nerdywithme_reader_bar_body_class');

function nerdywithme_social_platform_choices() {
	return array(
		'custom'     => __('Custom', 'nerdywithme'),
		'facebook'   => __('Facebook', 'nerdywithme'),
		'x'          => __('X (Twitter)', 'nerdywithme'),
		'instagram'  => __('Instagram', 'nerdywithme'),
		'youtube'    => __('YouTube', 'nerdywithme'),
		'tiktok'     => __('TikTok', 'nerdywithme'),
		'discord'    => __('Discord', 'nerdywithme'),
		'telegram'   => __('Telegram', 'nerdywithme'),
		'linkedin'   => __('LinkedIn', 'nerdywithme'),
		'github'     => __('GitHub', 'nerdywithme'),
		'reddit'     => __('Reddit', 'nerdywithme'),
		'tradingview' => __('TradingView', 'nerdywithme'),
		'whatsapp'   => __('WhatsApp', 'nerdywithme'),
		'snapchat'   => __('Snapchat', 'nerdywithme'),
		'threads'    => __('Threads', 'nerdywithme'),
		'twitch'     => __('Twitch', 'nerdywithme'),
		'newsletter' => __('Newsletter', 'nerdywithme'),
		'website'    => __('Website', 'nerdywithme'),
	);
}

function nerdywithme_sanitize_social_platform($value) {
	$allowed = array_keys(nerdywithme_social_platform_choices());
	return in_array($value, $allowed, true) ? $value : 'custom';
}

function nerdywithme_social_platform_meta($platform) {
	$map = array(
		'facebook'    => array('label' => 'Facebook', 'icon' => 'f'),
		'x'           => array('label' => 'X', 'icon' => 'x'),
		'instagram'   => array('label' => 'Instagram', 'icon' => 'ig'),
		'youtube'     => array('label' => 'YouTube', 'icon' => 'yt'),
		'tiktok'      => array('label' => 'TikTok', 'icon' => 'tt'),
		'discord'     => array('label' => 'Discord', 'icon' => 'dc'),
		'telegram'    => array('label' => 'Telegram', 'icon' => 'tg'),
		'linkedin'    => array('label' => 'LinkedIn', 'icon' => 'in'),
		'github'      => array('label' => 'GitHub', 'icon' => 'gh'),
		'reddit'      => array('label' => 'Reddit', 'icon' => 'rd'),
		'tradingview' => array('label' => 'TradingView', 'icon' => 'tv'),
		'whatsapp'    => array('label' => 'WhatsApp', 'icon' => 'wa'),
		'snapchat'    => array('label' => 'Snapchat', 'icon' => 'sc'),
		'threads'     => array('label' => 'Threads', 'icon' => 'th'),
		'twitch'      => array('label' => 'Twitch', 'icon' => 'tw'),
		'newsletter'  => array('label' => 'Newsletter', 'icon' => 'nl'),
		'website'     => array('label' => 'Website', 'icon' => 'ww'),
	);

	return $map[ $platform ] ?? array('label' => 'Custom', 'icon' => '');
}

function nerdywithme_social_icon_svg($platform, $fallback = '') {
	$icons = array(
		'facebook'    => '<path d="M14.5 8.3h2.1V5.1c-.4-.1-1.6-.2-3-.2-3 0-5 1.8-5 5.2v2.9H5.3v3.6h3.3v8.5h4v-8.5h3.3l.5-3.6h-3.8v-2.5c0-1 .3-1.7 1.9-1.7Z"/>',
		'x'           => '<path d="M5.1 5h4.5l3.6 5.1L17.6 5h4.1l-6.5 7.4 7.1 10.1h-4.5l-4-5.7-5 5.7H4.7l7.1-8.1L5.1 5Zm2.4 1.8 11.2 14h1.1L8.6 6.8H7.5Z"/>',
		'instagram'   => '<path d="M8.1 4.8h7.8c2.3 0 4.2 1.9 4.2 4.2v7.8c0 2.3-1.9 4.2-4.2 4.2H8.1c-2.3 0-4.2-1.9-4.2-4.2V9c0-2.3 1.9-4.2 4.2-4.2Zm0 1.8c-1.3 0-2.4 1.1-2.4 2.4v7.8c0 1.3 1.1 2.4 2.4 2.4h7.8c1.3 0 2.4-1.1 2.4-2.4V9c0-1.3-1.1-2.4-2.4-2.4H8.1Zm3.9 3a4.3 4.3 0 1 1 0 8.6 4.3 4.3 0 0 1 0-8.6Zm0 1.8a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Zm4.5-2.2a1.1 1.1 0 1 1-2.2 0 1.1 1.1 0 0 1 2.2 0Z"/>',
		'youtube'     => '<path d="M21.5 8.1s-.2-1.6-.9-2.3c-.9-.9-1.9-.9-2.3-1C15 4.5 12 4.5 12 4.5s-3 0-6.3.3c-.5.1-1.5.1-2.3 1-.7.7-.9 2.3-.9 2.3s-.3 1.9-.3 3.9v1.8c0 1.9.3 3.9.3 3.9s.2 1.6.9 2.3c.8.9 2 .9 2.5 1 1.8.2 6.1.3 6.1.3s3 0 6.3-.3c.5-.1 1.5-.1 2.3-1 .7-.7.9-2.3.9-2.3s.3-1.9.3-3.9V12c0-2-.3-3.9-.3-3.9ZM9.8 15.7V8.9l6.4 3.4-6.4 3.4Z"/>',
		'tiktok'      => '<path d="M14.5 4.5c.3 2.3 1.6 3.7 3.8 3.9v3.2c-1.3.1-2.6-.3-3.8-1v5.9c0 7.5-8.2 9.8-11.5 4.5-2.1-3.4-.8-9.4 5.9-9.6v3.4c-.4.1-.8.1-1.2.3-1.2.4-1.9 1.4-1.7 2.7.4 2.4 4.8 3.1 4.4-1.6V4.5h4.1Z"/>',
		'discord'     => '<path d="M19.8 6.6a16.6 16.6 0 0 0-4.1-1.3l-.5 1.1a15.6 15.6 0 0 0-4.6 0l-.5-1.1c-1.5.2-2.8.7-4.1 1.3-2.6 3.9-3.3 7.7-2.9 11.5a16.8 16.8 0 0 0 5 2.5l.9-1.5c-.5-.2-1-.5-1.5-.8l.4-.3a11.9 11.9 0 0 0 10.2 0l.4.3c-.5.3-1 .6-1.5.8l.9 1.5a16.8 16.8 0 0 0 5-2.5c.5-4.4-.8-8.1-3.1-11.5ZM9.4 15.7c-1 0-1.8-.9-1.8-2s.8-2 1.8-2 1.8.9 1.8 2-.8 2-1.8 2Zm5.2 0c-1 0-1.8-.9-1.8-2s.8-2 1.8-2 1.8.9 1.8 2-.8 2-1.8 2Z"/>',
		'telegram'    => '<path d="m21.7 4.6-3.2 15.1c-.2 1.1-.9 1.3-1.8.8l-4.9-3.6-2.4 2.3c-.3.3-.5.5-1 .5l.4-5 9.1-8.2c.4-.4-.1-.6-.6-.3L6 13.3l-4.8-1.5c-1-.3-1-1 .2-1.5L20.1 3c.9-.3 1.7.2 1.6 1.6Z"/>',
		'linkedin'    => '<path d="M6.5 8.7H3.3v12h3.2v-12ZM4.9 7.1a1.9 1.9 0 1 0 0-3.8 1.9 1.9 0 0 0 0 3.8Zm15.8 6.7c0-3.2-1.7-5.3-4.5-5.3-2 0-2.9 1.1-3.4 1.9V8.7H9.6v12h3.2v-6.6c0-1.7.9-2.8 2.4-2.8s2.3 1 2.3 2.8v6.6h3.2v-6.9Z"/>',
		'github'      => '<path d="M12 3.5a8.7 8.7 0 0 0-2.8 17c.4.1.6-.2.6-.4v-1.7c-2.5.5-3-1.1-3-1.1-.4-1-.9-1.3-.9-1.3-.8-.5.1-.5.1-.5.9.1 1.4.9 1.4.9.8 1.4 2.1 1 2.5.8.1-.6.3-1 .6-1.2-2-.2-4.1-1-4.1-4.3 0-1 .4-1.8.9-2.4-.1-.2-.4-1.1.1-2.4 0 0 .8-.2 2.5.9.7-.2 1.4-.3 2.2-.3.7 0 1.5.1 2.2.3 1.7-1.1 2.5-.9 2.5-.9.5 1.3.2 2.2.1 2.4.6.6.9 1.4.9 2.4 0 3.3-2.1 4.1-4.1 4.3.3.3.6.8.6 1.7v2.4c0 .2.2.5.6.4A8.7 8.7 0 0 0 12 3.5Z"/>',
		'reddit'      => '<path d="M20.8 11.2c-.7 0-1.3.3-1.7.8-1.7-1.1-4-1.8-6.5-1.9l1.1-3.4 2.9.7a1.9 1.9 0 1 0 .3-1.2l-3.4-.8c-.3-.1-.6.1-.7.4l-1.3 4.2c-2.6 0-5 .7-6.8 1.9a2.2 2.2 0 1 0-2.4 3.6v.5c0 3.3 4.4 6 9.8 6s9.8-2.7 9.8-6v-.5a2.2 2.2 0 0 0-1.1-4.1ZM8.5 14.8a1.3 1.3 0 1 1 0 2.6 1.3 1.3 0 0 1 0-2.6Zm6.9 4.2c-1.4 1-5.4 1-6.8 0-.3-.2-.3-.6-.1-.8.2-.3.6-.3.8-.1 1 .7 4.3.7 5.3 0 .3-.2.6-.2.8.1.2.3.2.6-.1.8Zm.1-1.6a1.3 1.3 0 1 1 0-2.6 1.3 1.3 0 0 1 0 2.6Z"/>',
		'tradingview' => '<path d="M4 7.4h5.1v2.1H7.6v7.1H5.5V9.5H4V7.4Zm6.1 0h2.2l2.1 5.7 2.1-5.7h2.2l-3.5 9.2h-1.6l-3.5-9.2Zm8.2 6.9h2v2.3h-2v-2.3Z"/>',
		'whatsapp'    => '<path d="M12 3.7a8.1 8.1 0 0 0-7 12.2L4 20.5l4.7-1.2A8.1 8.1 0 1 0 12 3.7Zm0 1.7a6.4 6.4 0 0 1 5.5 9.7 6.4 6.4 0 0 1-7.9 2.4l-.3-.1-2.8.7.7-2.7-.2-.3A6.4 6.4 0 0 1 12 5.4Zm-2.6 3.2c-.1 0-.3 0-.5.2-.2.2-.7.7-.7 1.8s.8 2.1.9 2.3c.1.1 1.6 2.6 4 3.5 2 .8 2.4.6 2.9.6.4 0 1.3-.5 1.5-1 .2-.5.2-.9.1-1-.1-.1-.2-.2-.5-.3l-1.6-.8c-.2-.1-.4-.1-.5.1l-.7.9c-.1.2-.3.2-.5.1-.3-.1-1.1-.4-2-1.2-.7-.7-1.2-1.5-1.4-1.8-.1-.2 0-.4.1-.5l.4-.5c.1-.1.2-.3.3-.4.1-.2 0-.3 0-.5l-.7-1.6c-.2-.4-.3-.4-.5-.4h-.5Z"/>',
		'snapchat'   => '<path d="M12 3.6c-2.5 0-4.5 1.8-4.5 4.2v2.8c0 .5-.2.9-.7 1.1l-1.5.7c-.5.2-.5.9 0 1.1.8.4 1.4.6 1.9.7.1.7.5 1.1 1.2 1.2.4.1.8.2 1 .6.6.8 1.4 1.4 2.6 1.4s2-.6 2.6-1.4c.3-.4.6-.5 1-.6.7-.1 1.1-.5 1.2-1.2.5-.1 1.1-.3 1.9-.7.5-.2.5-.9 0-1.1l-1.5-.7c-.5-.2-.7-.6-.7-1.1V7.8c0-2.4-2-4.2-4.5-4.2Z"/>',
		'threads'    => '<path d="M12.2 3.8c4.4 0 7.3 2.8 7.3 7.8 0 4.9-2.8 8.6-7.5 8.6-4.1 0-7.4-2.7-7.4-7.4 0-4.7 3.2-7.4 7.2-7.4 3 0 5.1 1.6 5.8 4.5l-2 .5c-.5-2.1-1.8-3.1-3.9-3.1-2.8 0-5 1.9-5 5.4 0 3.4 2.3 5.4 5.3 5.4 3.2 0 5.3-2.5 5.3-6.6 0-3.8-2.1-6.1-5.3-6.1-1.9 0-3.3.7-4.4 2l-1.5-1.3c1.5-1.8 3.5-2.8 6.1-2.8Zm-.4 6.4c2.2 0 3.8 1.1 3.8 3.1 0 2.1-1.8 3.4-4 3.4-2.1 0-3.7-1.1-3.7-2.9 0-1.8 1.6-2.9 3.9-2.9.5 0 1.1.1 1.6.2-.3-.7-.9-1-1.8-1-1 0-1.7.3-2.4.9l-1.1-1.4c1-.9 2.2-1.4 3.7-1.4Zm-.1 2.3c-1.1 0-1.7.4-1.7 1.1 0 .8.7 1.2 1.7 1.2 1.1 0 1.8-.5 1.8-1.3 0-.6-.6-1-1.8-1Z"/>',
		'twitch'     => '<path d="M5.5 4.5 4.2 8v11.8h4.1V22h2.3l2.2-2.2h3.4l4.6-4.6V4.5H5.5Zm13.2 9.7-2.7 2.7h-3.7l-2.2 2.2v-2.2H7.4V6.6h11.3v7.6Zm-2.2-5.3h-1.8v5.1h1.8V8.9Zm-4.9 0H9.8v5.1h1.8V8.9Z"/>',
		'newsletter' => '<path d="M4 6.5h16c1.1 0 2 .9 2 2v9c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2v-9c0-1.1.9-2 2-2Zm0 2v.4l8 5 8-5v-.4H4Zm16 2.8-7.5 4.7a1 1 0 0 1-1 0L4 11.3v6.2h16v-6.2Z"/>',
		'website'    => '<path d="M12 3.8a8.2 8.2 0 1 0 0 16.4 8.2 8.2 0 0 0 0-16.4Zm5.8 7.2h-3.1a13.3 13.3 0 0 0-1.1-4.7A6.4 6.4 0 0 1 17.8 11Zm-5.8-5c.5.7 1.1 2.2 1.3 5h-2.6c.2-2.8.8-4.3 1.3-5ZM5.9 13h3.2c.1 1.8.5 3.4 1.1 4.7A6.4 6.4 0 0 1 5.9 13Zm3.2-2H5.9a6.4 6.4 0 0 1 4.3-4.7A13.3 13.3 0 0 0 9.1 11Zm2.9 7c-.5-.7-1.1-2.2-1.3-5h2.6c-.2 2.8-.8 4.3-1.3 5Zm1.6-.3c.6-1.3 1-2.9 1.1-4.7h3.1a6.4 6.4 0 0 1-4.2 4.7Z"/>',
	);

	$path = $icons[ $platform ] ?? '';

	if (! $path && $fallback) {
		return '<span class="social-icon social-icon--text" aria-hidden="true">' . esc_html($fallback) . '</span>';
	}

	if (! $path) {
		return '<span class="social-icon social-icon--text" aria-hidden="true">•</span>';
	}

	return '<svg class="social-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $path . '</svg>';
}

function nerdywithme_card_tone_choices() {
	return array(
		'green'  => __('Green', 'nerdywithme'),
		'pink'   => __('Pink', 'nerdywithme'),
		'orange' => __('Orange', 'nerdywithme'),
		'blue'   => __('Blue', 'nerdywithme'),
	);
}

function nerdywithme_sanitize_card_tone($value) {
	$allowed = array_keys(nerdywithme_card_tone_choices());
	return in_array($value, $allowed, true) ? $value : 'green';
}

function nerdywithme_default_home_social_cards() {
	return array(
		array(
			'platform'    => 'tradingview',
			'tone'        => 'green',
			'icon_text'   => 'TV',
			'handle'      => '@nerdywithme',
			'description' => __('Listen on TradingView', 'nerdywithme'),
			'url'         => '#',
		),
		array(
			'platform'    => 'x',
			'tone'        => 'pink',
			'icon_text'   => 'X',
			'handle'      => '@nerdywithme',
			'description' => __('Follow on X', 'nerdywithme'),
			'url'         => '#',
		),
		array(
			'platform'    => 'instagram',
			'tone'        => 'orange',
			'icon_text'   => 'IG',
			'handle'      => '@nerdywithme',
			'description' => __('Find me on Instagram', 'nerdywithme'),
			'url'         => '#',
		),
		array(
			'platform'    => 'github',
			'tone'        => 'blue',
			'icon_text'   => 'GH',
			'handle'      => '@nerdywithme',
			'description' => __('Builds on GitHub', 'nerdywithme'),
			'url'         => '#',
		),
	);
}

function nerdywithme_default_sidebar_social_cards() {
	return array(
		array(
			'platform'    => 'tradingview',
			'tone'        => 'green',
			'icon_text'   => 'TV',
			'handle'      => '@nerdywithme',
			'description' => __('Track setups in TradingView', 'nerdywithme'),
			'url'         => '#',
		),
		array(
			'platform'    => 'x',
			'tone'        => 'pink',
			'icon_text'   => 'X',
			'handle'      => '@nerdywithme',
			'description' => __('Follow market notes on X', 'nerdywithme'),
			'url'         => '#',
		),
		array(
			'platform'    => 'github',
			'tone'        => 'blue',
			'icon_text'   => 'GH',
			'handle'      => '@nerdywithme',
			'description' => __('See bots and build logs', 'nerdywithme'),
			'url'         => '#',
		),
	);
}

function nerdywithme_get_social_cards($context = 'home') {
	$defaults = ('sidebar' === $context) ? nerdywithme_default_sidebar_social_cards() : nerdywithme_default_home_social_cards();
	$count    = ('sidebar' === $context) ? 3 : 4;
	$cards    = array();

	for ($i = 1; $i <= $count; $i++) {
		$base    = 'nerdywithme_' . $context . '_social_' . $i . '_';
		$default = $defaults[ $i - 1 ];
		$image_id = absint(get_theme_mod($base . 'icon_image', 0));
		$platform = nerdywithme_sanitize_social_platform(get_theme_mod($base . 'platform', $default['platform']));
		$platform_meta = nerdywithme_social_platform_meta($platform);
		$icon_text = sanitize_text_field(get_theme_mod($base . 'icon_text', $default['icon_text']));
		if ('custom' !== $platform) {
			$icon_text = $platform_meta['icon'] ?: $icon_text;
		}

		$cards[] = array(
			'platform'    => $platform,
			'tone'        => nerdywithme_sanitize_card_tone(get_theme_mod($base . 'tone', $default['tone'])),
			'icon_text'   => $icon_text,
			'handle'      => sanitize_text_field(get_theme_mod($base . 'handle', $default['handle'])),
			'description' => sanitize_text_field(get_theme_mod($base . 'description', $default['description'])),
			'url'         => esc_url(get_theme_mod($base . 'url', $default['url'])),
			'icon_image'  => $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '',
			'label'       => $platform_meta['label'] ?: __('Custom', 'nerdywithme'),
		);
	}

	return $cards;
}

function nerdywithme_render_card_icon($card, $icon_class) {
	if (! empty($card['icon_image'])) {
		return '<span class="' . esc_attr($icon_class) . '"><img src="' . esc_url($card['icon_image']) . '" alt="" loading="lazy" decoding="async"></span>';
	}

	return '<span class="' . esc_attr($icon_class) . '">' . nerdywithme_social_icon_svg($card['platform'] ?? 'custom', $card['icon_text'] ?? '') . '</span>';
}

function nerdywithme_get_profile_card_settings() {
	return array(
		'handle'       => sanitize_text_field(get_theme_mod('nerdywithme_profile_handle', '@nerdywithme')),
		'followers'    => sanitize_text_field(get_theme_mod('nerdywithme_profile_followers', __('127K followers', 'nerdywithme'))),
		'button_label' => sanitize_text_field(get_theme_mod('nerdywithme_profile_button_label', __('Follow', 'nerdywithme'))),
		'button_url'   => esc_url(get_theme_mod('nerdywithme_profile_button_url', '#')),
	);
}

function nerdywithme_get_profile_stack_urls() {
	$urls = array();

	for ($i = 1; $i <= 3; $i++) {
		$image_id = absint(get_theme_mod('nerdywithme_profile_stack_image_' . $i, 0));
		if ($image_id) {
			$url = wp_get_attachment_image_url($image_id, 'nwm-card');
			if ($url) {
				$urls[] = $url;
			}
		}
	}

	if (count($urls) < 3) {
		$fallback_query = nerdywithme_get_featured_posts(3);
		if ($fallback_query->have_posts()) {
			while ($fallback_query->have_posts()) {
				$fallback_query->the_post();
				$urls[] = nerdywithme_get_post_image(get_the_ID(), 'nwm-card');
				if (count($urls) >= 3) {
					break;
				}
			}
			wp_reset_postdata();
		}
	}

	while (count($urls) < 3) {
		$urls[] = nerdywithme_fallback_image();
	}

	return array_slice($urls, 0, 3);
}

function nerdywithme_fallback_image() {
	return 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 900"><rect fill="#dff3ff" width="1200" height="900"/><circle fill="#ffe0d1" cx="290" cy="210" r="180"/><circle fill="#ddf8b8" cx="930" cy="180" r="150"/><circle fill="#ffd6e8" cx="920" cy="720" r="210"/><rect fill="#132a78" x="145" y="560" width="360" height="64" rx="32"/><rect fill="#ff5f36" x="145" y="660" width="590" height="64" rx="32"/><rect fill="#ffffff" x="145" y="350" width="720" height="140" rx="36"/><text x="175" y="444" fill="#132a78" font-family="Arial, sans-serif" font-size="74" font-weight="700">NerdyWithMe</text></svg>');
}

function nerdywithme_get_post_image($post_id, $size = 'large') {
	if ($post_id && has_post_thumbnail($post_id)) {
		return get_the_post_thumbnail_url($post_id, $size);
	}

	return nerdywithme_fallback_image();
}

function nerdywithme_get_image_sizes_hint($context) {
	$map = array(
		'home-hero'       => '(max-width: 700px) 100vw, (max-width: 1100px) 100vw, 62vw',
		'single-hero'     => '(max-width: 820px) 100vw, (max-width: 1200px) 92vw, 760px',
		'standard-card'   => '(max-width: 560px) 100vw, (max-width: 1100px) 50vw, 560px',
		'compact-card'    => '(max-width: 560px) 100vw, (max-width: 1100px) 50vw, 272px',
		'row-card'        => '(max-width: 680px) 100vw, (max-width: 1100px) 45vw, 320px',
		'related-card'    => '(max-width: 680px) 100vw, (max-width: 1100px) 50vw, 320px',
		'category-card'   => '(max-width: 680px) 100vw, (max-width: 1100px) 50vw, 260px',
		'featured-slider' => '(max-width: 680px) 100vw, 240px',
		'mini-card'       => '(max-width: 680px) 100vw, (max-width: 1100px) 50vw, 260px',
		'search-panel'    => '(max-width: 680px) 84vw, (max-width: 1100px) 40vw, 260px',
		'mega-thumb'      => '84px',
		'list-thumb'      => '(max-width: 680px) 104px, 18vw',
		'ranked-thumb'    => '104px',
		'compact-thumb'   => '90px',
		'reading-thumb'   => '44px',
	);

	return $map[ $context ] ?? '';
}

function nerdywithme_get_post_image_tag($post_id, $size = 'large', $attrs = array(), $sizes = '') {
	$classes = array('nwm-img');
	$size_map = array(
		'nwm-hero'   => array(1600, 900),
		'nwm-hero-mobile' => array(820, 620),
		'nwm-card'   => array(900, 650),
		'nwm-card-compact' => array(640, 460),
		'nwm-mini'   => array(600, 420),
		'nwm-thumb'  => array(320, 320),
		'nwm-square' => array(220, 220),
		'large'      => array(1024, 1024),
		'medium_large' => array(768, 768),
		'medium'     => array(300, 300),
		'thumbnail'  => array(150, 150),
	);

	if ($post_id && has_post_thumbnail($post_id)) {
		$defaults = array(
			'class'   => implode(' ', $classes),
			'loading' => 'lazy',
			'decoding' => 'async',
		);
		if (! empty($size_map[ $size ])) {
			$defaults['width']  = $size_map[ $size ][0];
			$defaults['height'] = $size_map[ $size ][1];
		}
		$attrs = wp_parse_args($attrs, $defaults);
		if ($sizes) {
			$attrs['sizes'] = $sizes;
		}
		return wp_get_attachment_image(get_post_thumbnail_id($post_id), $size, false, $attrs);
	}

	$alt = $attrs['alt'] ?? '';
	$size_attr = $sizes ? ' sizes="' . esc_attr($sizes) . '"' : '';
	$width_attr = '';
	$height_attr = '';
	if (! empty($size_map[ $size ])) {
		$width_attr = ' width="' . esc_attr((string) $size_map[ $size ][0]) . '"';
		$height_attr = ' height="' . esc_attr((string) $size_map[ $size ][1]) . '"';
	}
	return '<img src="' . esc_url(nerdywithme_fallback_image()) . '" alt="' . esc_attr($alt) . '" class="' . esc_attr(implode(' ', $classes)) . '" loading="lazy" decoding="async"' . $size_attr . $width_attr . $height_attr . '>';
}

function nerdywithme_site_title_markup() {
	$name = get_bloginfo('name');

	if (! $name) {
		$name = 'NerdyWithMe';
	}

	$parts = preg_split('/(?=[A-Z])/', preg_replace('/\s+/', '', $name), -1, PREG_SPLIT_NO_EMPTY);

	if (empty($parts)) {
		$parts = array($name);
	}

	$head = array_shift($parts);
	$tail = implode('', $parts);

	return '<span>' . esc_html($head) . '</span><span class="accent">' . esc_html($tail) . '</span>';
}

function nerdywithme_site_title_lockup_markup() {
	return '<span class="site-title__lead">nerdy</span><span class="site-title__tail">withme.</span>';
}

function nerdywithme_get_option($key, $default = '') {
	return get_theme_mod('nerdywithme_' . $key, $default);
}

function nerdywithme_reader_bar_custom_css() {
	$size = nerdywithme_sanitize_reader_bar_size(get_theme_mod('nerdywithme_reader_bar_size', 'standard'));

	$size_map = array(
		'compact'  => array(
			'padding_y'          => '14px',
			'padding_x'          => '22px',
			'mobile_padding_y'   => '10px',
			'mobile_padding_x'   => '12px',
			'progress_height'    => '3px',
			'mobile_progress'    => '2px',
		),
		'standard' => array(
			'padding_y'          => '18px',
			'padding_x'          => '28px',
			'mobile_padding_y'   => '12px',
			'mobile_padding_x'   => '14px',
			'progress_height'    => '4px',
			'mobile_progress'    => '3px',
		),
		'large'    => array(
			'padding_y'          => '22px',
			'padding_x'          => '32px',
			'mobile_padding_y'   => '14px',
			'mobile_padding_x'   => '16px',
			'progress_height'    => '5px',
			'mobile_progress'    => '4px',
		),
	);

	$sizes = $size_map[ $size ];

	$bg             = sanitize_hex_color(get_theme_mod('nerdywithme_reader_bar_bg', '#ffffff')) ?: '#ffffff';
	$text           = sanitize_hex_color(get_theme_mod('nerdywithme_reader_bar_text', '#132a78')) ?: '#132a78';
	$accent         = sanitize_hex_color(get_theme_mod('nerdywithme_reader_bar_accent', '#ff5f36')) ?: '#ff5f36';
	$progress_start = sanitize_hex_color(get_theme_mod('nerdywithme_reader_bar_progress_start', '#ff5f36')) ?: '#ff5f36';
	$progress_end   = sanitize_hex_color(get_theme_mod('nerdywithme_reader_bar_progress_end', '#ff6aa2')) ?: '#ff6aa2';
	$progress_track = sanitize_hex_color(get_theme_mod('nerdywithme_reader_bar_progress_track', '#eef1f7')) ?: '#eef1f7';

	$css  = ":root{";
	$css .= "--nwm-reader-bar-bg: {$bg};";
	$css .= "--nwm-reader-bar-text: {$text};";
	$css .= "--nwm-reader-bar-accent: {$accent};";
	$css .= "--nwm-reader-bar-progress-start: {$progress_start};";
	$css .= "--nwm-reader-bar-progress-end: {$progress_end};";
	$css .= "--nwm-reader-bar-progress-track: {$progress_track};";
	$css .= "--nwm-reader-bar-padding-y: {$sizes['padding_y']};";
	$css .= "--nwm-reader-bar-padding-x: {$sizes['padding_x']};";
	$css .= "--nwm-reader-bar-mobile-padding-y: {$sizes['mobile_padding_y']};";
	$css .= "--nwm-reader-bar-mobile-padding-x: {$sizes['mobile_padding_x']};";
	$css .= "--nwm-reader-bar-progress-height: {$sizes['progress_height']};";
	$css .= "--nwm-reader-bar-mobile-progress-height: {$sizes['mobile_progress']};";
	$css .= "}";

	return $css;
}

function nerdywithme_get_content_source_slug($key, $default = '') {
	return sanitize_title((string) get_theme_mod('nerdywithme_' . $key, $default));
}

function nerdywithme_get_brand_logo_id() {
	$custom_logo_id = (int) get_theme_mod('custom_logo');

	if ($custom_logo_id) {
		return $custom_logo_id;
	}

	$matches = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'post_mime_type' => 'image',
			's'              => 'Coloured Logo',
			'fields'         => 'ids',
		)
	);

	if (! empty($matches)) {
		return (int) $matches[0];
	}

	return 0;
}

function nerdywithme_social_links() {
	$links = array();
	$defaults = array(
		1 => array(
			'platform' => 'facebook',
			'url'      => nerdywithme_get_option('facebook_url', '#'),
		),
		2 => array(
			'platform' => 'x',
			'url'      => nerdywithme_get_option('x_url', '#'),
		),
		3 => array(
			'platform' => 'instagram',
			'url'      => nerdywithme_get_option('instagram_url', '#'),
		),
	);

	for ($i = 1; $i <= 3; $i++) {
		$default  = $defaults[ $i ];
		$platform = nerdywithme_sanitize_social_platform(get_theme_mod('nerdywithme_social_link_' . $i . '_platform', $default['platform']));
		$url      = esc_url(get_theme_mod('nerdywithme_social_link_' . $i . '_url', $default['url']));
		if ('custom' === $platform && '#' === $url) {
			$platform = $default['platform'];
		}
		if (! $url) {
			continue;
		}
		$meta = nerdywithme_social_platform_meta($platform);
		$links[] = array(
			'label'    => $meta['label'] ?: __('Social', 'nerdywithme'),
			'platform' => $platform,
			'text'     => $meta['icon'] ?: '•',
			'url'      => $url,
		);
	}

	if (! $links) {
		return array(
			array(
				'label'    => __('Facebook', 'nerdywithme'),
				'platform' => 'facebook',
				'text'     => 'f',
				'url'      => nerdywithme_get_option('facebook_url', '#'),
			),
			array(
				'label'    => __('X', 'nerdywithme'),
				'platform' => 'x',
				'text'     => 'x',
				'url'      => nerdywithme_get_option('x_url', '#'),
			),
			array(
				'label'    => __('Instagram', 'nerdywithme'),
				'platform' => 'instagram',
				'text'     => 'ig',
				'url'      => nerdywithme_get_option('instagram_url', '#'),
			),
		);
	}

	return $links;
}

function nerdywithme_render_social_links() {
	$links = nerdywithme_social_links();
	?>
	<div class="social-links" aria-label="<?php esc_attr_e('Social links', 'nerdywithme'); ?>">
		<?php foreach ($links as $link) : ?>
			<a href="<?php echo esc_url($link['url']); ?>" aria-label="<?php echo esc_attr($link['label']); ?>">
				<?php echo nerdywithme_social_icon_svg($link['platform'] ?? 'custom', $link['text'] ?? ''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
}

function nerdywithme_primary_menu_fallback() {
	echo '<ul class="menu">';
	echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'nerdywithme') . '</a></li>';
	echo '<li><a href="' . esc_url(home_url('/about')) . '">' . esc_html__('About', 'nerdywithme') . '</a></li>';
	echo '<li><a href="' . esc_url(home_url('/tools')) . '">' . esc_html__('Tools', 'nerdywithme') . '</a></li>';
	echo '<li><a href="' . esc_url(home_url('/projects')) . '">' . esc_html__('Projects', 'nerdywithme') . '</a></li>';
	$categories = nerdywithme_get_primary_categories();
	foreach ($categories as $category) {
		echo '<li><a href="' . esc_url(get_category_link($category)) . '">' . esc_html($category->name) . '</a></li>';
	}
	echo '</ul>';
}

function nerdywithme_drawer_menu_fallback() {
	echo '<ul class="mega-panel__links">';
	echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'nerdywithme') . '</a></li>';
	echo '<li><a href="' . esc_url(home_url('/about')) . '">' . esc_html__('About', 'nerdywithme') . '</a></li>';
	echo '<li><a href="' . esc_url(home_url('/blog')) . '">' . esc_html__('Blog', 'nerdywithme') . '</a></li>';
	echo '<li><a href="' . esc_url(home_url('/tools')) . '">' . esc_html__('Tools', 'nerdywithme') . '</a></li>';
	echo '<li><a href="' . esc_url(home_url('/projects')) . '">' . esc_html__('Projects', 'nerdywithme') . '</a></li>';
	echo '</ul>';
}

function nerdywithme_branding($show_tagline = true, $variant = '') {
	$custom_logo_id = nerdywithme_get_brand_logo_id();
	$tagline        = get_bloginfo('description');
	$variant        = $variant ? $variant : nerdywithme_get_option('brand_style', 'refined');
	?>
	<a class="site-brand site-brand--<?php echo esc_attr($variant); ?>" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
		<?php if ('lockup' === $variant) : ?>
			<span class="site-brand__text site-brand__text--lockup">
				<span class="site-title site-title--lockup">
					<span class="site-title__lead">nerdy</span>
					<span class="site-brand__mark">
						<?php if ($custom_logo_id) : ?>
							<?php echo wp_get_attachment_image($custom_logo_id, 'thumbnail'); ?>
						<?php else : ?>
							<span class="site-brand__fallback">N</span>
						<?php endif; ?>
					</span>
					<span class="site-title__tail">withme.</span>
				</span>
				<?php if ($show_tagline && $tagline) : ?>
					<span class="site-tagline"><?php echo esc_html($tagline); ?></span>
				<?php endif; ?>
			</span>
		<?php else : ?>
			<span class="site-brand__mark">
				<?php if ($custom_logo_id) : ?>
					<?php echo wp_get_attachment_image($custom_logo_id, 'thumbnail'); ?>
				<?php else : ?>
					<span class="site-brand__fallback">N</span>
				<?php endif; ?>
			</span>
			<span class="site-brand__text">
				<span class="site-title"><?php echo wp_kses_post(nerdywithme_site_title_markup()); ?></span>
				<?php if ($show_tagline && $tagline) : ?>
					<span class="site-tagline"><?php echo esc_html($tagline); ?></span>
				<?php endif; ?>
			</span>
		<?php endif; ?>
	</a>
	<?php
}

function nerdywithme_brand_style_label($variant) {
	return 'lockup' === $variant ? __('Option D: Head-In-Middle Lockup', 'nerdywithme') : __('Option 1: Refined Wordmark', 'nerdywithme');
}

function nerdywithme_get_category_preview_post($category_id) {
	static $preview_cache = array();
	$category_id = (int) $category_id;

	if (isset($preview_cache[ $category_id ])) {
		return $preview_cache[ $category_id ];
	}

	$posts = get_posts(
		array(
			'posts_per_page'      => 1,
			'cat'                 => $category_id,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'fields'              => 'ids',
		)
	);

	$preview_cache[ $category_id ] = (int) ($posts[0] ?? 0);

	return $preview_cache[ $category_id ];
}

function nerdywithme_category_card($category) {
	$image_id    = nerdywithme_get_category_preview_post($category->term_id);
	$description = trim(wp_strip_all_tags(category_description($category)));
	?>
	<article class="category-pill">
		<a class="category-pill__thumb" href="<?php echo esc_url(get_category_link($category)); ?>">
			<?php echo nerdywithme_get_post_image_tag($image_id, 'nwm-card-compact', array('alt' => $category->name), nerdywithme_get_image_sizes_hint('category-card')); ?>
		</a>
		<div class="category-pill__name"><?php echo esc_html($category->name); ?></div>
		<p class="section-intro">
			<?php echo esc_html($description ? wp_trim_words($description, 12, '...') : __('Fresh posts and discoveries from this corner of the blog.', 'nerdywithme')); ?>
		</p>
	</article>
	<?php
}

function nerdywithme_get_display_category($post_id = null) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$cats    = get_the_category($post_id);
	$hidden  = array('editors-pick', 'featured');

	if (empty($cats)) {
		return null;
	}

	foreach ($cats as $cat) {
		if (! in_array($cat->slug, $hidden, true)) {
			return $cat;
		}
	}

	return null;
}

function nerdywithme_get_single_post_style($post_id = null) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if (has_category(array('featured', 'editors-pick'), $post_id)) {
		return 'feature';
	}

	return 'standard';
}

function nerdywithme_post_meta($post_id = null) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$cat     = nerdywithme_get_display_category($post_id);
	?>
	<div class="post-meta">
		<?php if ($cat) : ?>
			<span class="post-categories">
				<a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"><?php echo esc_html($cat->name); ?></a>
			</span>
		<?php endif; ?>
		<span><?php echo esc_html(get_the_date('', $post_id)); ?></span>
	</div>
	<?php
}

function nerdywithme_section_heading($title, $intro = '', $link = '', $link_label = '') {
	?>
	<div class="section-header">
		<div>
			<h2 class="section-heading"><?php echo esc_html($title); ?></h2>
			<?php if ($intro) : ?>
				<p class="section-intro"><?php echo esc_html($intro); ?></p>
			<?php endif; ?>
		</div>
		<?php if ($link && $link_label) : ?>
			<a class="section-link" href="<?php echo esc_url($link); ?>"><?php echo esc_html($link_label); ?></a>
		<?php endif; ?>
	</div>
	<?php
}

function nerdywithme_card($post_id, $variant = 'standard') {
	if (! $post_id) {
		return;
	}

	$image_size  = 'compact' === $variant ? 'nwm-card-compact' : 'nwm-card';
	$image_sizes = 'compact' === $variant ? nerdywithme_get_image_sizes_hint('compact-card') : nerdywithme_get_image_sizes_hint('standard-card');
	?>
	<article <?php post_class('card card--' . $variant, $post_id); ?>>
		<a class="card__thumb" href="<?php echo esc_url(get_permalink($post_id)); ?>">
			<?php echo nerdywithme_get_post_image_tag($post_id, $image_size, array('alt' => get_the_title($post_id)), $image_sizes); ?>
		</a>
		<div class="card__content">
			<?php nerdywithme_post_meta($post_id); ?>
			<h3 class="card__title"><a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html(get_the_title($post_id)); ?></a></h3>
			<?php if ('compact' !== $variant) : ?>
				<p class="card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt($post_id), 20)); ?></p>
			<?php endif; ?>
		</div>
	</article>
	<?php
}

function nerdywithme_mini_post($post_id) {
	if (! $post_id) {
		return;
	}
	?>
	<article class="mini-post">
		<a class="mini-post__thumb" href="<?php echo esc_url(get_permalink($post_id)); ?>">
			<?php echo nerdywithme_get_post_image_tag($post_id, 'nwm-mini', array('alt' => get_the_title($post_id)), nerdywithme_get_image_sizes_hint('mini-card')); ?>
		</a>
		<div>
			<?php nerdywithme_post_meta($post_id); ?>
			<h3 class="mini-post__title"><a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html(get_the_title($post_id)); ?></a></h3>
		</div>
	</article>
	<?php
}

function nerdywithme_row_post($post_id) {
	if (! $post_id) {
		return;
	}
	?>
	<article class="row-post">
		<a class="row-post__thumb" href="<?php echo esc_url(get_permalink($post_id)); ?>">
			<?php echo nerdywithme_get_post_image_tag($post_id, 'nwm-card-compact', array('alt' => get_the_title($post_id)), nerdywithme_get_image_sizes_hint('row-card')); ?>
		</a>
		<div class="row-post__content">
			<?php nerdywithme_post_meta($post_id); ?>
			<h3 class="row-post__title"><a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html(get_the_title($post_id)); ?></a></h3>
			<p class="row-post__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt($post_id), 18)); ?></p>
		</div>
	</article>
	<?php
}

function nerdywithme_load_more_bar() {
	$next_link = get_next_posts_link(__('Load More', 'nerdywithme'));

	if (! $next_link) {
		return;
	}
	?>
	<div class="load-more-wrap">
		<?php echo wp_kses_post($next_link); ?>
	</div>
	<?php
}

function nerdywithme_build_post_query_args($query_args = array(), $limit = 0) {
	$args = wp_parse_args($query_args, array());

	$args['post_type']           = 'post';
	$args['post_status']         = 'publish';
	$args['ignore_sticky_posts'] = true;
	$args['no_found_rows']       = true;

	if ($limit > 0) {
		$args['posts_per_page'] = $limit;
	}

	return $args;
}

function nerdywithme_get_cached_query($query_args = array(), $limit = 0) {
	static $query_cache = array();

	$args      = nerdywithme_build_post_query_args($query_args, $limit);
	$cache_key = md5(wp_json_encode($args));

	if (isset($query_cache[ $cache_key ])) {
		return clone $query_cache[ $cache_key ];
	}

	$query                    = new WP_Query($args);
	$query_cache[ $cache_key ] = clone $query;

	return $query;
}

function nerdywithme_resolve_category_term($category_name) {
	static $category_cache = array();

	$raw_name  = is_string($category_name) ? trim($category_name) : '';
	$cache_key = $raw_name ? strtolower($raw_name) : '__empty__';

	if (array_key_exists($cache_key, $category_cache)) {
		return $category_cache[ $cache_key ];
	}

	$category = null;

	if ($raw_name) {
		$category = get_category_by_slug(sanitize_title($raw_name));

		if (! $category) {
			$category = get_term_by('name', $raw_name, 'category');
		}
	}

	$category_cache[ $cache_key ] = (! $category || is_wp_error($category)) ? null : $category;

	return $category_cache[ $cache_key ];
}

function nerdywithme_ranked_posts($query_args, $limit = 3) {
	$query = nerdywithme_get_cached_query($query_args, $limit);

	if (! $query->have_posts()) {
		return;
	}
	?>
	<div class="rank-list">
		<?php
		$rank = 1;
		while ($query->have_posts()) :
			$query->the_post();
			?>
			<article class="rank-list__item">
				<a class="rank-list__media" href="<?php the_permalink(); ?>">
						<span class="rank-list__thumb">
							<span class="rank-list__count"><?php echo esc_html((string) $rank); ?></span>
							<?php echo nerdywithme_get_post_image_tag(get_the_ID(), 'nwm-thumb', array('alt' => get_the_title()), nerdywithme_get_image_sizes_hint('ranked-thumb')); ?>
						</span>
				</a>
				<div class="rank-list__body">
					<div class="rank-list__content">
						<?php nerdywithme_post_meta(get_the_ID()); ?>
						<h3 class="rank-list__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					</div>
				</div>
			</article>
			<?php
			$rank++;
		endwhile;
		wp_reset_postdata();
		?>
	</div>
	<?php
}

function nerdywithme_compact_posts($query_args, $limit = 3) {
	$query = nerdywithme_get_cached_query($query_args, $limit);

	if (! $query->have_posts()) {
		return;
	}
	?>
	<div class="compact-list">
		<?php while ($query->have_posts()) : $query->the_post(); ?>
			<a class="compact-list__item compact-list__item--featured" href="<?php the_permalink(); ?>">
				<div class="compact-list__thumb">
					<?php echo nerdywithme_get_post_image_tag(get_the_ID(), 'nwm-thumb', array('alt' => get_the_title()), nerdywithme_get_image_sizes_hint('compact-thumb')); ?>
				</div>
				<div class="compact-list__content">
					<?php nerdywithme_post_meta(get_the_ID()); ?>
					<strong class="compact-list__title"><?php the_title(); ?></strong>
				</div>
			</a>
		<?php endwhile; ?>
	</div>
	<?php
	wp_reset_postdata();
}

function nerdywithme_featured_slider_posts($query_args, $limit = 3) {
	$query = nerdywithme_get_cached_query($query_args, $limit);

	if (! $query->have_posts()) {
		return;
	}
	?>
	<div class="featured-slider" data-featured-slider>
		<div class="featured-slider__track">
			<?php
			$index = 0;
			while ($query->have_posts()) :
				$query->the_post();
				?>
				<article class="featured-slide<?php echo 0 === $index ? ' is-active' : ''; ?>" data-featured-slide>
					<div class="compact-list__item compact-list__item--featured">
						<a class="compact-list__thumb-link" href="<?php the_permalink(); ?>">
							<div class="compact-list__thumb">
								<?php echo nerdywithme_get_post_image_tag(get_the_ID(), 'nwm-card-compact', array('alt' => get_the_title()), nerdywithme_get_image_sizes_hint('featured-slider')); ?>
							</div>
						</a>
						<div class="compact-list__content">
							<?php nerdywithme_post_meta(get_the_ID()); ?>
							<h3 class="compact-list__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						</div>
					</div>
				</article>
				<?php
				$index++;
			endwhile;
			?>
		</div>
		<div class="widget-card__nav featured-slider__nav" aria-label="<?php esc_attr_e('Featured posts navigation', 'nerdywithme'); ?>">
			<button class="featured-slider__arrow" type="button" data-featured-prev aria-label="<?php esc_attr_e('Previous featured post', 'nerdywithme'); ?>">&lsaquo;</button>
			<div class="featured-slider__dots">
				<?php for ($i = 0; $i < $index; $i++) : ?>
					<button class="featured-slider__dot<?php echo 0 === $i ? ' is-active' : ''; ?>" type="button" data-featured-dot="<?php echo esc_attr((string) $i); ?>" aria-label="<?php echo esc_attr(sprintf(__('Go to featured post %d', 'nerdywithme'), $i + 1)); ?>"></button>
				<?php endfor; ?>
			</div>
			<button class="featured-slider__arrow" type="button" data-featured-next aria-label="<?php esc_attr_e('Next featured post', 'nerdywithme'); ?>">&rsaquo;</button>
		</div>
	</div>
	<?php
	wp_reset_postdata();
}

function nerdywithme_get_featured_posts($count = 6, $exclude = array()) {
	return nerdywithme_get_cached_query(
		array(
			'post__not_in' => $exclude,
		),
		$count
	);
}

function nerdywithme_get_posts_by_category_name($category_name, $count = 3, $exclude = array()) {
	$category = nerdywithme_resolve_category_term($category_name);

	if (! $category || is_wp_error($category)) {
		return nerdywithme_get_cached_query(
			array(
				'post__not_in' => $exclude,
			),
			$count
		);
	}

	return nerdywithme_get_cached_query(
		array(
			'post__not_in' => $exclude,
			'cat'          => (int) $category->term_id,
		),
		$count
	);
}

function nerdywithme_get_related_posts($post_id = null, $count = 3) {
	$post_id      = $post_id ? $post_id : get_the_ID();
	$source_slug  = nerdywithme_get_content_source_slug('read_next_category', '');
	$exclude      = array((int) $post_id);

	if ($source_slug) {
		return nerdywithme_get_posts_by_category_name($source_slug, $count, $exclude);
	}

	$display_category = nerdywithme_get_display_category($post_id);

	if ($display_category && ! is_wp_error($display_category)) {
		return nerdywithme_get_cached_query(
			array(
				'post__not_in' => $exclude,
				'cat'          => (int) $display_category->term_id,
			),
			$count
		);
	}

	return nerdywithme_get_featured_posts($count, $exclude);
}

function nerdywithme_add_dropcap_to_first_paragraph($content) {
	if (! is_string($content) || '' === trim($content)) {
		return $content;
	}

	return preg_replace_callback(
		'/<p([^>]*)>(.*?)<\/p>/is',
		function ($matches) {
			$inner = $matches[2];

			if (false !== strpos($inner, 'entry-dropcap')) {
				return $matches[0];
			}

			$updated = preg_replace(
				'/(^|>|\s)([A-Za-z])/',
				'$1<span class="entry-dropcap">$2</span>',
				$inner,
				1
			);

			if (null === $updated || $updated === $inner) {
				return $matches[0];
			}

			return '<p' . $matches[1] . '>' . $updated . '</p>';
		},
		$content,
		1
	);
}

function nerdywithme_prepare_single_content($post_id = null) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$content = apply_filters('the_content', get_post_field('post_content', $post_id));
	$content = nerdywithme_add_dropcap_to_first_paragraph($content);

	return nerdywithme_attach_table_of_contents($content);
}

function nerdywithme_attach_table_of_contents($content) {
	$toc_items = array();
	$id_counts = array();

	$processed = preg_replace_callback(
		'/<h([2-3])([^>]*)>(.*?)<\/h\1>/is',
		function ($matches) use (&$toc_items, &$id_counts) {
			$level = (int) $matches[1];
			$attrs = $matches[2];
			$inner = $matches[3];

			$id = '';
			if (preg_match('/\sid=["\']([^"\']+)["\']/', $attrs, $id_match)) {
				$id = $id_match[1];
			} else {
				$title = trim(wp_strip_all_tags($inner));
				$base  = sanitize_title($title);
				if (! $base) {
					$base = 'section';
				}
				$index = isset($id_counts[ $base ]) ? $id_counts[ $base ] + 1 : 1;
				$id_counts[ $base ] = $index;
				$id = $base . ($index > 1 ? '-' . $index : '');
				$attrs .= ' id="' . esc_attr($id) . '"';
			}

			$toc_items[] = array(
				'id'    => $id,
				'label' => trim(wp_strip_all_tags($inner)),
				'level' => $level,
			);

			return '<h' . $level . $attrs . '>' . $inner . '</h' . $level . '>';
		},
		$content
	);

	if (count($toc_items) < 2) {
		return array(
			'toc'     => '',
			'content' => $processed,
		);
	}

	$toc = '<nav class="toc" aria-label="' . esc_attr__('Table of contents', 'nerdywithme') . '">';
	$toc .= '<div class="toc__title">' . esc_html__('Table Of Contents', 'nerdywithme') . '</div>';
	$toc .= '<ul class="toc__list">';
	foreach ($toc_items as $item) {
		if (! $item['label']) {
			continue;
		}
		$level_class = $item['level'] === 3 ? ' toc__item--sub' : '';
		$toc .= '<li class="toc__item' . $level_class . '">';
		$toc .= '<a href="#' . esc_attr($item['id']) . '">' . esc_html($item['label']) . '</a>';
		$toc .= '</li>';
	}
	$toc .= '</ul>';
	$toc .= '</nav>';

	return array(
		'toc'     => $toc,
		'content' => $processed,
	);
}

function nerdywithme_get_404_suggestion_posts($count = 3) {
	$source_slug = nerdywithme_get_content_source_slug('404_suggestions_category', '');

	if ($source_slug) {
		return nerdywithme_get_posts_by_category_name($source_slug, $count);
	}

	return nerdywithme_get_featured_posts($count);
}

function nerdywithme_get_primary_categories($count = 6) {
	static $primary_categories_cache = array();
	$count = max(1, (int) $count);

	if (isset($primary_categories_cache[ $count ])) {
		return $primary_categories_cache[ $count ];
	}

	$primary_categories_cache[ $count ] = get_categories(
		array(
			'hide_empty' => false,
			'number'     => $count,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);

	return $primary_categories_cache[ $count ];
}
