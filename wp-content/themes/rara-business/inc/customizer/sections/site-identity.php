<?php
/**
 * Site Identitiy Customizer section
 *
 * @package Rara_Business
 */
if ( ! function_exists( 'rara_business_customize_register_site_identity_section' ) ) :
    /**
     * Add custom site identity controls
     */
    function rara_business_customize_register_site_identity_section( $wp_customize ) {
    	/** Load default theme options */
        $default_options =  rara_business_default_theme_options();

        /** Add postMessage support for site title and description */
        $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
        $wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
        $wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
    	
        // Selective refresh for blogname 
        $wp_customize->selective_refresh->add_partial( 'blogname', array(
            'selector'        => '.site-title a',
            'render_callback' => 'rara_business_customize_partial_blogname',
        ) );

        // Selective refresh for blogdescription 
        $wp_customize->selective_refresh->add_partial( 'blogdescription', array(
            'selector'        => '.site-description',
            'render_callback' => 'rara_business_customize_partial_blogdescription',
        ) );
    }
endif;
add_action( 'customize_register', 'rara_business_customize_register_site_identity_section' );