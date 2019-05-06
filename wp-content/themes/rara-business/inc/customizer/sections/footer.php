<?php
/**
 * Footer Setting
 *
 * @package Rara_Business
 */

function rara_business_customize_register_footer( $wp_customize ) {
    
    $wp_customize->add_section(
        'rara_business_footer_settings',
        array(
            'title'      => __( 'Footer Settings', 'rara-business' ),
            'priority'   => 199,
            'capability' => 'edit_theme_options',
        )
    );
    
    /** Footer Copyright */
    $wp_customize->add_setting(
        'footer_copyright',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
            'transport'         => 'postMessage'
        )
    );
    
    $wp_customize->add_control(
        'footer_copyright',
        array(
            'label'   => __( 'Footer Copyright Text', 'rara-business' ),
            'section' => 'rara_business_footer_settings',
            'type'    => 'textarea',
        )
    );
    
    $wp_customize->selective_refresh->add_partial( 'footer_copyright', array(
        'selector' => '.site-footer .footer-b span.copyright',
        'render_callback' => 'rara_business_get_footer_copyright',
    ) );
        
}
add_action( 'customize_register', 'rara_business_customize_register_footer' );