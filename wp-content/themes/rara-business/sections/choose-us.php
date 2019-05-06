<?php
/**
 * Feature Section
 * 
 * @package Rara_Business
*/

if( is_active_sidebar( 'choose-us' ) ) { ?>
    <section id="choose-us" class="our-features wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">
    	<div class="container">
    		<div class="features-content">
    			<div class="grid">
    				<?php dynamic_sidebar( 'choose-us' ); ?>
    			</div>
    		</div>
    	</div>
    </section>
<?php
}