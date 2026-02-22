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
