<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Rara_Business
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php 
    /**
     * @hooked rara_business_post_thumbnail - 15
     * @hooked rara_business_entry_header   - 20
     * @hooked rara_business_entry_content  - 25
     * @hooked rara_business_entry_footer   - 30
    */
    do_action( 'rara_business_post_entry_content' );
    ?>
</article><!-- #post-<?php the_ID(); ?> -->
