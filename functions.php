<?php

function h4x0r_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'h4x0r_options', array(
		'title'    => __( 'H4x0r Options', 'h4x0r' ),
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'h4x0r_show_post_date', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	) );

	$wp_customize->add_control( 'h4x0r_show_post_date', array(
		'label'   => __( 'Show post date in post lists', 'h4x0r' ),
		'section' => 'h4x0r_options',
		'type'    => 'checkbox',
	) );
}
add_action( 'customize_register', 'h4x0r_customize_register' );

function h4x0r_maybe_hide_post_date( $block_content, $block ) {
	if ( 'core/post-date' !== $block['blockName'] ) {
		return $block_content;
	}

	if ( ! get_theme_mod( 'h4x0r_show_post_date', true ) ) {
		return '';
	}

	return $block_content;
}
add_filter( 'render_block', 'h4x0r_maybe_hide_post_date', 10, 2 );

function h4x0r_comment_form_toggle() {
	?>
	<script>
	(function() {
		var KEY = "h4x0r-comment-light";
		var form = document.querySelector(".wp-block-post-comments-form");
		if (!form) return;

		var btn = document.createElement("button");
		btn.type = "button";
		btn.className = "h4x0r-comment-toggle";
		btn.setAttribute("aria-label", "Toggle comment form theme");

		function apply(light) {
			if (light) {
				form.classList.add("light-mode");
				btn.textContent = "[ light ]";
			} else {
				form.classList.remove("light-mode");
				btn.textContent = "[ dark ]";
			}
		}

		var saved = localStorage.getItem(KEY);
		apply(saved === "1");

		btn.addEventListener("click", function() {
			var isLight = form.classList.contains("light-mode");
			apply(!isLight);
			localStorage.setItem(KEY, !isLight ? "1" : "0");
		});

		form.insertBefore(btn, form.firstChild);
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'h4x0r_comment_form_toggle' );
