<?php
/**
 * Rara Business Theme Customizer
 *
 * @package Rara_Business
 */

if ( ! function_exists( 'rara_business_customize_register' ) ) :
    /**
     * Add postMessage support for site title and description for the Theme Customizer.
     *
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     */
    function rara_business_customize_register( $wp_customize ) {
        $wp_customize->get_section( 'background_image' )->priority = 40;
    }
endif;
add_action( 'customize_register', 'rara_business_customize_register' );

$rara_business_panels       = array( 'frontpage', 'general', 'appearance' );
$rara_business_sections     = array( 'info', 'demo-content', 'site-identity', 'footer' );

$rara_business_sub_sections = array(
    'frontpage' => array( 'banner', 'portfolio', 'blog' ),
	'general'   => array( 'header', 'seo', 'post-page' ),
);

foreach( $rara_business_panels as $p ){
   require get_template_directory() . '/inc/customizer/panels/' . $p . '.php';
}

foreach( $rara_business_sections as $s ){
    require get_template_directory() . '/inc/customizer/sections/' . $s . '.php';
}

foreach( $rara_business_sub_sections as $k => $v ){
    foreach( $v as $w ){        
        require get_template_directory() . '/inc/customizer/panels/' . $k . '/' . $w . '.php';
    }
}

if ( ! function_exists( 'rara_business_customize_preview_js' ) ) :
    /**
     * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
     */
    function rara_business_customize_preview_js() {
        wp_enqueue_script( 'rara-business-customizer', get_template_directory_uri() . '/js/build/customizer.js', array( 'customize-preview' ), '20151215', true );
    }
endif;
add_action( 'customize_preview_init', 'rara_business_customize_preview_js' );

if ( ! function_exists( 'rara_business_customizer_script' ) ) :
    /**
     * Customizer Scripts
     */
    function rara_business_customizer_script(){
        wp_enqueue_style( 'rara-business-customize-controls', get_template_directory_uri() . '/inc/css/customize-controls.css', array(), false , 'screen' );
        wp_enqueue_script( 'rara-business-customize-controls', get_template_directory_uri() . '/inc/js/customize-controls.js', array( 'jquery', 'customize-controls' ), false, true  );
    }
endif;
add_action( 'customize_controls_enqueue_scripts', 'rara_business_customizer_script' );

/*
 * Notifications in customizer
 */
require get_template_directory() . '/inc/customizer-plugin-recommend/customizer-notice/class-customizer-notice.php';

require get_template_directory() . '/inc/customizer-plugin-recommend/plugin-install/class-plugin-install-helper.php';

$config_customizer = array(
    'recommended_plugins' => array( 
        'raratheme-companion' => array(
            'recommended' => true,
            'description' => sprintf( 
                /* translators: %s: plugin name */
                esc_html__( 'If you want to take full advantage of the features this theme has to offer, please install and activate %s plugin.', 'rara-business' ), '<strong>RaraTheme Companion</strong>' 
            ),
        ),
    ),
    'recommended_plugins_title' => esc_html__( 'Recommended Plugin', 'rara-business' ),
    'install_button_label'      => esc_html__( 'Install and Activate', 'rara-business' ),
    'activate_button_label'     => esc_html__( 'Activate', 'rara-business' ),
    'deactivate_button_label'   => esc_html__( 'Deactivate', 'rara-business' ),
);
Rara_Business_Customizer_Notice::init( apply_filters( 'rara_business_customizer_notice_array', $config_customizer ) );
