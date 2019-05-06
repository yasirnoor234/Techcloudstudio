<?php
/**
 * SEO Section
 *
 * @package Rara_Business
 */

if ( ! function_exists( 'rara_business_customize_register_seo_section' ) ) :
    /**
     * Add seo section controls
     */
    function rara_business_customize_register_seo_section( $wp_customize ) {
        /** Load default theme options */
        $default_options =  rara_business_default_theme_options();

        /** SEO Settings */
        $wp_customize->add_section(
            'seo_settings',
            array(
                'title'    => __( 'SEO Settings', 'rara-business' ),
                'priority' => 30,
                'panel'    => 'general_settings_panel',
            )
        );
        
        /** Enable updated date */
        $wp_customize->add_setting( 
            'ed_post_update_date', 
            array(
                'default'           => $default_options['ed_post_update_date'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
    		new Rara_Business_Toggle_Control( 
    			$wp_customize,
    			'ed_post_update_date',
    			array(
    				'section'     => 'seo_settings',
    				'label'	      => __( 'Enable Last Update Post Date', 'rara-business' ),
                    'description' => __( 'Enable to show last updated post date on listing as well as in single post.', 'rara-business' ),
    			)
    		)
    	);
        
        /** Enable Breadcrumb */
        $wp_customize->add_setting( 
            'ed_breadcrumb', 
            array(
                'default'           => $default_options['ed_breadcrumb'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
    		new Rara_Business_Toggle_Control( 
    			$wp_customize,
    			'ed_breadcrumb',
    			array(
    				'section'     => 'seo_settings',
    				'label'	      => __( 'Enable Breadcrumb', 'rara-business' ),
                    'description' => __( 'Enable to show breadcrumb in inner pages.', 'rara-business' ),
    			)
    		)
    	);
        
        /** Breadcrumb Home Text */
        $wp_customize->add_setting(
            'home_text',
            array(
                'default'           => $default_options['home_text'],
                'sanitize_callback' => 'sanitize_text_field' 
            )
        );
        
        $wp_customize->add_control(
            'home_text',
            array(
                'type'            => 'text',
                'section'         => 'seo_settings',
                'label'           => __( 'Breadcrumb Home Text', 'rara-business' ),
                'active_callback' => 'rara_business_breadcrumb_ac',
            )
        );
            
        /** SEO Settings Ends */
    }
endif;
add_action( 'customize_register', 'rara_business_customize_register_seo_section' );

if ( ! function_exists( 'rara_business_breadcrumb_ac' ) ) :
    /**
     * Active Callback
     */
    function rara_business_breadcrumb_ac( $control ) {
        $breadcrumb_control = $control->manager->get_setting( 'ed_breadcrumb' )->value();
        $control_id         = $control->id;

        if ( $control_id == 'home_text' && $breadcrumb_control ) return true;
    }
endif;