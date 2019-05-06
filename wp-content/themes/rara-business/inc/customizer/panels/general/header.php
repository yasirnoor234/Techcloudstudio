<?php
/**
 * Header Section
 *
 * @package Rara_Business
 */

if ( ! function_exists( 'rara_business_customize_register_header_section' ) ) :
    /**
     * Add header section controls
     */
    function rara_business_customize_register_header_section( $wp_customize ) {
        /** Load default theme options */
        $default_options =  rara_business_default_theme_options();

        /** Header Section */
        $wp_customize->add_section(
            'header_section',
            array(
                'title'    => __( 'Header Section', 'rara-business' ),
                'priority' => 10,
                'panel'    => 'general_settings_panel',
            )
        );

        /** Enable header top section */
        $wp_customize->add_setting( 
            'ed_header_contact_details', 
            array(
                'default'           => $default_options['ed_header_contact_details'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
            new Rara_Business_Toggle_Control( 
                $wp_customize,
                'ed_header_contact_details',
                array(
                    'section'     => 'header_section',
                    'label'       => __( 'Enable Header Contact Details', 'rara-business' ),
                    'description' => __( 'Enable to show contact details in header top section.', 'rara-business' ),
                )
            )
        );

        /** Phone number  */
        $wp_customize->add_setting(
            'header_phone',
            array(
                'default'           => $default_options['header_phone'],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage'
            )
        );

        $wp_customize->selective_refresh->add_partial( 'header_phone', array(
            'selector' => '.header-t .phone a.tel-link',
            'render_callback' => 'rara_business_header_phone_selective_refresh',
        ) );
        
        $wp_customize->add_control(
            'header_phone',
            array(
                'label'           => __( 'Phone Number', 'rara-business' ),
                'description'     => __( 'Add phone no. in header.', 'rara-business' ),
                'section'         => 'header_section',
                'type'            => 'text',
                'active_callback' => 'rara_business_header_top_section_ac'
            )
        );
        
        /** Address  */
        $wp_customize->add_setting(
            'header_address',
            array(
                'default'           => $default_options['header_address'],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage'
            )
        );
        
        $wp_customize->selective_refresh->add_partial( 'header_address', array(
            'selector'        => '.header-t .address address',
            'render_callback' => 'rara_business_header_address_selective_refresh',
        ) );

        $wp_customize->add_control(
            'header_address',
            array(
                'label'           => __( 'Address', 'rara-business' ),
                'description'     => __( 'Add address in header.', 'rara-business' ),
                'section'         => 'header_section',
                'type'            => 'text',
                'active_callback' => 'rara_business_header_top_section_ac'
            )
        );
        
        /** Email */
        $wp_customize->add_setting(
            'header_email',
            array(
                'default'           => $default_options['header_email'],
                'sanitize_callback' => 'sanitize_email',
                'transport'         => 'postMessage'
            )
        );

        $wp_customize->selective_refresh->add_partial( 'header_email', array(
            'selector'        => '.header-t .email a.email-link',
            'render_callback' => 'rara_business_header_email_selective_refresh',
        ) );
        
        $wp_customize->add_control(
            'header_email',
            array(
                'label'           => __( 'Email', 'rara-business' ),
                'description'     => __( 'Add email in header.', 'rara-business' ),
                'section'         => 'header_section',
                'type'            => 'text',
                'active_callback' => 'rara_business_header_top_section_ac'
            )
        );

        /** Enable Social Links */
        $wp_customize->add_setting( 
            'ed_header_social_links', 
            array(
                'default'           => $default_options['ed_header_social_links'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
            new Rara_Business_Toggle_Control( 
                $wp_customize,
                'ed_header_social_links',
                array(
                    'section'     => 'header_section',
                    'label'       => __( 'Enable Social Links', 'rara-business' ),
                    'description' => __( 'Enable to show social links at header.', 'rara-business' ),
                )
            )
        );
        
        $wp_customize->add_setting( 
            new Rara_Business_Repeater_Setting( 
                $wp_customize, 
                'header_social_links', 
                array(
                    'default' => $default_options['header_social_links'],
                    'sanitize_callback' => array( 'Rara_Business_Repeater_Setting', 'sanitize_repeater_setting' ),
                ) 
            ) 
        );
        
        $wp_customize->add_control(
            new Rara_Business_Control_Repeater(
                $wp_customize,
                'header_social_links',
                array(
                    'section' => 'header_section',               
                    'label'   => __( 'Social Links', 'rara-business' ),
                    'fields'  => array(
                        'font' => array(
                            'type'        => 'font',
                            'label'       => __( 'Font Awesome Icon', 'rara-business' ),
                            'description' => __( 'Example: fa-bell', 'rara-business' ),
                        ),
                        'link' => array(
                            'type'        => 'url',
                            'label'       => __( 'Link', 'rara-business' ),
                            'description' => __( 'Example: http://facebook.com', 'rara-business' ),
                        )
                    ),
                    'row_label' => array(
                        'type'  => 'field',
                        'value' => __( 'links', 'rara-business' ),
                        'field' => 'link'
                    ),
                    'choices'   => array(
                        'limit' => 10
                    ),             
                    'active_callback' => 'rara_business_header_top_section_ac',                 
                )
            )
        );

        /** Custom Link Icon  */
        $wp_customize->add_setting(
            'custom_link_icon',
            array(
                'default'           => $default_options['custom_link_icon'],
                'sanitize_callback' => 'sanitize_text_field',
            )
        );
        
        $wp_customize->add_control(
            'custom_link_icon',
            array(
                'type'            => 'font',
                'label'           => __( 'Custom Link Icon', 'rara-business' ),
                'description'     => __( 'Insert Icon eg. fa fa-edit.', 'rara-business' ),
                'section'         => 'header_section',
            )
        );

        /** Custom Link label  */
        $wp_customize->add_setting(
            'custom_link_label',
            array(
                'default'           => $default_options['custom_link_label'],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage'
            )
        );

        $wp_customize->selective_refresh->add_partial( 'custom_link_label', array(
            'selector' => '.main-header .right .btn-buy',
            'render_callback' => 'rara_business_header_custom_link_label_selective_refresh',
        ) );
        
        $wp_customize->add_control(
            'custom_link_label',
            array(
                'label'           => __( 'Custom Link Label', 'rara-business' ),
                'description'     => __( 'Add cutom link button label in header.', 'rara-business' ),
                'section'         => 'header_section',
                'type'            => 'text',
            )
        );

        /** Custom Link */
        $wp_customize->add_setting(
            'custom_link',
            array(
                'default'           => $default_options['custom_link'],
                'sanitize_callback' => 'esc_url_raw',
            )
        );
        
        $wp_customize->add_control(
            'custom_link',
            array(
                'label'           => __( 'Custom link', 'rara-business' ),
                'description'     => __( 'Add custom link in header.', 'rara-business' ),
                'section'         => 'header_section',
                'type'            => 'url',
            )
        );
    }
endif;
add_action( 'customize_register', 'rara_business_customize_register_header_section' );

if ( ! function_exists( 'rara_business_header_top_section_ac' ) ) :
    /**
     * Active Callback
     */
    function rara_business_header_top_section_ac( $control ){
        $ed_header_top        = $control->manager->get_setting( 'ed_header_contact_details' )->value();
        $social_media_control = $control->manager->get_setting( 'ed_header_social_links' )->value();
        $control_id           = $control->id;

        // Phone number, Address, Email and Custom Link controls
        if ( $control_id == 'header_phone' && $ed_header_top ) return true;
        if ( $control_id == 'header_address' && $ed_header_top ) return true;
        if ( $control_id == 'header_email' && $ed_header_top ) return true;
        if ( $control_id == 'header_social_links' && $social_media_control ) return true;

        return false;
    }
endif;