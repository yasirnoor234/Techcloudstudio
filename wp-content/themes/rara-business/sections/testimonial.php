<?php
/**
 * Testimonial Section
 * 
 * @package Rara_Business
*/

if( is_active_sidebar( 'testimonial' ) ) { ?>
    <section id="testimonial-section" class="our-testimonial wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">
    	<div class="container">
    		<div class="grid">
    			<?php dynamic_sidebar( 'testimonial' ); ?>
    		</div>
    	</div>
    </section>
<?php
}