<?php
/**
 * Rara Business Theme Customizer Default Value.
 *
 * @package Rara_Business
 */

function rara_business_default_theme_options() {
	$default_options = array(
        
        // Header section
        'ed_header_contact_details' => true,
        'header_phone'              => '',
        'header_address'            => '',
        'header_email'              => '',
        'custom_link_icon'          => 'fa fa-edit',
        'custom_link_label'         => '',
        'custom_link'               => '#',
        
        // Social media section
        'ed_header_social_links'    => true,
        'header_social_links'       => array(),
        
        // Seo section
        'ed_post_update_date'       => true,
        'ed_breadcrumb'             => true,
        'home_text'                 => __( 'Home', 'rara-business' ),
        
        // Post/Page section
        'page_sidebar_layout'       => 'right-sidebar',
        'post_sidebar_layout'       => 'right-sidebar',
        'ed_excerpt'                => true,
        'excerpt_length'            => '55',
        'read_more_text'            => __( 'Read More', 'rara-business' ),
        'post_note_text'            => '',
        'ed_author'                 => false,
        'ed_related'                => true,
        'related_post_title'        => __( 'You may also like...', 'rara-business' ),
        'ed_popular_posts'          => true,
        'popular_post_title'        => __( 'Popular Posts', 'rara-business' ),
        'ed_post_date_meta'         => false,
        'ed_post_author_meta'       => false,
        'ed_featured_image'         => true,
        'ed_prefix_archive'         => false,
        
        // Banner section 
        'ed_banner_section'         => 'static_banner',
        'banner_title'              => __( 'Perfectionist at Every Level', 'rara-business' ),
        'banner_description'        => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.', 'rara-business' ),
        'banner_link_one_label'     => __( 'Free Inquiry', 'rara-business' ),
        'banner_link_one_url'       => '#',
        'banner_link_two_label'     => __( 'View Services', 'rara-business' ),
        'banner_link_two_url'       => '#',
        
        // Portfolio section
        'ed_portfolio_section'      => true,
        'portfolio_title'           => __( 'Our Case Studies', 'rara-business' ),
        'portfolio_description'     => __( 'It looks perfect on all major browsers, tablets and phones. The kind of product you&rsquo;re looking for. Phasellus lacus nibh, ullamcorper in pulvinar semper, mollis sed turpis.', 'rara-business' ),
        'portfolio_no_of_posts'     => '10',
        
        // Blog section
        'ed_blog_section'           => true,
        'blog_title'                => __( 'Our Blog', 'rara-business' ),
        'blog_description'          => __( 'It looks perfect on all major browsers, tablets and phones. The kind of product you &rsquo; re looking for.', 'rara-business' ),
	);

	$output = apply_filters( 'rara_business_default_theme_options', $default_options );

	return $output;
}