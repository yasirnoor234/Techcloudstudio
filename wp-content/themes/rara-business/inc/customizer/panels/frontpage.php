<?php
/**
 * Front Page Panel
 *
 * @package Rara_Business
 */

if ( ! function_exists( 'rara_business_customize_register_frontpage_panel' ) ) :
	/**
	 * Add frontpage panel
	 */
	function rara_business_customize_register_frontpage_panel( $wp_customize ) {

	    $wp_customize->add_panel( 'frontpage_panel', array(
	        'title'          => __( 'Frontpage Settings', 'rara-business' ),
	        'priority'       => 60,
	        'capability'     => 'edit_theme_options',
	    ) );
	    
	}
endif;
add_action( 'customize_register', 'rara_business_customize_register_frontpage_panel' );