<?php
/**
 * Clients Section
 * 
 * @package Rara_Business
*/

if( is_active_sidebar( 'client' ) ){ ?>
    <section id="client-section" class="our-clients">
    	<div class="container">
    		<?php dynamic_sidebar( 'client' ); ?>
    	</div>
    </section>
<?php
}