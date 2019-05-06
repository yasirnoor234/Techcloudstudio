<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Rara_Business
 */

if ( ! function_exists( 'rara_business_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time.
 */
function rara_business_posted_on() {
    $default_options   = rara_business_default_theme_options(); // Get default theme options
    $post_updated_date = get_theme_mod( 'ed_post_update_date', $default_options['ed_post_update_date'] );
    $hide_date         = get_theme_mod( 'ed_post_date_meta', $default_options['ed_post_date_meta'] );
    $hide_author       = get_theme_mod( 'ed_post_author_meta', $default_options['ed_post_author_meta'] );
    $on                = '';

    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
        if( $post_updated_date ){
            $time_string = '<time class="entry-date published updated" datetime="%3$s" itemprop="dateModified">%4$s</time></time><time class="updated" datetime="%1$s" itemprop="datePublished">%2$s</time>';
            $on = __( 'Updated on ', 'rara-business' );         
        }else{
            $time_string = '<time class="entry-date published" datetime="%1$s" itemprop="datePublished">%2$s</time><time class="updated" datetime="%3$s" itemprop="dateModified">%4$s</time>';  
        }        
    }else{
       $time_string = '<time class="entry-date published updated" datetime="%1$s" itemprop="datePublished">%2$s</time><time class="updated" datetime="%3$s" itemprop="dateModified">%4$s</time>';   
    }

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

    if ( ! $hide_author && ! $hide_date ) {
        $separator = '<span class="separator">/</span>'; 
    } else {
        $separator = '';
    }

    $posted_on = sprintf( '%1$s %2$s', esc_html( $on ), '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
    );

	echo '<span class="posted-on">'. $posted_on .'</span>'. $separator; // WPCS: XSS OK.

}
endif;

if( ! function_exists( 'rara_business_posted_by' ) ) :
    /**
     * Prints HTML with meta information for the current author
     */
    function rara_business_posted_by(){      
        $default_options = rara_business_default_theme_options(); // Get default theme options
        $hide_date       = get_theme_mod( 'ed_post_date_meta', $default_options['ed_post_date_meta'] );
        $hide_author     = get_theme_mod( 'ed_post_author_meta', $default_options['ed_post_author_meta'] );

        $byline = '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" itemprop="url"><span itemprop="name">' . esc_html( get_the_author() ) . '</span></a></span>';

        if ( ! $hide_author && ! $hide_date ) {
            $separator = '<span class="separator">/</span>'; 
        } else {
            $separator = '';
        }

        echo '<span class="byline" itemprop="author" itemscope itemtype="https://schema.org/Person"> ' . $byline . '</span>';
    }
endif;

if( ! function_exists( 'rara_business_categories' ) ) :
/**
 * Categories
*/
function rara_business_categories(){
    // Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		$categories_list = get_the_category_list( esc_html__( ' ', 'rara-business' ) );
		if ( $categories_list ) {
			echo '<div class="categories">' . $categories_list . '</div>';
		}
	}
}
endif;

if( ! function_exists( 'rara_business_tags' ) ) :
/**
 * Tags
*/
function rara_business_tags(){
    // Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		$tags_list = get_the_tag_list( '', esc_html_x( ' ', 'list item separator', 'rara-business' ) );
		if ( $tags_list ) {
			echo '<div class="tag">' . $tags_list . '</span>';
		}
	}
}
endif;

if( ! function_exists( 'rara_business_theme_comment' ) ) :
/**
 * Callback function for Comment List *
 * 
 * @link https://codex.wordpress.org/Function_Reference/wp_list_comments 
 */
