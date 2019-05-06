<?php
/**
 * General Setting Panel
 *
 * @package Rara_Business
 */

if ( ! function_exists( 'rara_business_customize_register_general_settings_panel' ) ) :
	/**
	 * Add general settings panel
	 */
	function rara_business_customize_register_general_settings_panel( $wp_customize ) {

	    $wp_customize->add_panel( 'general_settings_panel', array(
	        'title'          => __( 'General Settings', 'rara-business' ),
	        'priority'       => 60,
	        'capability'     => 'edit_theme_options',
	    ) );
	    
	}
endif;
add_action( 'customize_register', 'rara_business_customize_register_general_settings_panel' );