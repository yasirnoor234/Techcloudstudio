<?php
/**
 * Template part for displaying portfolio taxonomy
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Rara_Business
 */

$terms = get_the_terms( get_the_ID(), 'rara_portfolio_categories' );
$s = '';
$n = '';
$i = 0;
if( $terms ){
    foreach( $terms as $t ){
        $i++;
        $s .= $t->slug;
        $n .= '#'.$t->name;
        if( count( $terms ) > $i ){
            $s .= ' ';
            $n .= ' ';
        }
    }
}                    
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="element-item">
        <div class="img-holder">
            <a href="<?php the_permalink(); ?>">
                <?php 
                    if( has_post_thumbnail() ){
                        the_post_thumbnail( 'rara-business-portfolio' );
                    }else{
                        echo '<img src="'. esc_url( get_template_directory_uri().'/images/rara-business-portfolio.jpg' ) .'" alt="'. esc_attr( get_the_title() ).'">';
                    }
                ?>                        
            </a>
            <div class="text-holder">
                <div class="text">
					<?php 
                        the_title( '<h3 class="title">', '</h3>' );
                        if( $n ) echo '<p>'. esc_html( $n ) .'</p>'; 
                    ?>                                
				</div>
            </div>                        
        </div>                  
    </div>
</article><!-- #post-<?php the_ID(); ?> -->
