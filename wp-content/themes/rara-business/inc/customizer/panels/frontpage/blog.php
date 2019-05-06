<?php
/**
 * Blog Section
 *
 * @package Rara_Business
 */

if ( ! function_exists( 'rara_business_customize_register_blog_section' ) ) :
    /**
     * Add blog section controls
     */
    function rara_business_customize_register_blog_section( $wp_customize ) {
        /** Load default theme options */
        $default_options =  rara_business_default_theme_options();

        /** Blog Sectopm */
        $wp_customize->add_section(
            'blog_section',
            array(
                'title'    => __( 'Blog Section', 'rara-business' ),
                'priority' => 77,
                'panel'    => 'frontpage_panel',
            )
        );

        /** Blog Options */
        $wp_customize->add_setting(
            'ed_blog_section',
            array(
                'default'           => $default_options['ed_blog_section'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            )
        );

        $wp_customize->add_control(
            new Rara_Business_Toggle_Control(
                $wp_customize,
                'ed_blog_section',
                array(
                    'label'       => __( 'Enable Blog Section', 'rara-business' ),
                    'description' => __( 'Enable to show blog section.', 'rara-business' ),
                    'section'     => 'blog_section',
                    'priority'    => 5 
                )            
            )
        );

        /** Blog title */
        $wp_customize->add_setting(
            'blog_title',
            array(
                'default'           => $default_options['blog_title'],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage'
            )
        );
        
        $wp_customize->add_control(
            'blog_title',
            array(
                'section'         => 'blog_section',
                'label'           => __( 'Blog Title', 'rara-business' ),
                'active_callback' => 'rara_business_blog_ac'
            )
        );

        // Selective refresh for blog title.
        $wp_customize->selective_refresh->add_partial( 'blog_title', array(
            'selector'            => '.blog-section .widget_text h2.widget-title',
            'render_callback'     => 'rara_business_blog_title_selective_refresh',
            'container_inclusive' => false,
            'fallback_refresh'    => true,
        ) );

        /** Blog description */
        $wp_customize->add_setting(
            'blog_description',
            array(
                'default'           => $default_options['blog_description'],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage'
            )
        );
        
        $wp_customize->add_control(
            'blog_description',
            array(
                'section'         => 'blog_section',
                'label'           => __( 'Blog Description', 'rara-business' ),
                'active_callback' => 'rara_business_blog_ac'
            )
        ); 

        // Selective refresh for blog description.
        $wp_customize->selective_refresh->add_partial( 'blog_description', array(
            'selector'            => '.blog-section .textwidget p',
            'render_callback'     => 'rara_business_blog_description_selective_refresh',
            'container_inclusive' => false,
            'fallback_refresh'    => true,
        ) );      
    }
endif;
add_action( 'customize_register', 'rara_business_customize_register_blog_section' );

if ( ! function_exists( 'rara_business_blog_ac' ) ) :
    /**
     * Active Callback
     */
    function rara_business_blog_ac( $control ){
        $show_blog  = $control->manager->get_setting( 'ed_blog_section' )->value();
        $control_id = $control->id;

        // Blog title, description and number of posts controls
        if ( $control_id == 'blog_title' && $show_blog ) return true;
        if ( $control_id == 'blog_description' && $show_blog ) return true;

        return false;
    }
endif;