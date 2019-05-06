<?php
/**
 * Services Section
 * 
 * @package Rara_Business
*/

if( is_active_sidebar( 'services' ) ){ ?>
    <section id="services-section" class="our-services wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s" itemscope itemtype="http://schema.org/Service">
    	<div class="container">
    		<div class="grid">
    			<?php dynamic_sidebar( 'services' ); ?>
    		</div>
    	</div>
    </section>
<?php
}