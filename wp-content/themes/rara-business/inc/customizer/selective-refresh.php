<?php
/**
 * Rara Business Customizer selective refresh functions.
 *
 * @package Rara_Business
 *
 */

if ( ! function_exists( 'rara_business_customize_partial_blogname' ) ) :
	/**
	 * Render the site title for the selective refresh partial.
	 *
	 */
	function rara_business_customize_partial_blogname() {
		$blog_name = get_bloginfo( 'name' );

		if ( $blog_name ){
			return esc_html( $blog_name );
		} else {
			return false;
		}

	}
endif;

if ( ! function_exists( 'rara_business_customize_partial_blogdescription' ) ) :
	/**
	 * Render the site description for the selective refresh partial.
	 *
	 */
	function rara_business_customize_partial_blogdescription() {
		$blog_description = get_bloginfo( 'description' );

		if ( $blog_description ){
			return esc_html( $blog_description );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_banner_title_selective_refresh' ) ) :
	/**
	 * Render banner title selective refresh partial.
	 *
	 */
	function rara_business_banner_title_selective_refresh() {
		/** Load default theme options */
		$default_options = rara_business_default_theme_options();                          
		$banner_title    = get_theme_mod( 'banner_title', $default_options['banner_title'] );

		if ( $banner_title ){
			return esc_html( $banner_title );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_banner_description_selective_refresh' ) ) :
	/**
	 * Render banner description selective refresh partial.
	 *
	 */
	function rara_business_banner_description_selective_refresh() {
		/** Load default theme options */
		$default_options    = rara_business_default_theme_options();                          
		$banner_description = get_theme_mod( 'banner_description', $default_options['banner_description'] );

		if ( $banner_description ){
			return esc_html( $banner_description );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_banner_link_one_label_selective_refresh' ) ) :
	/**
	 * Render banner link one label selective refresh partial.
	 *
	 */
	function rara_business_banner_link_one_label_selective_refresh() {
		/** Load default theme options */
		$default_options       = rara_business_default_theme_options();                          
		$banner_link_one_label = get_theme_mod( 'banner_link_one_label', $default_options['banner_link_one_label'] );

		if ( $banner_link_one_label ){
			return esc_html( $banner_link_one_label );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_banner_link_two_label_selective_refresh' ) ) :
	/**
	 * Render banner link two label selective refresh partial.
	 *
	 */
	function rara_business_banner_link_two_label_selective_refresh() {
		/** Load default theme options */
		$default_options       = rara_business_default_theme_options();                          
		$banner_link_two_label = get_theme_mod( 'banner_link_two_label', $default_options['banner_link_two_label'] );

		if ( $banner_link_two_label ){
			return esc_html( $banner_link_two_label );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_portfolio_title_selective_refresh' ) ) :
	/**
	 * Render portfolio title selective refresh partial.
	 *
	 */
	function rara_business_portfolio_title_selective_refresh() {
		/** Load default theme options */
		$default_options = rara_business_default_theme_options();                          
		$portfolio_title = get_theme_mod( 'portfolio_title', $default_options['portfolio_title'] );

		if ( $portfolio_title ){
			return esc_html( $portfolio_title );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_portfolio_description_selective_refresh' ) ) :
	/**
	 * Render portfolio description selective refresh partial.
	 *
	 */
	function rara_business_portfolio_description_selective_refresh() {
		/** Load default theme options */
		$default_options       = rara_business_default_theme_options();                          
		$portfolio_description = get_theme_mod( 'portfolio_description', $default_options['portfolio_description'] );

		if ( $portfolio_description ){
			return esc_html( $portfolio_description );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_blog_title_selective_refresh' ) ) :
	/**
	 * Render blog title selective refresh partial.
	 *
	 */
	function rara_business_blog_title_selective_refresh() {
		/** Load default theme options */
		$default_options = rara_business_default_theme_options();                          
		$blog_title      = get_theme_mod( 'blog_title', $default_options['blog_title'] );

		if ( $blog_title ){
			return esc_html( $blog_title );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_blog_description_selective_refresh' ) ) :
	/**
	 * Render blog description selective refresh partial.
	 *
	 */
	function rara_business_blog_description_selective_refresh() {
		/** Load default theme options */
		$default_options  = rara_business_default_theme_options();                          
		$blog_description = get_theme_mod( 'blog_description', $default_options['blog_description'] );

		if ( $blog_description ){
			return esc_html( $blog_description );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_header_phone_selective_refresh' ) ) :
	/**
	 * Render header phone number selective refresh partial.
	 *
	 */
	function rara_business_header_phone_selective_refresh() {
		/** Load default theme options */
		$default_options = rara_business_default_theme_options();                          
		$phone_number = get_theme_mod( 'header_phone', $default_options['header_phone'] );

		if ( $phone_number ){
			return esc_html( $phone_number );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_header_address_selective_refresh' ) ) :
	/**
	 * Render header address number selective refresh partial.
	 *
	 */
	function rara_business_header_address_selective_refresh() {
		/** Load default theme options */
		$default_options = rara_business_default_theme_options();                          
		$address = get_theme_mod( 'header_address', $default_options['header_address'] );

		if ( $address ){
			return esc_html( $address );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_header_email_selective_refresh' ) ) :
	/**
	 * Render header email number selective refresh partial.
	 *
	 */
	function rara_business_header_email_selective_refresh() {
		/** Load default theme options */
		$default_options = rara_business_default_theme_options();                          
		$email = get_theme_mod( 'header_email', $default_options['header_email'] );

		if ( $email ){
			return esc_html( $email );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_header_custom_link_label_selective_refresh' ) ) :
	/**
	 * Render custom link label selective refresh partial.
	 *
	 */
	function rara_business_header_custom_link_label_selective_refresh() {
		/** Load default theme options */
		$default_options   = rara_business_default_theme_options();                          
		$custom_link_label = get_theme_mod( 'custom_link_label', $default_options['custom_link_label'] );

		if ( $custom_link_label ){
			return esc_html( $custom_link_label );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_readmore_label_selective_refresh' ) ) :
	/**
	 * Render readmore label selective refresh partial.
	 *
	 */
	function rara_business_readmore_label_selective_refresh() {
		/** Load default theme options */
		$default_options = rara_business_default_theme_options();                          
		$readmore_label  = get_theme_mod( 'read_more_text', $default_options['read_more_text'] );

		if ( $readmore_label ){
			return esc_html( $readmore_label );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_related_post_section_title_selective_refresh' ) ) :
	/**
	 * Render related post section title for selective refresh partial.
	 *
	 */
	function rara_business_related_post_section_title_selective_refresh() {
		/** Load default theme options */
		$default_options    = rara_business_default_theme_options();                          
		$related_post_title = get_theme_mod( 'related_post_title', $default_options['related_post_title'] );

		if ( $related_post_title ){
			return esc_html( $related_post_title );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'rara_business_popular_post_section_title_selective_refresh' ) ) :
	/**
	 * Render popular post section title for selective refresh partial.
	 *
	 */
	function rara_business_popular_post_section_title_selective_refresh() {
		/** Load default theme options */
		$default_options    = rara_business_default_theme_options();                          
		$popular_post_title = get_theme_mod( 'popular_post_title', $default_options['popular_post_title'] );

		if ( $popular_post_title ){
			return esc_html( $popular_post_title );
		} else {
			return false;
		}
	}
endif;


if( ! function_exists( 'rara_business_get_footer_copyright' ) ) :
	/**
	 * Footer Copyright
	 */
	function rara_business_get_footer_copyright(){
	    $copyright = get_theme_mod( 'footer_copyright' );
	    echo '<span class="copyright">';
	    if( $copyright ){
	        echo wp_kses_post( $copyright );
	    }else{
	        esc_html_e( 'Copyright &copy; ', 'rara-business' );
	        echo date_i18n( esc_html__( 'Y', 'rara-business' ) );
	        echo ' <a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>. ';
	    }
	    echo '</span>'; 
	}
endif;