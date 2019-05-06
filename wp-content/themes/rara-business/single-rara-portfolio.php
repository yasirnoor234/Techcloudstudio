<?php
/**
 * The template for displaying all portfolio single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Rara_Business
 */

get_header(); ?>

	<div id="primary" class="content-area">
    	<main id="main" class="site-main">

    	<?php
    	while ( have_posts() ) : the_post();

    		get_template_part( 'template-parts/content', 'page' );

    	endwhile; // End of the loop.
    	?>

    	</main><!-- #main -->
        
        <?php
        /**
         * @hooked rara_business_author        - 10 
         * @hooked rara_business_pagination    - 20
        */
        do_action( 'rara_business_after_protfolio_post_content' );
        ?>
        
	</div><!-- #primary -->

<?php

get_footer();