function rara_business_theme_comment( $comment, $args, $depth ){
	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>
	<<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
	
    <?php if ( 'div' != $args['style'] ) : ?>
    <div id="div-comment-<?php comment_ID() ?>" class="comment-body" itemscope itemtype="http://schema.org/UserComments">
	<?php endif; ?>
    	
        <footer class="comment-meta">
            <div class="comment-author vcard">
        	   <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
        	</div><!-- .comment-author vcard -->
        </footer>
        
        <div class="text-holder">
        	<div class="top">
                <div class="left">
                    <?php if ( $comment->comment_approved == '0' ) : ?>
                		<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'rara-business' ); ?></em>
                		<br />
                	<?php endif; 
                        /* translators: %s: author link  */
                        printf( __( '<b class="fn" itemprop="creator" itemscope itemtype="http://schema.org/Person">%s</b> <span class="says">says:</span>', 'rara-business' ), get_comment_author_link() ); 
                    ?>
                	<div class="comment-metadata commentmetadata">
                        <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>">
                    		<time itemprop="commentTime" datetime="<?php echo esc_attr( get_gmt_from_date( get_comment_date() . get_comment_time(), 'Y-m-d H:i:s' ) ); ?>">
                                <?php
                                /* translators: 1: comment date, 2: comment time  */
                                printf( esc_html__( '%1$s at %2$s', 'rara-business' ), get_comment_date(),  get_comment_time() ); ?>
                            </time>
                        </a>
                	</div>
                </div>
                <div class="reply">
                    <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
            	</div>
            </div>            
            <div class="comment-content" itemprop="commentText"><?php comment_text(); ?></div>        
        </div><!-- .text-holder -->
        
	<?php if ( 'div' != $args['style'] ) : ?>
    </div><!-- .comment-body -->
	<?php endif; ?>
    
<?php
}
endif;

if( ! function_exists( 'rara_business_social_links' ) ) :
/**
 * Prints social links in header
*/
function rara_business_social_links( $ed_social = false , $social_links = array() ){
    if( $ed_social && $social_links ){
        echo '<ul class="social-networks">';
    	foreach( $social_links as $link ){
            if( $link['link'] && $link['font'] ) echo '<li><a href="' . esc_url( $link['link'] ) . '" target="_blank" rel="nofollow"><i class="' . esc_attr( $link['font'] ) . '"></i></a></li>';    	   
    	}
	   echo '</ul>';    
    }
}
endif;

if( ! function_exists( 'rara_business_header_phone' ) ) :
/**
 * Phone
*/
function rara_business_header_phone( $phone ){ ?>
    <div class="phone">
		<i class="fa fa-mobile-phone"></i>
		<a href="<?php echo esc_url( 'tel:' . preg_replace( '/\D/', '', $phone ) ); ?>" class="tel-link"><?php echo esc_html( $phone ); ?></a>
	</div>
    <?php
}
endif;

if( ! function_exists( 'rara_business_header_address' ) ) :
/**
 * Address
*/
function rara_business_header_address( $address ){ ?>
    <div class="address" itemscope itemtype="http://schema.org/PostalAddress">
		<i class="fa fa-map-marker"></i>
		<address><?php echo esc_html( $address ); ?></address>
	</div>
    <?php
}
endif;

if( ! function_exists( 'rara_business_header_email' ) ) :
/**
 * Email
*/
function rara_business_header_email( $email ){ ?>
    <div class="email">
		<i class="fa fa-envelope-o"></i>
		<a href="<?php echo esc_url( 'mailto:' . sanitize_email( $email ) ); ?>" class="email-link"><?php echo esc_html( $email ); ?></a>
	</div>
    <?php
}
endif;

if( ! function_exists( 'rara_business_custom_link' ) ) :
/**
 * Additional Link in menu
*/
function rara_business_custom_link( $icon, $link, $label ){
    if( ! empty( $icon ) ){
        echo '<a href="' . esc_url( $link ) . '" class="btn-buy custom_label"><i class="'. esc_attr( $icon ) .'"></i>' . esc_html( $label ) . '</a>';
    } else {
        echo '<a href="' . esc_url( $link ) . '" class="btn-buy">' . esc_html( $label ) . '</a>';
    }
}
endif;

if( ! function_exists( 'rara_business_primary_menu_fallback' ) ) :
/**
 * Primary Menu Fallback
*/
function rara_business_primary_menu_fallback(){
    if( current_user_can( 'manage_options' ) ){
        echo '<ul id="primary-menu" class="menu">';
        echo '<li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Click here to add a menu', 'rara-business' ) . '</a></li>';
        echo '</ul>';
    }
}
endif;

