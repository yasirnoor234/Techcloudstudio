<?php
/**
 * Team Section
 * 
 * @package Rara_Business
*/

if( is_active_sidebar( 'team' ) ) { ?>
    <section id="team-section" class="our-team wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">
    	<div class="container">
    		<div class="grid">
    			<?php dynamic_sidebar( 'team' ); ?>
    		</div>
    	</div>
    </section>
<?php
}