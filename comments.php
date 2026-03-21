<?php
/**
 * Comments template.
 *
 * @package NerdyWithMe
 */

if (post_password_required()) {
	return;
}
?>

<section id="comments" class="comments-area">
	<?php if (have_comments()) : ?>
		<h2 class="widget-title">
			<?php
			printf(
				esc_html(
					_n('%s Comment', '%s Comments', get_comments_number(), 'nerdywithme')
				),
				number_format_i18n(get_comments_number())
			);
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
				)
			);
			?>
		</ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'        => __('Add a Comment', 'nerdywithme'),
			'title_reply_before' => '<h2 class="widget-title">',
			'title_reply_after'  => '</h2>',
		)
	);
	?>
</section>
