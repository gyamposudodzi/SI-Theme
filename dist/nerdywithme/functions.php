<?php
/**
 * NerdyWithMe theme functions.
 *
 * @package NerdyWithMe
 */

if (! defined('NERDYWITHME_VERSION')) {
	define('NERDYWITHME_VERSION', '1.0.0');
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
	$fonts_url = 'https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap';
	$style_version  = file_exists(get_stylesheet_directory() . '/style.css') ? (string) filemtime(get_stylesheet_directory() . '/style.css') : NERDYWITHME_VERSION;
	$script_version = file_exists(get_template_directory() . '/script.js') ? (string) filemtime(get_template_directory() . '/script.js') : NERDYWITHME_VERSION;

	wp_enqueue_style('nerdywithme-fonts', esc_url($fonts_url), array(), null);
	wp_enqueue_style('nerdywithme-style', get_stylesheet_uri(), array('nerdywithme-fonts'), $style_version);
	wp_add_inline_style('nerdywithme-style', nerdywithme_reader_bar_custom_css());
	wp_enqueue_script(
		'nerdywithme-script',
		get_template_directory_uri() . '/script.js',
		array(),
		$script_version,
		true
	);
}
add_action('wp_enqueue_scripts', 'nerdywithme_enqueue_assets');

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
				'icon_text' => __('Fallback Icon Text', 'nerdywithme'),
				'handle'    => __('Handle', 'nerdywithme'),
				'description' => __('Description', 'nerdywithme'),
			);

			foreach ($text_fields as $field => $label) {
				$wp_customize->add_setting(
					$base . $field,
					array(
						'default'           => $defaults[ $field ],
						'sanitize_callback' => 'sanitize_text_field',
					)
				);
				$wp_customize->add_control(
					$base . $field,
					array(
						'label'   => sprintf(__('Card %1$d %2$s', 'nerdywithme'), $i, $label),
						'section' => $config['section'],
						'type'    => 'text',
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
			'tone'        => 'green',
			'icon_text'   => 'TV',
			'handle'      => '@nerdywithme',
			'description' => __('Listen on TradingView', 'nerdywithme'),
			'url'         => '#',
		),
		array(
			'tone'        => 'pink',
			'icon_text'   => 'X',
			'handle'      => '@nerdywithme',
			'description' => __('Follow on X', 'nerdywithme'),
			'url'         => '#',
		),
		array(
			'tone'        => 'orange',
			'icon_text'   => 'IG',
			'handle'      => '@nerdywithme',
			'description' => __('Find me on Instagram', 'nerdywithme'),
			'url'         => '#',
		),
		array(
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
			'tone'        => 'green',
			'icon_text'   => 'TV',
			'handle'      => '@nerdywithme',
			'description' => __('Track setups in TradingView', 'nerdywithme'),
			'url'         => '#',
		),
		array(
			'tone'        => 'pink',
			'icon_text'   => 'X',
			'handle'      => '@nerdywithme',
			'description' => __('Follow market notes on X', 'nerdywithme'),
			'url'         => '#',
		),
		array(
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

		$cards[] = array(
			'tone'        => nerdywithme_sanitize_card_tone(get_theme_mod($base . 'tone', $default['tone'])),
			'icon_text'   => sanitize_text_field(get_theme_mod($base . 'icon_text', $default['icon_text'])),
			'handle'      => sanitize_text_field(get_theme_mod($base . 'handle', $default['handle'])),
			'description' => sanitize_text_field(get_theme_mod($base . 'description', $default['description'])),
			'url'         => esc_url(get_theme_mod($base . 'url', $default['url'])),
			'icon_image'  => $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '',
		);
	}

	return $cards;
}

function nerdywithme_render_card_icon($card, $icon_class) {
	if (! empty($card['icon_image'])) {
		return '<span class="' . esc_attr($icon_class) . '"><img src="' . esc_url($card['icon_image']) . '" alt=""></span>';
	}

	return '<span class="' . esc_attr($icon_class) . '">' . esc_html($card['icon_text']) . '</span>';
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
			$url = wp_get_attachment_image_url($image_id, 'medium_large');
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
				$urls[] = nerdywithme_get_post_image(get_the_ID(), 'medium_large');
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
	return array(
		array(
			'label' => __('Facebook', 'nerdywithme'),
			'text'  => 'f',
			'url'   => nerdywithme_get_option('facebook_url', '#'),
		),
		array(
			'label' => __('X', 'nerdywithme'),
			'text'  => 'x',
			'url'   => nerdywithme_get_option('x_url', '#'),
		),
		array(
			'label' => __('Instagram', 'nerdywithme'),
			'text'  => 'ig',
			'url'   => nerdywithme_get_option('instagram_url', '#'),
		),
	);
}

function nerdywithme_render_social_links() {
	$links = nerdywithme_social_links();
	?>
	<div class="social-links" aria-label="<?php esc_attr_e('Social links', 'nerdywithme'); ?>">
		<?php foreach ($links as $link) : ?>
			<a href="<?php echo esc_url($link['url']); ?>" aria-label="<?php echo esc_attr($link['label']); ?>"><?php echo esc_html($link['text']); ?></a>
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
	$posts = get_posts(
		array(
			'posts_per_page'      => 1,
			'cat'                 => $category_id,
			'ignore_sticky_posts' => true,
		)
	);

	return $posts[0]->ID ?? 0;
}

function nerdywithme_category_card($category) {
	$image_id    = nerdywithme_get_category_preview_post($category->term_id);
	$description = trim(wp_strip_all_tags(category_description($category)));
	?>
	<article class="category-pill">
		<a class="category-pill__thumb" href="<?php echo esc_url(get_category_link($category)); ?>">
			<img src="<?php echo esc_url(nerdywithme_get_post_image($image_id, 'medium_large')); ?>" alt="<?php echo esc_attr($category->name); ?>">
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
	?>
	<article <?php post_class('card card--' . $variant, $post_id); ?>>
		<a class="card__thumb" href="<?php echo esc_url(get_permalink($post_id)); ?>">
			<img src="<?php echo esc_url(nerdywithme_get_post_image($post_id, 'large')); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>">
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
			<img src="<?php echo esc_url(nerdywithme_get_post_image($post_id, 'medium_large')); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>">
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
			<img src="<?php echo esc_url(nerdywithme_get_post_image($post_id, 'medium_large')); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>">
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

function nerdywithme_ranked_posts($query_args, $limit = 3) {
	$args                   = wp_parse_args($query_args, array());
	$args['post_type']      = 'post';
	$args['posts_per_page'] = 3;
	$args['ignore_sticky_posts'] = true;
	$query                  = new WP_Query($args);

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
						<img src="<?php echo esc_url(nerdywithme_get_post_image(get_the_ID(), 'thumbnail')); ?>" alt="<?php the_title_attribute(); ?>">
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
	$query = new WP_Query(wp_parse_args($query_args, array('posts_per_page' => $limit)));

	if (! $query->have_posts()) {
		return;
	}
	?>
	<div class="compact-list">
		<?php while ($query->have_posts()) : $query->the_post(); ?>
			<a class="compact-list__item compact-list__item--featured" href="<?php the_permalink(); ?>">
				<div class="compact-list__thumb">
					<img src="<?php echo esc_url(nerdywithme_get_post_image(get_the_ID(), 'thumbnail')); ?>" alt="<?php the_title_attribute(); ?>">
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
	$args                   = wp_parse_args($query_args, array());
	$args['post_type']      = 'post';
	$args['posts_per_page'] = $limit;
	$args['ignore_sticky_posts'] = true;
	$query                  = new WP_Query($args);

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
								<img src="<?php echo esc_url(nerdywithme_get_post_image(get_the_ID(), 'medium_large')); ?>" alt="<?php the_title_attribute(); ?>">
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
	return new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => $count,
			'post__not_in'        => $exclude,
			'ignore_sticky_posts' => true,
		)
	);
}

function nerdywithme_get_posts_by_category_name($category_name, $count = 3, $exclude = array()) {
	$category = get_category_by_slug(sanitize_title($category_name));

	if (! $category) {
		$category = get_term_by('name', $category_name, 'category');
	}

	if (! $category || is_wp_error($category)) {
		return new WP_Query(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => $count,
				'post__not_in'        => $exclude,
				'ignore_sticky_posts' => true,
			)
		);
	}

	return new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => $count,
			'post__not_in'        => $exclude,
			'ignore_sticky_posts' => true,
			'cat'                 => (int) $category->term_id,
		)
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
		return new WP_Query(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => $count,
				'post__not_in'        => $exclude,
				'ignore_sticky_posts' => true,
				'cat'                 => (int) $display_category->term_id,
			)
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
	return get_categories(
		array(
			'hide_empty' => false,
			'number'     => $count,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);
}
