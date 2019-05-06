<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Rara_Business
 */

$sidebar_layout = rara_business_sidebar_layout();

get_header(); ?>

	<div id="primary" class="content-area">
    	<main id="main" class="site-main">

    	<?php
    	while ( have_posts() ) : the_post();

    		get_template_part( 'template-parts/content', 'single' );

    	endwhile; // End of the loop.
    	?>

    	</main><!-- #main -->
        
        <?php
        /**
         * @hooked rara_business_author        - 15 
         * @hooked rara_business_pagination    - 20
         * @hooked rara_business_related_posts - 25
         * @hooked rara_business_popular_posts - 30
         * @hooked rara_business_comment       - 35
        */
        do_action( 'rara_business_after_post_content' );
        ?>
        
	</div><!-- #primary -->

<?php
if ( 'full-width' != $sidebar_layout )
get_sidebar();
get_footer();
