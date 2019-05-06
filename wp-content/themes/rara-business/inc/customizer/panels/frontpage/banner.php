<?php
/**
 * Banner Section
 *
 * @package Rara_Business
 */

if ( ! function_exists( 'rara_business_customize_register_banner_section' ) ) :
    /**
     * Add banner section controls
     */
    function rara_business_customize_register_banner_section( $wp_customize ) {
        /** Load default theme options */
        $default_options =  rara_business_default_theme_options();

        $wp_customize->get_section( 'header_image' )->panel    = 'frontpage_panel';
        $wp_customize->get_section( 'header_image' )->title    = __( 'Banner Section', 'rara-business' );
        $wp_customize->get_section( 'header_image' )->priority = 10;
        $wp_customize->get_control( 'header_image' )->active_callback = 'rara_business_banner_ac';
        $wp_customize->get_control( 'header_video' )->active_callback = 'rara_business_banner_ac';
        $wp_customize->get_control( 'external_header_video' )->active_callback = 'rara_business_banner_ac';
        $wp_customize->get_section( 'header_image' )->description = '';                                               
        $wp_customize->get_setting( 'header_image' )->transport = 'refresh';
        $wp_customize->get_setting( 'header_video' )->transport = 'refresh';
        $wp_customize->get_setting( 'external_header_video' )->transport = 'refresh';

        /** Banner Options */
        $wp_customize->add_setting(
            'ed_banner_section',
            array(
                'default'           => $default_options['ed_banner_section'],
                'sanitize_callback' => 'rara_business_sanitize_select'
            )
        );

        $wp_customize->add_control(
            new Rara_Business_Select_Control(
                $wp_customize,
                'ed_banner_section',
                array(
                    'label'       => __( 'Banner Options', 'rara-business' ),
                    'description' => __( 'Choose banner as static image/video.', 'rara-business' ),
                    'section'     => 'header_image',
                    'choices'     => array(
                        'no_banner'     => __( 'Disable Banner Section', 'rara-business' ),
                        'static_banner' => __( 'Static/Video Banner', 'rara-business' ),
                    ),
                    'priority' => 5 
                )            
            )
        );

        /** Banner title */
        $wp_customize->add_setting(
            'banner_title',
            array(
                'default'           => $default_options['banner_title'],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage'
            )
        );
        
        $wp_customize->add_control(
            'banner_title',
            array(
                'section'         => 'header_image',
                'label'           => __( 'Banner Title', 'rara-business' ),
                'active_callback' => 'rara_business_banner_ac'
            )
        );

        // banner title selective refresh
        $wp_customize->selective_refresh->add_partial( 'banner_title', array(
            'selector'            => '.banner .text-holder h2.title',
            'render_callback'     => 'rara_business_banner_title_selective_refresh',
            'container_inclusive' => false,
            'fallback_refresh'    => true,
        ) );

        /** Banner description */
        $wp_customize->add_setting(
            'banner_description',
            array(
                'default'           => $default_options['banner_description'],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage'
            )
        );
        
        $wp_customize->add_control(
            'banner_description',
            array(
                'section'         => 'header_image',
                'label'           => __( 'Banner Description', 'rara-business' ),
                'active_callback' => 'rara_business_banner_ac'
            )
        );

        // Banner description selective refresh
        $wp_customize->selective_refresh->add_partial( 'banner_description', array(
            'selector'            => '.banner .text-holder p',
            'render_callback'     => 'rara_business_banner_description_selective_refresh',
            'container_inclusive' => false,
            'fallback_refresh'    => true,
        ) );

        /** Banner link one label */
        $wp_customize->add_setting(
            'banner_link_one_label',
            array(
                'default'           => $default_options['banner_link_one_label'],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage'
            )
        );
        
        $wp_customize->add_control(
            'banner_link_one_label',
            array(
                'section'         => 'header_image',
                'label'           => __( 'Link One Label', 'rara-business' ),
                'active_callback' => 'rara_business_banner_ac'
            )
        );

        // Selective refresh for banner link one label
        $wp_customize->selective_refresh->add_partial( 'banner_link_one_label', array(
            'selector'            => '.banner .btn-holder a.btn-free-inquiry',
            'render_callback'     => 'rara_business_banner_link_one_label_selective_refresh',
            'container_inclusive' => false,
            'fallback_refresh'    => true,
        ) );

        /** Banner link one url */
        $wp_customize->add_setting(
            'banner_link_one_url',
            array(
                'default'           => $default_options['banner_link_one_url'],
                'sanitize_callback' => 'esc_url_raw',
            )
        );

        $wp_customize->add_control(
            'banner_link_one_url',
            array(
                'section'         => 'header_image',
                'label'           => __( 'Link One Url', 'rara-business' ),
                'type'            => 'url',
                'active_callback' => 'rara_business_banner_ac'
            )
        );

        /** Banner link two label */
        $wp_customize->add_setting(
            'banner_link_two_label',
            array(
                'default'           => $default_options['banner_link_two_label'],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage'
            )
        );
        
        $wp_customize->add_control(
            'banner_link_two_label',
            array(
                'section'         => 'header_image',
                'label'           => __( 'Link Two Label', 'rara-business' ),
                'active_callback' => 'rara_business_banner_ac'
            )
        );

        // Selective refresh for banner link two label.
        $wp_customize->selective_refresh->add_partial( 'banner_link_two_label', array(
            'selector'            => '.banner .btn-holder a.btn-view-service',
            'render_callback'     => 'rara_business_banner_link_two_label_selective_refresh',
            'container_inclusive' => false,
            'fallback_refresh'    => true,
        ) );

        /** Banner link two url */
        $wp_customize->add_setting(
            'banner_link_two_url',
            array(
                'default'           => $default_options['banner_link_two_url'],
                'sanitize_callback' => 'esc_url_raw',
            )
        );

        $wp_customize->add_control(
            'banner_link_two_url',
            array(
                'section'         => 'header_image',
                'label'           => __( 'Link Two Url', 'rara-business' ),
                'type'            => 'url',
                'active_callback' => 'rara_business_banner_ac'
            )
        );
    }
endif;
add_action( 'customize_register', 'rara_business_customize_register_banner_section' );

if ( ! function_exists( 'rara_business_banner_ac' ) ) :
    /**
     * Active Callback
     */
    function rara_business_banner_ac( $control ){
        $banner      = $control->manager->get_setting( 'ed_banner_section' )->value();
        $control_id  = $control->id;
        
        // static banner controls
        if ( $control_id == 'header_image' && $banner == 'static_banner' ) return true;
        if ( $control_id == 'header_video' && $banner == 'static_banner' ) return true;
        if ( $control_id == 'external_header_video' && $banner == 'static_banner' ) return true;

        // banner title and description controls
        if ( $control_id == 'banner_title' && $banner == 'static_banner' ) return true;
        if ( $control_id == 'banner_description' && $banner == 'static_banner' ) return true;

        // Link button controls
        if ( $control_id == 'banner_link_one_label' && $banner == 'static_banner' ) return true;
        if ( $control_id == 'banner_link_one_url' && $banner == 'static_banner' ) return true;
        if ( $control_id == 'banner_link_two_label' && $banner == 'static_banner' ) return true;
        if ( $control_id == 'banner_link_two_url' && $banner == 'static_banner' ) return true;

        return false;
    }
endif;