if( ! function_exists( 'rara_business_get_home_sections' ) ) :
/**
 * Returns Home Sections 
*/
function rara_business_get_home_sections(){
    $sections = array( 
        'services'    => array( 'sidebar' => 'services' ), 
        'about'       => array( 'sidebar' => 'about' ), 
        'choose-us'   => array( 'sidebar' => 'choose-us' ), 
        'team'        => array( 'sidebar' => 'team' ), 
        'testimonial' => array( 'sidebar' => 'testimonial' ), 
        'stats'       => array( 'sidebar' => 'stats' ), 
        'portfolio'   => array( 'section' => 'portfolio' ), 
        'blog'        => array( 'section' => 'blog' ), 
        'cta'         => array( 'sidebar' => 'cta' ), 
        'faq'         => array( 'sidebar' => 'faq' ), 
        'client'      => array( 'sidebar' => 'client' ) 
    );

    $enabled_section = array();
    
    foreach( $sections as $k => $v ){
        if( array_key_exists( 'sidebar', $v ) ){
            if( is_active_sidebar( $v['sidebar'] ) ) array_push( $enabled_section, $v['sidebar'] );
        }else{
            if( get_theme_mod( 'ed_' . $v['section'] . '_section', true ) ) array_push( $enabled_section, $v['section'] );
        }
    }  
    
    return apply_filters( 'rara_business_home_sections', $enabled_section );
}
endif;

if( ! function_exists( 'rara_business_get_portfolio_buttons' ) ) :
/**
 * Query for Portfolio Buttons
*/
function rara_business_get_portfolio_buttons( $no_of_portfolio, $home = false ){
    if( taxonomy_exists( 'rara_portfolio_categories' ) ){
        if( $home ){
            $s = '';
            $i = 0;
            $portfolio_posts = get_posts( array( 'post_type' => 'rara-portfolio', 'post_status' => 'publish', 'posts_per_page' => $no_of_portfolio ) );
            foreach( $portfolio_posts as $portfolio ){
                $terms = get_the_terms( $portfolio->ID, 'rara_portfolio_categories' );
                if( $terms ){
                    foreach( $terms as $term ){
                        $i++;
                        $s .= $term->term_id;
                        $s .= ', ';    
                    }
                }
            }
            $term_ids = explode( ', ', $s );
            $term_ids = array_diff( array_unique( $term_ids ), array('') );
            wp_reset_postdata();//Reseting get_posts       
        }
        
        $args = array(
            'taxonomy'      => 'rara_portfolio_categories',
            'orderby'       => 'name', 
            'order'         => 'ASC',
        );                
        $terms = get_terms( $args );
        if( $terms ){
        ?>
        <div class="button-group filter-button-group">        
            <button data-filter="*" class="button is-checked"><?php echo esc_html_e( 'All', 'rara-business' ); ?></button><!-- This is HACK for reducing space between inline block elements.
            --><?php
                foreach( $terms as $t ){
                    if( $home ){
                        if( in_array( $t->term_id, $term_ids ) )
                        echo '<button class="button" data-filter=".' . esc_attr( $t->slug ) .  '">' . esc_html( $t->name ) . '</button>';
                    }else{
                        echo '<button class="button" data-filter=".' . esc_attr( $t->slug ) .  '">' . esc_html( $t->name ) . '</button>';    
                    }                    
                } 
            ?>
        </div>            
        <?php
        }
    }
}
endif;

if( ! function_exists( 'rara_business_get_portfolios' ) ) :
/**
 * Query for portfolios 
*/
function rara_business_get_portfolios( $no_of_portfolio = -1 ){
    $portfolio_qry = new WP_Query( array( 'post_type' => 'rara-portfolio', 'post_status' => 'publish', 'posts_per_page' => $no_of_portfolio ) );
    if( taxonomy_exists( 'rara_portfolio_categories' ) && $portfolio_qry->have_posts() ){ ?>
                        
        <div class="filter-grid">
    		<?php
            while( $portfolio_qry->have_posts() ){
                $portfolio_qry->the_post();
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
                <div class="element-item <?php echo esc_attr( $s );?>">
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
    		    <?php
            }
            ?>
    	</div><!-- .filter-grid -->
        <?php
        wp_reset_postdata(); 
    } 
}
endif;

/**
 * Query WooCommerce activation
 */
