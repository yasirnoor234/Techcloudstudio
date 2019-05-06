<?php
/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 * @package Rara_Business
 */
function rara_business_widgets_init() {
	
    $sidebars = array(
        'sidebar' => array(
            'name'        => __( 'Sidebar', 'rara-business' ),
            'id'          => 'sidebar', 
            'description' => __( 'Default Sidebar', 'rara-business' ),
        ),
        'services' => array(
            'name'        => __( 'Services Widget', 'rara-business' ),
            'id'          => 'services', 
            'description' => __( 'Add "Text" widget for the title and description. Add "Rara: Icon Text" widget for the services.', 'rara-business' ),
        ),
        'about' => array(
            'name'        => __( 'About Widget', 'rara-business' ),
            'id'          => 'about', 
            'description' => __( 'Add "Rara: Featured Page Widget" for about section.', 'rara-business' ),
        ),
        'choose-us' => array(
            'name'        => __( 'Why Choose Us Widget', 'rara-business' ),
            'id'          => 'choose-us', 
            'description' => __( 'Add "Text" widget for the title and description. Add "Rara: Icon Text" widget for the choos us reasons. Add "Image" widget for featured image.', 'rara-business' ),
        ),
        'team' => array(
            'name'        => __( 'Team Widget', 'rara-business' ),
            'id'          => 'team', 
            'description' => __( 'Add "Text" widget for the title and description. Add "Rara: Team Member" widget for the team members.', 'rara-business' ),
        ),
        'testimonial' => array(
            'name'        => __( 'Testimonial Widget', 'rara-business' ),
            'id'          => 'testimonial', 
            'description' => __( 'Add "Text" widget for the title and description. Add "Rara: Testimonial" widget for the testimonials.', 'rara-business' ),
        ),
        'stats' => array(
            'name'        => __( 'Stat Counter Widget', 'rara-business' ),
            'id'          => 'stats', 
            'description' => __( 'Add "Text" widget for the title and description. Add "Rara: Stat Counter Widget" for the statistics.', 'rara-business' ),
        ),
        'cta' => array(
            'name'        => __( 'Call To Action Widget', 'rara-business' ),
            'id'          => 'cta', 
            'description' => __( 'Add "Rara : Call To Action widget" for the title, description and call to action buttons.', 'rara-business' ),
        ),
        'faq' => array(
            'name'        => __( 'FAQs Widget', 'rara-business' ),
            'id'          => 'faq', 
            'description' => __( 'Add "Text" widget for the title and description. Add "Rara: FAQs" widget for frequently asked questions.', 'rara-business' ),
        ),
        'client' => array(
            'name'        => __( 'Clients Widget', 'rara-business' ),
            'id'          => 'client', 
            'description' => __( 'Add "Rara: Client Logo" widget for client logos.', 'rara-business' ),
        ),
        'footer-one'=> array(
            'name'        => __( 'Footer One', 'rara-business' ),
            'id'          => 'footer-one', 
            'description' => __( 'Add footer one widgets here.', 'rara-business' ),
        ),
        'footer-two'=> array(
            'name'        => __( 'Footer Two', 'rara-business' ),
            'id'          => 'footer-two', 
            'description' => __( 'Add footer two widgets here.', 'rara-business' ),
        ),
        'footer-three'=> array(
            'name'        => __( 'Footer Three', 'rara-business' ),
            'id'          => 'footer-three', 
            'description' => __( 'Add footer three widgets here.', 'rara-business' ),
        ),
        'footer-four'=> array(
            'name'        => __( 'Footer Four', 'rara-business' ),
            'id'          => 'footer-four', 
            'description' => __( 'Add footer four widgets here.', 'rara-business' ),
        )
    );
    
    foreach( $sidebars as $sidebar ){
        register_sidebar( array(
    		'name'          => esc_html( $sidebar['name'] ),
    		'id'            => esc_attr( $sidebar['id'] ),
    		'description'   => esc_html( $sidebar['description'] ),
    		'before_widget' => '<section id="%1$s" class="widget %2$s">',
    		'after_widget'  => '</section>',
    		'before_title'  => '<h2 class="widget-title" itemprop="name">',
    		'after_title'   => '</h2>',
    	) );
    }
}
add_action( 'widgets_init', 'rara_business_widgets_init' );

if( ! function_exists( 'rara_business_recent_post_thumbnail' ) ):
    /**
     * Filter to modify recent post widget thumbnail image
     */
    function rara_business_recent_post_thumbnail( $size ){
        return $size = "rara-business-blog";
    }
endif;
add_filter( 'rara_recent_img_size', 'rara_business_recent_post_thumbnail' );