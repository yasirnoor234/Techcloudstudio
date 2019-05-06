<?php
/**
 * Portfolio Section
 *
 * @package Rara_Business
 */

if ( ! function_exists( 'rara_business_customize_register_portfolio_section' ) ) :
    /**
     * Add portfolio section controls
     */
    function rara_business_customize_register_portfolio_section( $wp_customize ) {
        /** Load default theme options */
        $default_options =  rara_business_default_theme_options();

        /** Portfolio Section */
        $wp_customize->add_section(
            'portfolio_section',
            array(
                'title'    => __( 'Portfolio Section', 'rara-business' ),
                'priority' => 77,
                'panel'    => 'frontpage_panel',
            )
        );

        /** Portfolio Options */
        $wp_customize->add_setting(
            'ed_portfolio_section',
            array(
                'default'           => $default_options['ed_portfolio_section'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            )
        );

        $wp_customize->add_control(
            new Rara_Business_Toggle_Control(
                $wp_customize,
                'ed_portfolio_section',
                array(
                    'label'       => __( 'Enable Portfolio Section', 'rara-business' ),
                    'description' => __( 'Enable to show portfolio section.', 'rara-business' ),
                    'section'     => 'portfolio_section',
                    'priority'    => 5 
                )            
            )
        );

        if ( rara_business_is_rara_theme_companion_activated() ) {
            /** Portfolio title */
            $wp_customize->add_setting(
                'portfolio_title',
                array(
                    'default'           => $default_options['portfolio_title'],
                    'sanitize_callback' => 'sanitize_text_field',
                    'transport'         => 'postMessage'
                )
            );
            
            $wp_customize->add_control(
                'portfolio_title',
                array(
                    'section'         => 'portfolio_section',
                    'label'           => __( 'Portfolio Title', 'rara-business' ),
                    'active_callback' => 'rara_business_portfolio_ac'
                )
            );

            // Selective refresh for portfolio title.
            $wp_customize->selective_refresh->add_partial( 'portfolio_title', array(
                'selector'            => '.portfolio .widget_text h2.widget-title',
                'render_callback'     => 'rara_business_portfolio_title_selective_refresh',
                'container_inclusive' => false,
                'fallback_refresh'    => true,
            ) );

            /** Portfolio description */
            $wp_customize->add_setting(
                'portfolio_description',
                array(
                    'default'           => $default_options['portfolio_description'],
                    'sanitize_callback' => 'sanitize_text_field',
                    'transport'         => 'postMessage'
                )
            );
            
            $wp_customize->add_control(
                'portfolio_description',
                array(
                    'section'         => 'portfolio_section',
                    'label'           => __( 'Portfolio Description', 'rara-business' ),
                    'active_callback' => 'rara_business_portfolio_ac'
                )
            );

            // Selective refresh for portfolio description.
            $wp_customize->selective_refresh->add_partial( 'portfolio_description', array(
                'selector'            => '.portfolio .textwidget p',
                'render_callback'     => 'rara_business_portfolio_description_selective_refresh',
                'container_inclusive' => false,
                'fallback_refresh'    => true,
            ) );

            /** Number Of Portfolio Posts */
            $wp_customize->add_setting(
                'portfolio_no_of_posts',
                array(
                    'default'           => $default_options['portfolio_no_of_posts'],
                    'sanitize_callback' => 'rara_business_sanitize_select'
                )
            );

            $wp_customize->add_control(
                new Rara_Business_Select_Control(
                    $wp_customize,
                    'portfolio_no_of_posts',
                    array(
                        'label'       => __( 'Number of Posts', 'rara-business' ),
                        'description' => __( 'Choose number of portfolio posts to be displayed.', 'rara-business' ),
                        'section'     => 'portfolio_section',
                        'choices'     => array(
                            '5'     => __( '5', 'rara-business' ),
                            '10'    => __( '10', 'rara-business' ),
                        ),
                        'active_callback' => 'rara_business_portfolio_ac'
                    )            
                )
            );
        } else {
            /** Activate RaraTheme Companion Plugin Note */
            $wp_customize->add_setting(
                'portfolio_note',
                array(
                    'sanitize_callback' => 'wp_kses_post' 
                )
            );
            
            $wp_customize->add_control(
                new Rara_Business_Note_Control( 
                    $wp_customize,
                    'portfolio_note',
                    array(
                        'section'     => 'portfolio_section',
                        /* translators: 1: link start, 2: link close  */
                        'description' => sprintf( __( 'Please install and activate the recommended plugin %1$sRaraTheme Companion%2$s.', 'rara-business' ), '<a href="' . esc_url( admin_url( 'themes.php?page=tgmpa-install-plugins' ) ) . '" target="_blank">', '</a>' ),
                    )
                )
            );
        }
    }
endif;
add_action( 'customize_register', 'rara_business_customize_register_portfolio_section' );

if ( ! function_exists( 'rara_business_portfolio_ac' ) ) :
    /**
     * Active Callback
     */
    function rara_business_portfolio_ac( $control ){
        $show_portfolio = $control->manager->get_setting( 'ed_portfolio_section' )->value();
        $control_id     = $control->id;

        // Portfolio title, description and number of posts controls
        if ( $control_id == 'portfolio_title' &&  $show_portfolio ) return true;
        if ( $control_id == 'portfolio_description' &&  $show_portfolio ) return true;
        if ( $control_id == 'portfolio_no_of_posts' &&  $show_portfolio ) return true;

        return false;
    }
endif;