function rara_business_is_woocommerce_activated() {
	return class_exists( 'woocommerce' ) ? true : false;
}

/**
 * Query Rara theme companion activation
 */
function rara_business_is_rara_theme_companion_activated() {
    return class_exists( 'Raratheme_Companion_Public' ) ? true : false;
}

if( ! function_exists( 'rara_business_get_svg' ) ) :
    /**
     * Return SVG markup.
     *
     * @param array $args {
     *     Parameters needed to display an SVG.
     *
     *     @type string $icon  Required SVG icon filename.
     *     @type string $title Optional SVG title.
     *     @type string $desc  Optional SVG description.
     * }
     * @return string SVG markup.
     */
    function rara_business_get_svg( $args = array() ) {
        // Make sure $args are an array.
        if ( empty( $args ) ) {
            return __( 'Please define default parameters in the form of an array.', 'rara-business' );
        }

        // Define an icon.
        if ( false === array_key_exists( 'icon', $args ) ) {
            return __( 'Please define an SVG icon filename.', 'rara-business' );
        }

        // Set defaults.
        $defaults = array(
            'icon'        => '',
            'title'       => '',
            'desc'        => '',
            'fallback'    => false,
        );

        // Parse args.
        $args = wp_parse_args( $args, $defaults );

        // Set aria hidden.
        $aria_hidden = ' aria-hidden="true"';

        // Set ARIA.
        $aria_labelledby = '';

        /*
         * Restaurant and Cafe Pro doesn't use the SVG title or description attributes; non-decorative icons are described with .screen-reader-text.
         *
         * However, child themes can use the title and description to add information to non-decorative SVG icons to improve accessibility.
         *
         * Example 1 with title: <?php echo rara_business_get_svg( array( 'icon' => 'arrow-right', 'title' => __( 'This is the title', 'textdomain' ) ) ); ?>
         *
         * Example 2 with title and description: <?php echo rara_business_get_svg( array( 'icon' => 'arrow-right', 'title' => __( 'This is the title', 'textdomain' ), 'desc' => __( 'This is the description', 'textdomain' ) ) ); ?>
         *
         * See https://www.paciellogroup.com/blog/2013/12/using-aria-enhance-svg-accessibility/.
         */
        if ( $args['title'] ) {
            $aria_hidden     = '';
            $unique_id       = uniqid();
            $aria_labelledby = ' aria-labelledby="title-' . $unique_id . '"';

            if ( $args['desc'] ) {
                $aria_labelledby = ' aria-labelledby="title-' . $unique_id . ' desc-' . $unique_id . '"';
            }
        }

        // Begin SVG markup.
        $svg = '<svg class="icon icon-' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img">';

        // Display the title.
        if ( $args['title'] ) {
            $svg .= '<title id="title-' . $unique_id . '">' . esc_html( $args['title'] ) . '</title>';

            // Display the desc only if the title is already set.
            if ( $args['desc'] ) {
                $svg .= '<desc id="desc-' . $unique_id . '">' . esc_html( $args['desc'] ) . '</desc>';
            }
        }

        /*
         * Display the icon.
         *
         * The whitespace around `<use>` is intentional - it is a work around to a keyboard navigation bug in Safari 10.
         *
         * See https://core.trac.wordpress.org/ticket/38387.
         */
        $svg .= ' <use href="#icon-' . esc_attr( $args['icon'] ) . '" xlink:href="#icon-' . esc_attr( $args['icon'] ) . '"></use> ';

        // Add some markup to use as a fallback for browsers that do not support SVGs.
        if ( $args['fallback'] ) {
            $svg .= '<span class="svg-fallback icon-' . esc_attr( $args['icon'] ) . '"></span>';
        }

        $svg .= '</svg>';

        return $svg;
    }
endif;

