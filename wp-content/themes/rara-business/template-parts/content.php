<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Rara_Business
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="https://schema.org/Blog">
	
	<?php 
    /**
     * @hooked rara_business_entry_header   - 15
     * @hooked rara_business_post_thumbnail - 20
     * @hooked rara_business_entry_content  - 25
     * @hooked rara_business_entry_footer   - 30
    */
    do_action( 'rara_business_posts_entry_content' );
    ?>
	
</article><!-- #post-<?php the_ID(); ?> -->
