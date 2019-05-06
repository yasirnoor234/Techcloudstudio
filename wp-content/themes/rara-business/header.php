<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Rara_Business
 */
    /**
     * Doctype Hook
     * 
     * @hooked rara_business_doctype
    */
    do_action( 'rara_business_doctype' );
?>
<head itemscope itemtype="http://schema.org/WebSite">
	<?php 
    /**
     * Before wp_head
     * 
     * @hooked rara_business_head
    */
    do_action( 'rara_business_before_wp_head' );
    
    wp_head(); ?>
</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

<?php
    /**
     * Before Header
     * 
     * @hooked rara_business_page_start - 20 
    */
    do_action( 'rara_business_before_header' );
    
    /**
     * Header
     * 
     * @hooked rara_business_header - 20     
    */
    do_action( 'rara_business_header' );
    
    /**
     * Before Content
     * 
     * @hooked rara_business_banner - 15
    */
    do_action( 'rara_business_after_header' );
    
    /**
     * Content
     * 
     * @hooked rara_business_content_start
    */
    do_action( 'rara_business_content' );