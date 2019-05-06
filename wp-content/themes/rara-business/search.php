<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Rara_Business
 */

get_header(); ?>

	<section id="primary" class="content-area">
		
        <?php
        /**
         * Search Header
         * 
         * @hooked rara_business_breadcrumb  - 15
         * @hooked rara_business_page_header - 20
        */
        do_action( 'rara_business_before_posts_content' );
        ?>
        
        <main id="main" class="site-main">

		<?php
		if ( have_posts() ) : 
        
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-search.php and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_format() );

			endwhile;

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
        
        <?php
        /**
         * @hooked rara_business_pagination
        */
        do_action( 'rara_business_after_posts_content' );
        ?>
        
	</section><!-- #primary -->

<?php
get_sidebar();
get_footer();
