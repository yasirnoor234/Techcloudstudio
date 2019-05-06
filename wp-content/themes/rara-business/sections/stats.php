<?php
/**
 * Stats Section
 * 
 * @package Rara_Business
*/

if( is_active_sidebar( 'stats' ) ) { ?>
    <section id="stats-section" class="our-stats wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">
    	<div class="container">
    		<div class="grid">
    			<?php dynamic_sidebar( 'stats' ); ?>
    		</div>
    	</div>
    </section>
<?php
}