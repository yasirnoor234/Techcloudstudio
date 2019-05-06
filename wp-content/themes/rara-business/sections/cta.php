<?php
/**
 * Call To Action Section
 * 
 * @package Rara_Business
*/

if( is_active_sidebar( 'cta' ) ) { ?>
    <section id="cta-section" class="cta wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">
    	<div class="container">
    		<?php dynamic_sidebar( 'cta' ); ?>
    	</div>
    </section>
<?php
}