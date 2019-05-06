<?php
/**
 * Post and Page Section
 *
 * @package Rara_Business
 */

if ( ! function_exists( 'rara_business_customize_register_post_page_section' ) ) :
    /**
     * Add social media section controls
     */
    function rara_business_customize_register_post_page_section( $wp_customize ) {
        /** Load default theme options */
        $default_options =  rara_business_default_theme_options();

        /** Posts(Blog) & Pages Settings */
        $wp_customize->add_section(
            'post_page_settings',
            array(
                'title'    => __( 'Posts(Blog) & Pages Settings', 'rara-business' ),
                'priority' => 40,
                'panel'    => 'general_settings_panel',
            )
        );
        
        /** Page Sidebar layout */
        $wp_customize->add_setting( 
            'page_sidebar_layout', 
            array(
                'default'           => $default_options['page_sidebar_layout'],
                'sanitize_callback' => 'rara_business_sanitize_radio'
            ) 
        );
        
        $wp_customize->add_control(
    		new Rara_Business_Radio_Image_Control(
    			$wp_customize,
    			'page_sidebar_layout',
    			array(
    				'section'	  => 'post_page_settings',
    				'label'		  => __( 'Page Sidebar Layout', 'rara-business' ),
    				'description' => __( 'This is the general sidebar layout for pages. You can override the sidebar layout for individual page in repective page.', 'rara-business' ),
    				'choices'	  => array(
    					'no-sidebar'    => get_template_directory_uri() . '/images/no-sidebar.png',
    					'left-sidebar'  => get_template_directory_uri() . '/images/left-sidebar.png',
                        'right-sidebar' => get_template_directory_uri() . '/images/right-sidebar.png',
    				)
    			)
    		)
    	);
        
        /** Post Sidebar layout */
        $wp_customize->add_setting( 
            'post_sidebar_layout', 
            array(
                'default'           => $default_options['post_sidebar_layout'],
                'sanitize_callback' => 'rara_business_sanitize_radio'
            ) 
        );
        
        $wp_customize->add_control(
    		new Rara_Business_Radio_Image_Control(
    			$wp_customize,
    			'post_sidebar_layout',
    			array(
    				'section'	  => 'post_page_settings',
    				'label'		  => __( 'Post Sidebar Layout', 'rara-business' ),
    				'description' => __( 'This is the general sidebar layout for posts. You can override the sidebar layout for individual post in repective post.', 'rara-business' ),
    				'choices'	  => array(
    					'no-sidebar'    => get_template_directory_uri() . '/images/no-sidebar.png',
    					'left-sidebar'  => get_template_directory_uri() . '/images/left-sidebar.png',
                        'right-sidebar' => get_template_directory_uri() . '/images/right-sidebar.png',
    				)
    			)
    		)
    	);
        
        /** Blog Excerpt */
        $wp_customize->add_setting( 
            'ed_excerpt', 
            array(
                'default'           => $default_options['ed_excerpt'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
    		new Rara_Business_Toggle_Control( 
    			$wp_customize,
    			'ed_excerpt',
    			array(
    				'section'     => 'post_page_settings',
    				'label'	      => __( 'Enable Blog Excerpt', 'rara-business' ),
                    'description' => __( 'Enable to show excerpt or disable to show full post content.', 'rara-business' ),
    			)
    		)
    	);
        
        /** Excerpt Length */
        $wp_customize->add_setting( 
            'excerpt_length', 
            array(
                'default'           => $default_options['excerpt_length'],
                'sanitize_callback' => 'rara_business_sanitize_number_absint'
            ) 
        );
        
        $wp_customize->add_control(
    		new Rara_Business_Slider_Control( 
    			$wp_customize,
    			'excerpt_length',
    			array(
    				'section'	  => 'post_page_settings',
    				'label'		  => __( 'Excerpt Length', 'rara-business' ),
    				'description' => __( 'Automatically generated excerpt length (in words).', 'rara-business' ),
                    'choices'	  => array(
    					'min' 	=> 10,
    					'max' 	=> 100,
    					'step'	=> 5,
    				)                 
    			)
    		)
    	);
        
        /** Read More Text */
        $wp_customize->add_setting(
            'read_more_text',
            array(
                'default'           => $default_options['read_more_text'],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage' 
            )
        );
        
        $wp_customize->add_control(
            'read_more_text',
            array(
                'type'    => 'text',
                'section' => 'post_page_settings',
                'label'   => __( 'Read More Text', 'rara-business' ),
            )
        );
        
        $wp_customize->selective_refresh->add_partial( 'read_more_text', array(
            'selector' => '.entry-footer a.btn-readmore',
            'render_callback' => 'rara_business_readmore_label_selective_refresh',
        ) );

       /** Hide Posted Date */
        $wp_customize->add_setting( 
            'ed_post_date_meta', 
            array(
                'default'           => $default_options['ed_post_date_meta'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
            new Rara_Business_Toggle_Control( 
                $wp_customize,
                'ed_post_date_meta',
                array(
                    'section'     => 'post_page_settings',
                    'label'       => __( 'Hide Posted Date Meta', 'rara-business' ),
                    'description' => __( 'Enable to hide posted date.', 'rara-business' ),
                )
            )
        );

        /** Hide Posted Date */
        $wp_customize->add_setting( 
            'ed_post_author_meta', 
            array(
                'default'           => $default_options['ed_post_author_meta'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
            new Rara_Business_Toggle_Control( 
                $wp_customize,
                'ed_post_author_meta',
                array(
                    'section'     => 'post_page_settings',
                    'label'       => __( 'Hide Author Meta', 'rara-business' ),
                    'description' => __( 'Enable to hide author meta.', 'rara-business' ),
                )
            )
        );

        /** Prefix Archive Page */
        $wp_customize->add_setting( 
            'ed_prefix_archive', 
            array(
                'default'           => $default_options['ed_prefix_archive'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
            new Rara_Business_Toggle_Control( 
                $wp_customize,
                'ed_prefix_archive',
                array(
                    'section'     => 'post_page_settings',
                    'label'       => __( 'Hide Prefix in Archive Page', 'rara-business' ),
                    'description' => __( 'Enable to hide prefix in archive page.', 'rara-business' ),
                )
            )
        );
        
        /** Note */
        $wp_customize->add_setting(
            'post_note_text',
            array(
                'default'           => $default_options['post_note_text'],
                'sanitize_callback' => 'wp_kses_post' 
            )
        );
        
        $wp_customize->add_control(
            new Rara_Business_Note_Control( 
    			$wp_customize,
    			'post_note_text',
    			array(
    				'section'	  => 'post_page_settings',
    				'description' => __( '<hr/>These options affect your individual posts.', 'rara-business' ),
    			)
    		)
        );
        
        /** Hide Author */
        $wp_customize->add_setting( 
            'ed_author', 
            array(
                'default'           => $default_options['ed_author'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
    		new Rara_Business_Toggle_Control( 
    			$wp_customize,
    			'ed_author',
    			array(
    				'section'     => 'post_page_settings',
    				'label'	      => __( 'Hide Author', 'rara-business' ),
                    'description' => __( 'Enable to hide author section.', 'rara-business' ),
    			)
    		)
    	);
        
        /** Show Related Posts */
        $wp_customize->add_setting( 
            'ed_related', 
            array(
                'default'           => $default_options['ed_related'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
    		new Rara_Business_Toggle_Control( 
    			$wp_customize,
    			'ed_related',
    			array(
    				'section'     => 'post_page_settings',
    				'label'	      => __( 'Show Related Posts', 'rara-business' ),
                    'description' => __( 'Enable to show related posts in single page.', 'rara-business' ),
    			)
    		)
    	);
        
        /** Related Posts section title */
        $wp_customize->add_setting(
            'related_post_title',
            array(
                'default'           => $default_options['related_post_title'],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage' 
            )
        );
        
        $wp_customize->add_control(
            'related_post_title',
            array(
                'type'            => 'text',
                'section'         => 'post_page_settings',
                'label'           => __( 'Related Posts Section Title', 'rara-business' ),
                'active_callback' => 'rara_business_page_post_section_ac'
            )
        );
        
        $wp_customize->selective_refresh->add_partial( 'related_post_title', array(
            'selector' => '.related-post h2.section-title',
            'render_callback' => 'rara_business_related_post_section_title_selective_refresh',
        ) );
        

        /** Show Popular Posts */
        $wp_customize->add_setting( 
            'ed_popular_posts', 
            array(
                'default'           => $default_options['ed_popular_posts'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
            new Rara_Business_Toggle_Control( 
                $wp_customize,
                'ed_popular_posts',
                array(
                    'section'     => 'post_page_settings',
                    'label'       => __( 'Show Popular Posts', 'rara-business' ),
                    'description' => __( 'Enable to show popular posts in single page.', 'rara-business' ),
                )
            )
        );
        
        /** Popular Posts section title */
        $wp_customize->add_setting(
            'popular_post_title',
            array(
                'default'           => $default_options['popular_post_title'],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage' 
            )
        );
        
        $wp_customize->add_control(
            'popular_post_title',
            array(
                'type'            => 'text',
                'section'         => 'post_page_settings',
                'label'           => __( 'Popular Posts Section Title', 'rara-business' ),
                'active_callback' => 'rara_business_page_post_section_ac'
            )
        );
        
        $wp_customize->selective_refresh->add_partial( 'popular_post_title', array(
            'selector' => '.popular-post h2.section-title',
            'render_callback' => 'rara_business_popular_post_section_title_selective_refresh',
        ) );

        /** Show Featured Image */
        $wp_customize->add_setting( 
            'ed_featured_image', 
            array(
                'default'           => $default_options['ed_featured_image'],
                'sanitize_callback' => 'rara_business_sanitize_checkbox'
            ) 
        );
        
        $wp_customize->add_control(
    		new Rara_Business_Toggle_Control( 
    			$wp_customize,
    			'ed_featured_image',
    			array(
    				'section'     => 'post_page_settings',
    				'label'	      => __( 'Show Featured Image', 'rara-business' ),
                    'description' => __( 'Enable to show featured image in post detail (single page).', 'rara-business' ),
    			)
    		)
    	);
        
        /** Posts(Blog) & Pages Settings Ends */
    }
endif;
add_action( 'customize_register', 'rara_business_customize_register_post_page_section' );

if ( ! function_exists( 'rara_business_page_post_section_ac' ) ) :
    /**
     * Active Callback
     */
    function rara_business_page_post_section_ac( $control ) {
        $ed_related_post = $control->manager->get_setting( 'ed_related' )->value();
        $ed_popular_post = $control->manager->get_setting( 'ed_popular_posts' )->value();
        $control_id        = $control->id;

        if ( $control_id == 'related_post_title' && $ed_related_post ) return true;
        if ( $control_id == 'popular_post_title' && $ed_popular_post ) return true;
    }
endif;