if( ! function_exists( 'rara_business_sidebar_layout' ) ) :
    /**
     * Return sidebar layouts for pages/posts
     */
    function rara_business_sidebar_layout(){
        global $post;
        $return = false;
        $page_layout = get_theme_mod( 'page_sidebar_layout', 'right-sidebar' ); //Default Layout Style for Pages
        $post_layout = get_theme_mod( 'post_sidebar_layout', 'right-sidebar' ); //Default Layout Style for Posts
        
        if( is_singular( array( 'page', 'post' ) ) ){         
            if( get_post_meta( $post->ID, 'sidebar_layout', true ) ){
                $sidebar_layout = get_post_meta( $post->ID, 'sidebar_layout', true );
            }else{
                $sidebar_layout = 'default-sidebar';
            }
            
            if( is_page() ){
                if( is_page_template( 'templates/portfolio.php' ) ){
                    $return = '';
                }elseif( is_active_sidebar( 'sidebar' ) ){
                    if( $sidebar_layout == 'no-sidebar' ){
                        $return = 'full-width';
                    }elseif( ( $sidebar_layout == 'default-sidebar' && $page_layout == 'right-sidebar' ) || ( $sidebar_layout == 'right-sidebar' ) ){
                        $return = 'rightsidebar';
                    }elseif( ( $sidebar_layout == 'default-sidebar' && $page_layout == 'left-sidebar' ) || ( $sidebar_layout == 'left-sidebar' ) ){
                        $return = 'leftsidebar';
                    }elseif( $sidebar_layout == 'default-sidebar' && $page_layout == 'no-sidebar' ){
                        $return = 'full-width';
                    }
                }else{
                    $return = 'full-width';
                }
            }elseif( is_single() ){
                if( is_active_sidebar( 'sidebar' ) ){
                    if( $sidebar_layout == 'no-sidebar' ){
                        $return = 'full-width';
                    }elseif( ( $sidebar_layout == 'default-sidebar' && $post_layout == 'right-sidebar' ) || ( $sidebar_layout == 'right-sidebar' ) ){
                        $return = 'rightsidebar';
                    }elseif( ( $sidebar_layout == 'default-sidebar' && $post_layout == 'left-sidebar' ) || ( $sidebar_layout == 'left-sidebar' ) ){
                        $return = 'leftsidebar';
                    }elseif( $sidebar_layout == 'default-sidebar' && $post_layout == 'no-sidebar' ){
                        $return = 'full-width';
                    }
                }else{
                    $return = 'full-width';
                }
            }
        }elseif( is_tax( 'rara_portfolio_categories' ) ){
            $return = 'page-template-portfolio';
        }elseif( is_singular( 'rara-portfolio' ) ){
            $return = 'full-width';
        }elseif( rara_business_is_woocommerce_activated() && is_post_type_archive( 'product' ) ){
            if( is_active_sidebar( 'shop-sidebar' ) ){            
                $return = 'rightsidebar';             
            }else{
                $return = 'full-width';
            } 
        }else{
            if( is_active_sidebar( 'sidebar' ) ){            
                $return = 'rightsidebar';             
            }else{
                $return = 'full-width';
            } 
        }
        
        return $return; 
    }
endif;

if( ! function_exists( 'rara_business_escape_text_tags' ) ) :
/**
 * Remove new line tags from string
 *
 * @param $text
 *
 * @return string
 */
function rara_business_escape_text_tags( $text ) {
    return (string) str_replace( array( "\r", "\n" ), '', strip_tags( $text ) );
}
endif;

if( ! function_exists( 'rara_business_fonts_url' ) ) :
    /**
     * Register custom fonts.
     */
    function rara_business_fonts_url() {
        $fonts_url = '';

        /* Translators: If there are characters in your language that are not
        * supported by respective fonts, translate this to 'off'. Do not translate
        * into your own language.
        */

        $lato_font       = _x( 'on', 'Lato font: on or off', 'rara-business' );
        $montserrat_font = _x( 'on', 'Montserrat font: on or off', 'rara-business' );

        if ( 'off' !== $lato_font || 'off' !== $montserrat_font ) {
            $font_families = array();

            if ( 'off' !== $lato_font ) {
                $font_families[] = 'Lato:100,100i,300,300i,400,400i,700,700i,900,900i';

            }

            if ( 'off' !== $montserrat_font ) {
                $font_families[] = 'Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i';

            }

            $query_args = array(
                'family' => urlencode( implode( '|', $font_families ) ),
                'subset' => urlencode( 'latin,latin-ext' ),
            );

            $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
        }

        return esc_url_raw( $fonts_url );
    }
endif;