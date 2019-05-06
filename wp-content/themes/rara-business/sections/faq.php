<?php
/**
 * FAQ's Section
 * 
 * @package Rara_Business
*/

if( is_active_sidebar( 'faq' ) ){ ?>
    <section id="faq-section" class="faq-section wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">
    	<div class="container">
    		<?php dynamic_sidebar( 'faq' ); ?>
    	</div>
    </section>
<?php
}