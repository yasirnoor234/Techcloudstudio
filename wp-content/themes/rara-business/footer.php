<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Rara_Business
 */
    
    /**
     * After Content
     * 
     * @hooked rara_business_content_end - 20
    */
    do_action( 'rara_business_before_footer' );
    
    /**
     * Footer
     * 
     * @hooked rara_business_footer_start  - 20
     * @hooked rara_business_footer_top    - 30
     * @hooked rara_business_footer_bottom - 40
     * @hooked rara_business_footer_end    - 50
    */
    do_action( 'rara_business_footer' );
    
    /**
     * After Footer
     * 
     * @hooked rara_business_page_end    - 20
    */
    do_action( 'rara_business_after_footer' );
    
    wp_footer(); ?>
</body>
</html>
