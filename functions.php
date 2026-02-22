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

		var details = form.querySelector(".h4x0r-comment-details");
		if (details) {
			details.querySelector("summary").after(btn);
		} else {
			form.insertBefore(btn, form.firstChild);
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'h4x0r_comment_form_toggle' );

function h4x0r_comment_form_details( $defaults ) {
	$defaults['title_reply_before'] = '<details class="h4x0r-comment-details"><summary>'
		. $defaults['title_reply_before'];
	$defaults['title_reply_after'] = $defaults['title_reply_after'] . '</summary>';
	return $defaults;
}
add_filter( 'comment_form_defaults', 'h4x0r_comment_form_details' );

function h4x0r_comment_form_details_close() {
	echo '</details>';
}
add_action( 'comment_form', 'h4x0r_comment_form_details_close' );

function h4x0r_comment_honeypot_field() {
	echo '<p style="display:none" aria-hidden="true"><label>Leave this empty: <input type="text" name="h4x0r_hp" value="" tabindex="-1" autocomplete="off" /></label></p>';
}
add_action( 'comment_form_after_fields', 'h4x0r_comment_honeypot_field' );
add_action( 'comment_form_logged_in_after', 'h4x0r_comment_honeypot_field' );

function h4x0r_antispam_check( $commentdata ) {
	if ( ! empty( $_POST['h4x0r_hp'] ) ) {
		wp_die( 'Spam detected.', 'Comment Blocked', array( 'response' => 403, 'back_link' => true ) );
	}

	$ip = $_SERVER['REMOTE_ADDR'];
	$key = 'h4x0r_rate_' . md5( $ip );
	$count = (int) get_transient( $key );

	if ( $count >= 3 ) {
		wp_die( 'Too many comments. Please try again in a few minutes.', 'Rate Limited', array( 'response' => 429, 'back_link' => true ) );
	}

	set_transient( $key, $count + 1, 5 * MINUTE_IN_SECONDS );

	return $commentdata;
}
add_filter( 'preprocess_comment', 'h4x0r_antispam_check' );
