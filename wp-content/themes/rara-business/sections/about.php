<?php
/**
 * About Section
 * 
 * @package Rara_Business
*/

if( is_active_sidebar( 'about' ) ) { ?>
    <section id="about-section" class="featured-page wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">
    	<div class="container">
    		<?php dynamic_sidebar( 'about' ); ?>
    	</div>
    </section>
<?php
}