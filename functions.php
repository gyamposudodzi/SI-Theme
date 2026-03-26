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
			'primary' => __('Primary Menu', 'nerdywithme'),
			'footer'  => __('Footer Menu', 'nerdywithme'),
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
}
add_action('customize_register', 'nerdywithme_customize_register');

function nerdywithme_sanitize_brand_style($value) {
	$allowed = array('refined', 'lockup');
	return in_array($value, $allowed, true) ? $value : 'refined';
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

function nerdywithme_widgets_init() {
	register_sidebar(
		array(
			'name'          => __('Sidebar', 'nerdywithme'),
			'id'            => 'sidebar-1',
			'description'   => __('Widgets on posts and archive pages.', 'nerdywithme'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action('widgets_init', 'nerdywithme_widgets_init');

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
				<span class="compact-list__thumb">
					<img src="<?php echo esc_url(nerdywithme_get_post_image(get_the_ID(), 'thumbnail')); ?>" alt="<?php the_title_attribute(); ?>">
				</span>
				<span class="compact-list__content">
					<?php nerdywithme_post_meta(get_the_ID()); ?>
					<strong class="compact-list__title"><?php the_title(); ?></strong>
				</span>
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
					<a class="compact-list__item compact-list__item--featured" href="<?php the_permalink(); ?>">
						<span class="compact-list__thumb">
							<img src="<?php echo esc_url(nerdywithme_get_post_image(get_the_ID(), 'medium_large')); ?>" alt="<?php the_title_attribute(); ?>">
						</span>
						<span class="compact-list__content">
							<?php nerdywithme_post_meta(get_the_ID()); ?>
							<strong class="compact-list__title"><?php the_title(); ?></strong>
						</span>
					</a>
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
