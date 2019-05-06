<?php
   /**
    * The template for displaying for Portfolio taxonomy
    *
    * * @package Rara_Business
    */
    get_header(); 

        /**
         * Search Header
         * 
         * @hooked rara_business_breadcrumb  - 15
         * @hooked rara_business_page_header - 20
         */
        do_action( 'rara_business_before_posts_content' ); ?>
    
        <div class="portfolio-holder">

            <?php if( have_posts() ) : ?>

                <div class="filter-grid">
                    <?php
                        /* Start the Loop */
                        while ( have_posts() ) : the_post();
                            /*
                            * Include the Post-Format-specific template for the content.
                            * If you want to override this in a child theme, then include a file
                            * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                            */
                            get_template_part( 'template-parts/taxonomy', 'portfolio' );
                                
                        endwhile;
                                            
                        /**
                         * @hooked rara_business_pagination
                         */
                        do_action( 'rara_business_after_posts_content' );
                    ?>
                </div>

            <?php else: 
                                                
                get_template_part( 'template-parts/content', 'none' );

                endif;
                
            ?>

        </div>

    <?php get_footer();
