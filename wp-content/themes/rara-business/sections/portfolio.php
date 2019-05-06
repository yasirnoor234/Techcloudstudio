<?php
/**
 * Portfolio Section
 * 
 * @package Rara_Business
*/

/** Load default theme options */
$default_options   =  rara_business_default_theme_options();

$show_portfolio  = get_theme_mod( 'ed_portfolio_section', $default_options['ed_portfolio_section'] );
$title           = get_theme_mod( 'portfolio_title', $default_options['portfolio_title'] );
$subtitle        = get_theme_mod( 'portfolio_description', $default_options['portfolio_description'] );
$no_of_portfolio = get_theme_mod( 'portfolio_no_of_posts', $default_options['portfolio_no_of_posts'] );

$args = array(
    'post_type'      => 'rara-portfolio',
    'post_status'    => 'publish',
    'posts_per_page' => 10,
);

$qry = new WP_Query( $args );

if( ( $title || $subtitle || ( $qry->have_posts() && $no_of_portfolio > 0 ) ) && $show_portfolio ){ ?>
    <section id="portfolio-section" class="portfolio wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">
    	<?php if( $title || $subtitle ){ ?>
        <div class="container">
    		<section class="widget widget_text">
    			<?php 
                    if( $title ) echo '<h2 class="widget-title">' . esc_html( $title )  . '</h2>';
                    if( $subtitle ) echo '<div class="textwidget">' . wpautop( wp_kses_post( $subtitle ) ) . '</div>';
                ?>
    		</section>
    	</div>
    	<?php } ?>
        
        <?php if( $qry->have_posts() && $no_of_portfolio > 0 ){ ?>
        <div class="portfolio-holder">
    		<?php 
                rara_business_get_portfolio_buttons( $no_of_portfolio, true );
                rara_business_get_portfolios( $no_of_portfolio );
            ?>
    	</div>
        <?php } ?>
    </section>
<?php
}