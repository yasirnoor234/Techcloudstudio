<?php
/**
 * Appearance Setting Panel
 *
 * @package Rara_Business
 */

if ( ! function_exists( 'rara_business_customize_register_appearance_settings_panel' ) ) :
	/**
	 * Add appearance settings panel
	 */
	function rara_business_customize_register_appearance_settings_panel( $wp_customize ) {

	    $wp_customize->add_panel( 'appearance_settings_panel', array(
	        'title'          => __( 'Appearance Settings', 'rara-business' ),
	        'priority'       => 60,
	        'capability'     => 'edit_theme_options',
	    ) );
	    
	    // Move default section to apperance settings panel
		$wp_customize->get_section( 'background_image' )->panel = 'appearance_settings_panel';
		$wp_customize->get_section( 'colors' )->panel           = 'appearance_settings_panel';
	}
endif;
add_action( 'customize_register', 'rara_business_customize_register_appearance_settings_panel' );