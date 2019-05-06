<?php
/**
 * Rara Business Demo Content
 *
 * @package Rara_Business
 */

if ( ! function_exists( 'rara_business_customizer_demo_content' ) ) :
	/**
     * Add demo content info
     */
	function rara_business_customizer_demo_content( $wp_customize ) {
		
	    $wp_customize->add_section( 'demo_content_section' , array(
			'title'       => __( 'Demo Content Import' , 'rara-business' ),
			'priority'    => 7,
			));
	        
	    $wp_customize->add_setting(
			'demo_content_instruction',
			array(
				'sanitize_callback' => 'wp_kses_post'
			)
		);

	    /* translators: 1: string, 2: url, 3: string */
	    $demo_content_description = sprintf( '%1$s<a class="documentation" href="%2$s" target="_blank">%3$s</a>', esc_html__( 'Rara Business comes with demo content import feature. You can import the demo content with just one click. For step-by-step video tutorial, ', 'rara-business' ), esc_url( 'https://raratheme.com/blog/import-demo-content-rara-themes/' ), esc_html__( 'Click here', 'rara-business' ) );


		$wp_customize->add_control(
			new Rara_Business_Note_Control( 
				$wp_customize,
				'demo_content_instruction',
				array(
					'section'		=> 'demo_content_section',
					'description'	=> $demo_content_description
				)
			)
		);
	    
		$theme_demo_content_desc = '';

		if( ! class_exists( 'RDDI_init' ) ) {
			$theme_demo_content_desc .= '<span class="sticky_info_row"><label class="row-element">' . __( 'Plugin required', 'rara-business' ) . ': </label><a href="' . esc_url( 'https://wordpress.org/plugins/rara-one-click-demo-import/' ) . '" target="_blank">' . __( 'Rara One Click Demo Import', 'rara-business' ) . '</a></span><br />';
		}

		$theme_demo_content_desc .= '<span class="sticky_info_row download-link"><label class="row-element">' . __( 'Download Demo Content', 'rara-business' ) . ': </label><a href="' . esc_url( 'https://raratheme.com/documentation/rara-business/' ) . '" target="_blank">' . __( 'Click here', 'rara-business' ) . '</a></span><br />';

		$wp_customize->add_setting( 'theme_demo_content_info',array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
			));

		// Demo content 
		$wp_customize->add_control( new Rara_Business_Note_Control( $wp_customize ,'theme_demo_content_info',array(
			'section'     => 'demo_content_section',
			'description' => $theme_demo_content_desc
			)));

	}
endif;
add_action( 'customize_register', 'rara_business_customizer_demo_content' );