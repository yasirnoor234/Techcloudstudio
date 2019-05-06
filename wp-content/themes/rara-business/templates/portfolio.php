<?php
/**
 * Template Name: Portfolio
 *
 * @package Rara_Business
 */
get_header(); 

    $args = array(
        'post_type'      => 'rara-portfolio',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    );

    $qry = new WP_Query( $args );

    if( $qry->have_posts() ){ ?>
        <div class="portfolio-holder">
            <?php 
                rara_business_get_portfolio_buttons( '-1' );
                rara_business_get_portfolios( '-1' );
            ?>
        </div>
    <?php }

get_footer();
