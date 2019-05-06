<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Rara_Business
 */

get_header(); ?>
    <header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Page not found.', 'rara-business' ); ?></h1>
	</header>
	<div class="error-holder">
		<div class="img-holder"><img src="<?php echo esc_url( trailingslashit( get_template_directory_uri() ) . 'images/error-img.jpg' ); ?>" alt="<?php esc_attr_e( '404 page not found', 'rara-business' ); ?>"></div>
		<div class="text-holder">
			<h2><?php esc_html_e( 'The page you are looking for might have been removed, has its name changed, or is temporarily unavailable.', 'rara-business' ); ?></h2>
			<span><?php esc_html_e( 'Please try using our search box below.', 'rara-business' ); ?></span>
			<?php get_search_form(); ?>
			<a href="<?php echo esc_url( home_url('/') ); ?>" class="btn-home"><?php esc_html_e( 'Back to homepage', 'rara-business' ); ?></a>
		</div>
	</div>
    
    <?php
    /**
     * Recent Posts
     * 
     * @hooked rara_business_recent_posts
    */
    do_action( 'rara_business_recent_post' );
    
get_footer();