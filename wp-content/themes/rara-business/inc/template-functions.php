<?php
/**
 * Template Functions which enhance the theme by hooking into WordPress
 *
 * @package Rara_Business
 */

if( ! function_exists( 'rara_business_doctype' ) ) :
/**
 * Doctype Declaration
*/
function rara_business_doctype(){
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <?php
}
endif;
add_action( 'rara_business_doctype', 'rara_business_doctype' );

if( ! function_exists( 'rara_business_head' ) ) :
/**
 * Before wp_head 
*/
function rara_business_head(){
    ?>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php
}
endif;
add_action( 'rara_business_before_wp_head', 'rara_business_head' );

if( ! function_exists( 'rara_business_page_start' ) ) :
/**
 * Page Start
*/
function rara_business_page_start(){
    ?>
    <div id="page" class="site">
    <?php
}
endif;
add_action( 'rara_business_before_header', 'rara_business_page_start', 20 );

if( ! function_exists( 'rara_business_header' ) ) :
/**
 * Header Start
*/
function rara_business_header(){ 
    $default_options = rara_business_default_theme_options(); // Get default theme options

    $ed_header_contact = get_theme_mod( 'ed_header_contact_details', $default_options['ed_header_contact_details'] );
    $phone             = get_theme_mod( 'header_phone', $default_options['header_phone'] );
    $address           = get_theme_mod( 'header_address', $default_options['header_address'] );
    $email             = get_theme_mod( 'header_email', $default_options['header_email'] );
    $icon              = get_theme_mod( 'custom_link_icon', $default_options['custom_link_icon'] );
    $label             = get_theme_mod( 'custom_link_label', $default_options['custom_link_label'] );
    $ed_header_social  = get_theme_mod( 'ed_header_social_links', $default_options['ed_header_social_links'] );
    $social_links      = get_theme_mod( 'header_social_links', $default_options['header_social_links'] );
    $link              = get_theme_mod( 'custom_link', $default_options['custom_link'] );

    if( is_customize_preview() ){
        if( $ed_header_contact ){
            if( empty( $phone ) ) $phone = __( '1-800-567-0123', 'rara-business' );
            if( empty( $address ) ) $address = __( 'Street, New York City', 'rara-business' );
            if( empty( $email ) ) $email = __( 'mail@domain.com', 'rara-business' );
        }

        if( $ed_header_social ){
            $preview_default_social_links = array(
                array(
                    'font'          => 'fa fa-facebook',
                    'link'          => 'https://www.facebook.com/',                        
                ),
                array(
                    'font'          => 'fa fa-twitter',
                    'link'          => 'https://twitter.com/',
                ),
                array(
                    'font'          => 'fa fa-youtube-play',
                    'link'          => 'https://www.youtube.com/',
                ),
                array(
                    'font'          => 'fa fa-instagram',
                    'link'          => 'https://www.instagram.com/',
                ),
                array(
                    'font'          => 'fa fa-google-plus-circle',
                    'link'          => 'https://plus.google.com',
                ),
                array(
                    'font'          => 'fa fa-odnoklassniki',
                    'link'          => 'https://ok.ru/',
                ),
                array(
                    'font'          => 'fa fa-vk',
                    'link'          => 'https://vk.com/',
                ),
                array(
                    'font'          => 'fa fa-xing',
                    'link'          => 'https://www.xing.com/',
                )
            );

            if( empty( $social_links ) ){
                $social_links = $preview_default_social_links;
            }
        }
    } 

    ?>
    
    <header id="masthead" class="site-header" itemscope itemtype="http://schema.org/WPHeader">
        <?php 
            if( ! ( ( $phone || $address || $email ) && $ed_header_contact ) &&  ! ( $ed_header_social && ! empty( $social_links ) ) ){ 
                $class = ' hide-header-top';
            } else {
                $class ='';
            }
        ?>
		<div class="header-t<?php echo esc_attr( $class ); ?>">
			<div class="container">
				<?php 
                if( ( $phone || $address || $email ) && $ed_header_contact ){ ?>
                    <div class="contact-info">
    					<?php 
                            if( $phone ) rara_business_header_phone( $phone );
                            if( $address ) rara_business_header_address( $address );
                            if( $email ) rara_business_header_email( $email ); 
                        ?>
    				</div>
                    <?php 
                } 
                
                rara_business_social_links( $ed_header_social, $social_links );
                ?>
                
				<div id="primary-toggle-button">
					<span></span>
					<span></span>
					<span></span>
				</div>
			</div>
            
			<div class="responsive-menu-holder">
				<div class= "social-networks-holder">
					<div class="container">
						<?php rara_business_social_links( $ed_header_social, $social_links ); ?>
					</div>
				</div>
				<div class="container">
					<nav class="main-navigation">
            			<?php
            				wp_nav_menu( array(
            					'theme_location' => 'primary',
            					'menu_id'        => 'primary-menu',
                                'fallback_cb'    => 'rara_business_primary_menu_fallback',
            				) );
            			?>
            		</nav><!-- #site-navigation -->
					
                    <?php 
                    if( $link && $label ) rara_business_custom_link( $icon, $link, $label );
                    
                    if( $phone || $address || $email ){ ?>
                        <div class="contact-info">
    						<?php 
                                if( $phone ) rara_business_header_phone( $phone );
                                if( $address ) rara_business_header_address( $address );
                                if( $email ) rara_business_header_email( $email ); 
                            ?>
    					</div>
                        <?php 
                    } 
                    ?>
				</div>
			</div>
		</div>

		<div class="main-header">
			<div class="container">
                <?php 
                    $display_header_text = get_theme_mod( 'header_text', 1 );
                    $site_title          = get_bloginfo( 'name', 'display' );
                    $description         = get_bloginfo( 'description', 'display' );

                    if( ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) && $display_header_text && ( ! empty( $site_title ) || ! empty(  $description  ) ) ){
                       $branding_class = 'logo-with-site-identity';                                                                                                                          
                    } else {
                        $branding_class = '';
                    }
                ?>
                <div class="site-branding <?php echo esc_attr( $branding_class ); ?>" itemscope itemtype="http://schema.org/Organization">
                    <?php 
                        if( function_exists( 'has_custom_logo' ) && has_custom_logo() ){
                            the_custom_logo();
                        } 

                        echo '<div class="text-logo">';
                            if( is_front_page() ){ ?>
                                <h1 class="site-title" itemprop="name"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" itemprop="url"><?php bloginfo( 'name' ); ?></a></h1>
                                <?php } else { ?>
                                <p class="site-title" itemprop="name"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" itemprop="url"><?php bloginfo( 'name' ); ?></a></p>
                            <?php
                            }

                            if ( $description || is_customize_preview() ){ ?>
                                <p class="site-description" itemprop="description"><?php echo esc_html( $description ); ?></p>
                            <?php
        
                            }
                        echo '</div><!-- .text-logo -->';
                    ?>
				</div>
				<div class="right">
					<?php if( $link && $label ) rara_business_custom_link( $icon, $link, $label ); ?>
					<nav id="site-navigation" class="main-navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
        			<?php
        				wp_nav_menu( array(
        					'theme_location' => 'primary',
        					'menu_id'        => 'primary-menu',
                            'fallback_cb'    => 'rara_business_primary_menu_fallback',
        				) );
        			?>
        		</nav><!-- #site-navigation -->
				</div>
			</div>
		</div>
	</header>
    <?php 
}
endif;
add_action( 'rara_business_header', 'rara_business_header', 20 );

if( ! function_exists( 'rara_business_banner' ) ) :
/**
 * Banner 
*/
function rara_business_banner(){
    $default_options = rara_business_default_theme_options(); // Get default theme options
    
    $banner_control      = get_theme_mod( 'ed_banner_section', $default_options['ed_banner_section'] );
    $title               = get_theme_mod( 'banner_title', $default_options['banner_title'] );
    $description         = get_theme_mod( 'banner_description', $default_options['banner_description'] );
    $link_one_label      = get_theme_mod( 'banner_link_one_label', $default_options['banner_link_one_label'] );
    $link_one_url        = get_theme_mod( 'banner_link_one_url', $default_options['banner_link_one_url'] );
    $link_two_label      = get_theme_mod( 'banner_link_two_label', $default_options['banner_link_two_label'] );
    $link_two_url        = get_theme_mod( 'banner_link_two_url', $default_options['banner_link_two_url'] );
    $custom_header_image = get_header_image_tag(); // get custom header image tag
    $class               = has_header_video() ? 'video-banner' : '';
    
    if( is_front_page() && ! is_home() && ( has_header_video() ||  ! empty( $custom_header_image ) ) && 'no_banner' != $banner_control ){ ?>
        <div id="banner-section" class="banner <?php echo esc_attr( $class ); ?>">
    		<?php the_custom_header_markup(); ?>
    		<div class="banner-text">
    			<div class="container">
    				<div class="text-holder">
                        <?php
                            if ( $title || $description ){
                                if ( $title ) echo '<h2 class="title wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">'. esc_html( $title ).'</h2>';
                                if ( $description ) echo '<p class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s">'. esc_html( $description ) .'</p>';
                            }

                            if ( $link_one_label || $link_two_label ) {
                                ?>
                                <div class="btn-holder wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s">
                                    <?php
                                        if ( $link_one_label ) echo  '<a href="'. esc_url( $link_one_url ) .'" class="btn-free-inquiry"><i class="fa fa-edit"></i>'. esc_html( $link_one_label ) .'</a>'; 
                                        if ( $link_two_label ) echo '<a href="'. esc_url( $link_two_url ) .'" class="btn-view-service">'. esc_html( $link_two_label ) .'</a>';
                                    ?>
                                </div>
                            <?php
                            }
                        ?>
    				</div>
    			</div>
    		</div>
    	</div>
    <?php 
    }
}
endif;
add_action( 'rara_business_after_header', 'rara_business_banner' );


if( ! function_exists( 'rara_business_content_start' ) ) :
/**
 * Content Start
*/
function rara_business_content_start(){ 
    $home_sections = rara_business_get_home_sections();

    if( !( is_front_page() && ! is_home() && $home_sections ) ){ ?>
	<div id="content" class="site-content">
        <div class="container">
        <?php 
        if( ! ( is_front_page() && ! is_home() ) && ! is_home() && ! is_search() && ! is_archive() ) rara_business_breadcrumb();
        if( ! is_search() && ! is_archive() && ! is_home() && ! ( is_single() && 'post' === get_post_type() ) ) rara_business_page_header();
        if( ! is_404() && ! is_page_template( 'templates/portfolio.php' ) && ! is_tax( 'rara_portfolio_categories' ) ) echo '<div class="content-grid">';
    }
}
endif;
add_action( 'rara_business_content', 'rara_business_content_start' );

if( ! function_exists( 'rara_business_breadcrumb' ) ) :
/**
 * Breadcrumbs
*/
function rara_business_breadcrumb(){ 
    global $post;

    $default_options    = rara_business_default_theme_options(); // Get default theme options
    $post_page          = get_option( 'page_for_posts' ); //The ID of the page that displays posts.
    $show_front         = get_option( 'show_on_front' ); //What to show on the front page    
    $home               = get_theme_mod( 'home_text', $default_options['home_text'] ); // text for the 'Home' link
    $breadcrumb_control = get_theme_mod( 'ed_breadcrumb', $default_options['ed_breadcrumb'] );
    $delimiter          = '>';
    $before             = '<span class="current" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">'; // tag before the current crumb
    $after              = '</span>'; // tag after the current crumb
    
    if( $breadcrumb_control ){
        $depth = 1;
        echo '<div class="breadcrumb-wrapper"><div id="crumbs" itemscope itemtype="http://schema.org/BreadcrumbList"><span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( home_url() ) . '" class="home_crumb"><span itemprop="name">' . esc_html( $home ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . esc_html( $delimiter ) . '</span></span>';
        
        if( is_home() ){
            $depth = 2;
            echo $before . '<a itemprop="item" href="'. esc_url( get_the_permalink() ) .'"><span itemprop="name">' . esc_html( single_post_title( '', false ) ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
            
        }elseif( is_category() ){
            $depth = 2;
            $thisCat = get_category( get_query_var( 'cat' ), false );

            if( $show_front === 'page' && $post_page ){ //If static blog post page is set
                $p = get_post( $post_page );
                echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_permalink( $post_page ) ) . '"><span itemprop="name">' . esc_html( $p->post_title ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . esc_html( $delimiter ) . '</span></span>';
                $depth ++;  
            }

            if ( $thisCat->parent != 0 ) {
                $parent_categories = get_category_parents( $thisCat->parent, false, ',' );
                $parent_categories = explode( ',', $parent_categories );

                foreach ( $parent_categories as $parent_term ) {
                    $parent_obj = get_term_by( 'name', $parent_term, 'category' );
                    if( is_object( $parent_obj ) ){
                        $term_url    = get_term_link( $parent_obj->term_id );
                        $term_name   = $parent_obj->name;
                        echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( $term_url ) . '"><span itemprop="name">' . esc_html( $term_name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . esc_html( $delimiter ) . '</span></span>';
                        $depth ++;
                    }
                }
            }
            echo $before . '<a itemprop="item" href="' . esc_url( get_term_link( $thisCat->term_id) ) . '"><span itemprop="name">' .  esc_html( single_cat_title( '', false ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $after;
        
        }elseif(rara_business_is_woocommerce_activated() && ( is_product_category() || is_product_tag() ) ){ //For Woocommerce archive page
            
            $depth = 2;
            $current_term = $GLOBALS['wp_query']->get_queried_object();
            if( is_product_category() ){
                $ancestors = get_ancestors( $current_term->term_id, 'product_cat' );
                $ancestors = array_reverse( $ancestors );
                foreach ( $ancestors as $ancestor ) {
                    $ancestor = get_term( $ancestor, 'product_cat' );    
                    if ( ! is_wp_error( $ancestor ) && $ancestor ) {
                        echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_term_link( $ancestor ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $ancestor->name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . esc_html( $delimiter ) . '</span></span>';
                        $depth ++;
                    }
                }
            }           
            echo $before . '<a itemprop="item" href="' . esc_url( get_term_link( $current_term->term_id ) ) . '"><span itemprop="name">' . esc_html( $current_term->name ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . $after;
            
        } elseif(rara_business_is_woocommerce_activated() && is_shop() ){ //Shop Archive page
            $depth = 2;
            if ( get_option( 'page_on_front' ) == wc_get_page_id( 'shop' ) ) {
                return;
            }
            $_name = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
            $shop_url = wc_get_page_id( 'shop' ) && wc_get_page_id( 'shop' ) > 0  ? get_the_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop' );
    
            if ( ! $_name ) {
                $product_post_type = get_post_type_object( 'product' );
                $_name = $product_post_type->labels->singular_name;
            }
            echo $before . '<a itemprop="item" href="' . esc_url( $shop_url ) . '"><span itemprop="name">' . esc_html( $_name ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
            
        }elseif( is_tag() ){
            $queried_object = get_queried_object();
            $depth = 2;

            echo $before . '<a itemprop="item" href="' . esc_url( get_term_link( $queried_object->term_id ) ) . '"><span itemprop="name">' . esc_html( single_tag_title( '', false ) ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
     
        }elseif( is_author() ){
            $depth = 2;
            global $author;

            $userdata = get_userdata( $author );
            echo $before . '<a itemprop="item" href="' . esc_url( get_author_posts_url( $author ) ) . '"><span itemprop="name">' . esc_html( $userdata->display_name ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
     
        }elseif( is_search() ){
            $depth = 2;
            $request_uri = $_SERVER['REQUEST_URI'];
            echo $before .'<a itemprop="item" href="'. esc_url( $request_uri ) .'"><span itemprop="name">'. esc_html__( 'Search Results for "', 'rara-business' ) . esc_html( get_search_query() ) . esc_html__( '"', 'rara-business' ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
        
        }elseif( is_day() ){
            $depth = 2;
            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_year_link( get_the_time( __( 'Y', 'rara-business' ) ) ) ) . '"><span itemprop="name">' . esc_html( get_the_time( __( 'Y', 'rara-business' ) ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . esc_html( $delimiter ) . '</span></span>';
            $depth ++;
            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_month_link( get_the_time( __( 'Y', 'rara-business' ) ), get_the_time( __( 'm', 'rara-business' ) ) ) ) . '"><span itemprop="name">' . esc_html( get_the_time( __( 'F', 'rara-business' ) ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . esc_html( $delimiter ) . '</span></span>';
            $depth ++;
            echo $before .'<a itemprop="item" href="' . esc_url( get_day_link( get_the_time( __( 'Y', 'rara-business' ) ), get_the_time( __( 'm', 'rara-business' ) ), get_the_time( __( 'd', 'rara-business' ) ) ) ) . '"><span itemprop="name">'. esc_html( get_the_time( __( 'd', 'rara-business' ) ) ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
        
        }elseif( is_month() ){
            $depth = 2;
            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_year_link( get_the_time( __( 'Y', 'rara-business' ) ) ) ) . '"><span itemprop="name">' . esc_html( get_the_time( __( 'Y', 'rara-business' ) ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . esc_html( $delimiter ) . '</span></span>';
            $depth++;
            echo $before .'<a itemprop="item" href="' . esc_url( get_month_link( get_the_time( __( 'Y', 'rara-business' ) ), get_the_time( __( 'm', 'rara-business' ) ) ) ) . '"><span itemprop="name">'. esc_html( get_the_time( __( 'F', 'rara-business' ) ) ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
        
        }elseif( is_year() ){
            $depth = 2;
            echo $before .'<a itemprop="item" href="' . esc_url( get_year_link( get_the_time( __( 'Y', 'rara-business' ) ) ) ) . '"><span itemprop="name">'. esc_html( get_the_time( __( 'Y', 'rara-business' ) ) ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
    
        }elseif( is_single() && !is_attachment() ){
            $depth = 2;
            if( rara_business_is_woocommerce_activated() && 'product' === get_post_type() ){ //For Woocommerce single product
                /** NEED TO CHECK THIS PORTION WHILE INTEGRATION WITH WOOCOMMERCE */
                if ( $terms = wc_get_product_terms( $post->ID, 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) ) ) {
                    $main_term = apply_filters( 'woocommerce_breadcrumb_main_term', $terms[0], $terms );
                    $ancestors = get_ancestors( $main_term->term_id, 'product_cat' );
                    $ancestors = array_reverse( $ancestors );
                    foreach ( $ancestors as $ancestor ) {
                        $ancestor = get_term( $ancestor, 'product_cat' );    
                        if ( ! is_wp_error( $ancestor ) && $ancestor ) {
                            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_term_link( $ancestor ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $ancestor->name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . esc_html( $delimiter ) . '</span></span>';
                            $depth++;
                        }
                    }
                    echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_term_link( $main_term ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $main_term->name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . esc_html( $delimiter ) . '</span></span>';
                }
                
                echo $before .'<a href="' . esc_url( get_the_permalink() ) . '" itemprop="item"><span itemprop="name">'. esc_html( get_the_title() ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
                
            }elseif ( get_post_type() != 'post' ){
                $post_type = get_post_type_object( get_post_type() );
                
                if( $post_type->has_archive == true ){// For CPT Archive Link
                   
                   // Add support for a non-standard label of 'archive_title' (special use case).
                   $label = !empty( $post_type->labels->archive_title ) ? $post_type->labels->archive_title : $post_type->labels->name;
                   printf( '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="%1$s" itemprop="item"><span itemprop="name">%2$s</span></a><meta itemprop="position" content="%3$s" />', esc_url( get_post_type_archive_link( get_post_type() ) ), $label, $depth );
                   echo '<meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . esc_html( $delimiter ) . '</span></span>';
                   $depth ++;
    
                }
                echo $before .'<a href="' . esc_url( get_the_permalink() ) . '" itemprop="item"><span itemprop="name">'. esc_html( get_the_title() ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
                
            }else{ //For Post
                $cat_object       = get_the_category();
                $potential_parent = 0;
                $depth            = 2;
                
                if( $show_front === 'page' && $post_page ){ //If static blog post page is set
                    $p = get_post( $post_page );
                    echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_permalink( $post_page ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $p->post_title ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . esc_html( $delimiter ) . '</span></span>';  
                    $depth++;
                }
                
                if( is_array( $cat_object ) ){ //Getting category hierarchy if any
        
                    //Now try to find the deepest term of those that we know of
                    $use_term = key( $cat_object );
                    foreach( $cat_object as $key => $object )
                    {
                        //Can't use the next($cat_object) trick since order is unknown
                        if( $object->parent > 0  && ( $potential_parent === 0 || $object->parent === $potential_parent ) ){
                            $use_term = $key;
                            $potential_parent = $object->term_id;
                        }
                    }
                    
                    $cat = $cat_object[$use_term];
              
                    $cats = get_category_parents( $cat, false, ',' );
                    $cats = explode( ',', $cats );

                    foreach ( $cats as $cat ) {
                        $cat_obj = get_term_by( 'name', $cat, 'category' );
                        if( is_object( $cat_obj ) ){
                            $term_url    = get_term_link( $cat_obj->term_id );
                            $term_name   = $cat_obj->name;
                            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( $term_url ) . '"><span itemprop="name">' . esc_html( $term_name ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /><span class="separator">' . esc_html( $delimiter ) . '</span></span>';
                            $depth ++;
                        }
                    }
                }
    
                echo $before .'<a itemprop="item" href="' . esc_url( get_the_permalink() ) . '"><span itemprop="name">'. esc_html( get_the_title() ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
                
            }
        
        }elseif( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ){
            $depth = 2;
            $post_type = get_post_type_object(get_post_type());
            if( get_query_var('paged') ){
                echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_post_type_archive_link( $post_type->name ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $post_type->label ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" />';
                echo ' <span class="separator">' . esc_html( $delimiter ) . '</span></span> ' . $before . sprintf( __('Page %s', 'rara-business'), get_query_var('paged') ) . $after;
            }elseif( is_archive() ){
                echo $before .'<a itemprop="item" href="' . esc_url( get_post_type_archive_link( $post_type->name ) ) . '"><span itemprop="name">'. esc_html( post_type_archive_title() ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
            }else{
                echo $before .'<a itemprop="item" href="' . esc_url( get_post_type_archive_link( $post_type->name ) ) . '"><span itemprop="name">'. esc_html( $post_type->label ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
            }
    
        }elseif( is_attachment() ){
            $depth = 2;
            $parent = get_post( $post->post_parent );
            $cat = get_the_category( $parent->ID ); 
            if( $cat ){
                $cat = $cat[0];
                echo get_category_parents( $cat, TRUE, ' <span class="separator">' . esc_html( $delimiter ) . '</span> ');
                echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_permalink( $parent ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( $parent->post_title ) . '<span></a><meta itemprop="position" content="'. absint( $depth ).'" />' . ' <span class="separator">' . esc_html( $delimiter ) . '</span></span>';
            }
            echo  $before .'<a itemprop="item" href="' . esc_url( get_post_type_archive_link( $post_type->name ) ) . '"><span itemprop="name">'. esc_html( get_the_title() ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
        
        }elseif( is_page() && !$post->post_parent ){
            $depth = 2;
            echo $before .'<a itemprop="item" href="' . esc_url( get_the_permalink() ) . '"><span itemprop="name">'. esc_html( get_the_title() ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" />'. $after;
    
        }elseif( is_page() && $post->post_parent ){
            $depth = 2;
            $parent_id  = $post->post_parent;
            $breadcrumbs = array();
            
            while( $parent_id ){
                $page = get_post( $parent_id );
                $breadcrumbs[] = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a href="' . esc_url( get_permalink( $page->ID ) ) . '" itemprop="item"><span itemprop="name">' . esc_html( get_the_title( $page->ID ) ) . '</span></a><meta itemprop="position" content="'. absint( $depth ).'" /></span>';
                $parent_id  = $page->post_parent;
                $depth++;
            }
            $breadcrumbs = array_reverse( $breadcrumbs );
            for ( $i = 0; $i < count( $breadcrumbs) ; $i++ ){
                echo $breadcrumbs[$i];
                if ( $i != count( $breadcrumbs ) - 1 ) echo ' <span class="separator">' . esc_html( $delimiter ) . '</span> ';
            }
            echo ' <span class="separator">' . esc_html( $delimiter ) . '</span> ' . $before .'<a href="' . get_permalink() . '" itemprop="item"><span itemprop="name">'. esc_html( get_the_title() ) .'</span></a><meta itemprop="position" content="'. absint( $depth ).'" /></span>'. $after;
        
        }elseif( is_404() ){
            echo $before . esc_html__( '404 Error - Page Not Found', 'rara-business' ) . $after;
        }
        
        if( get_query_var('paged') ) echo __( ' (Page', 'rara-business' ) . ' ' . get_query_var('paged') . __( ')', 'rara-business' );
        
        echo '</div></div><!-- .breadcrumb-wrapper -->';
        
    }                
}
endif;
add_action( 'rara_business_before_posts_content', 'rara_business_breadcrumb', 15 );

if( ! function_exists( 'rara_business_page_header' ) ) :
/**
 * Page Header
*/
function rara_business_page_header(){
    global $wp_query, $post;
    
    echo '<header class="page-header">';
    
    if( is_search() ){ ?>        
        <h1 class="page-title">
        <?php
            /* translators: %s: search query. */
            printf( esc_html__( 'Search Results for: %s', 'rara-business' ), get_search_query() );
        ?>
        </h1>
        <span>
            <?php
                /* translators: %s: number of posts found  */
                printf( esc_html__( 'We found %s results for web design. You can search again if you are unsatisfied.', 'rara-business' ), number_format_i18n( $wp_query->found_posts ) ); 
            ?>
        </span>
        <?php get_search_form();
    }
    
    if( is_archive() ){ 
        the_archive_title( '<h1 class="page-title">', '</h1>' );
        the_archive_description( '<div class="archive-description">', '</div>' );
    }
    
    if( is_page() ){ 
        if( is_page_template( 'templates/portfolio.php' ) ){
            the_title( '<h2 class="page-title">', '</h1>' );
            echo wpautop( wp_kses_post( $post->post_content ) );
        }else{
            the_title( '<h1 class="page-title">', '</h1>' );
        }
    }

    if( is_singular( 'rara-portfolio' ) ) {
        the_title( '<h1 class="page-title">', '</h1>' );
    }
    
    echo '</header><!-- .page-header -->';
}
endif;
add_action( 'rara_business_before_posts_content', 'rara_business_page_header', 20 );

if( ! function_exists( 'rara_business_entry_header' ) ) :
/**
 * Entry Header
*/
function rara_business_entry_header(){ 
    $default_options = rara_business_default_theme_options(); // Get default theme options
    $hide_date       = get_theme_mod( 'ed_post_date_meta', $default_options['ed_post_date_meta'] );
    $hide_author     = get_theme_mod( 'ed_post_author_meta', $default_options['ed_post_author_meta'] );
    ?>
    <header class="entry-header">
		<?php
		if( ( ! $hide_date || ! $hide_author ) && 'post' === get_post_type() ){ ?>
		<div class="entry-meta">
			<?php 
                if( ! $hide_date ) rara_business_posted_on();
                if( ! $hide_author ) rara_business_posted_by();
            ?>
		</div><!-- .entry-meta -->
		<?php
		}
        
        if( is_singular() ){
            the_title( '<h1 class="entry-title">', '</h1>' );
        }else{
            the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        }
        
        if( is_single() ) rara_business_categories();
        ?>
	</header><!-- .entry-header -->
    <?php
}
endif;
add_action( 'rara_business_posts_entry_content', 'rara_business_entry_header', 15 );
add_action( 'rara_business_post_entry_content', 'rara_business_entry_header', 20 );

if ( ! function_exists( 'rara_business_post_thumbnail' ) ) :
/**
 * Displays an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 */
function rara_business_post_thumbnail() {
    $default_options     = rara_business_default_theme_options(); // Get default theme options
    $show_post_thumbnail = get_theme_mod( 'ed_featured_image', $default_options['ed_featured_image'] );

	if ( is_singular() ) {
        if ( has_post_thumbnail() && $show_post_thumbnail ) { ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail( 'rara-business-featured' ); ?>
            </div><!-- .post-thumbnail -->
        <?php
        }
    } else { ?>

	<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
		<?php
			the_post_thumbnail( 'rara-business-featured', array(
				'alt' => the_title_attribute( array(
					'echo' => false,
				) ),
			) );
		?>
	</a>

	<?php }; // End is_singular().
}
endif;
add_action( 'rara_business_posts_entry_content', 'rara_business_post_thumbnail', 20 );
add_action( 'rara_business_page_entry_content', 'rara_business_post_thumbnail', 15 );
add_action( 'rara_business_post_entry_content', 'rara_business_post_thumbnail', 15 );

if( ! function_exists( 'rara_business_entry_content' ) ) :
/**
 * Entry Content
*/
function rara_business_entry_content(){
    $default_options = rara_business_default_theme_options(); // Get default theme options
    $ed_excerpt      = get_theme_mod( 'ed_excerpt', $default_options['ed_excerpt'] ); ?>
    <div class="entry-content" itemprop="text">
		<?php
			if( is_singular() || ! $ed_excerpt || ( get_post_format() != false ) ){
                the_content();
    
    			wp_link_pages( array(
    				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'rara-business' ),
    				'after'  => '</div>',
    			) );
            }else{
                the_excerpt();
            }
		?>
	</div><!-- .entry-content -->
    <?php
}
endif;
add_action( 'rara_business_posts_entry_content', 'rara_business_entry_content', 25 );
add_action( 'rara_business_page_entry_content', 'rara_business_entry_content', 20 );
add_action( 'rara_business_post_entry_content', 'rara_business_entry_content', 25 );

if( ! function_exists( 'rara_business_entry_footer' ) ) :
/**
 * Entry Footer 
*/
function rara_business_entry_footer(){ 
    $default_options = rara_business_default_theme_options(); // Get default theme options
    $readmore        = get_theme_mod( 'read_more_text', $default_options['read_more_text'] ); ?>
    <footer class="entry-footer">
		<?php 
        if( is_singular() ){
            if( is_single() ){
                rara_business_tags();
            }
        }elseif( $readmore ){
            echo '<a href="' . esc_url( get_the_permalink() ) . '" class="btn-readmore">' . esc_html( $readmore ) . '</a>';
        }
        
        if( get_edit_post_link() ){
            edit_post_link(
        		sprintf(
        			wp_kses(
        				/* translators: %s: Name of current post. Only visible to screen readers */
        				__( 'Edit <span class="screen-reader-text">%s</span>', 'rara-business' ),
        				array(
        					'span' => array(
        						'class' => array(),
        					),
        				)
        			),
        			get_the_title()
        		),
        		'<span class="edit-link">',
        		'</span>'
        	); 
        }
        ?>
	</footer><!-- .entry-footer -->
    <?php
}
endif;
add_action( 'rara_business_posts_entry_content', 'rara_business_entry_footer', 30 );
add_action( 'rara_business_page_entry_content', 'rara_business_entry_footer', 25 );
add_action( 'rara_business_post_entry_content', 'rara_business_entry_footer', 30 );

if( ! function_exists( 'rara_business_author' ) ) :
/**
 * Author Section
*/
function rara_business_author(){ 
    $default_options = rara_business_default_theme_options(); // Get default theme options
    $ed_author       = get_theme_mod( 'ed_author', $default_options['ed_author'] ); 

    if( ! $ed_author && get_the_author_meta( 'description' ) ){ ?>
    <div class="author-section">
		<div class="img-holder"><?php echo get_avatar( get_the_author_meta( 'ID' ), 170 ); ?></div>
		<div class="text-holder">
			<h3 class="name"><?php echo esc_html( get_the_author_meta( 'display_name' ) ); ?></h3>
			<?php echo wpautop( wp_kses_post( get_the_author_meta( 'description' ) ) ); ?>            
		</div>
	</div>
    <?php
    }
}
endif;
add_action( 'rara_business_after_post_content', 'rara_business_author', 15 );
add_action( 'rara_business_after_protfolio_post_content', 'rara_business_author', 10 );

if( ! function_exists( 'rara_business_pagination' ) ) :
/**
 * Paginations
*/
function rara_business_pagination(){
    if( is_single() ){
        $previous = get_previous_post_link(
    		'<div class="nav-previous">%link</div>',
    		esc_html__( 'Prev Post', 'rara-business' ) . '<span class="nav-arrow"><i class="fa fa-angle-left"></i></span><span>%title</span>',
    		false,
    		'',
    		'category'
    	);
    
    	$next = get_next_post_link(
    		'<div class="nav-next">%link</div>',
    		esc_html__( 'Next Post', 'rara-business' ) . '<span class="nav-arrow"><i class="fa fa-angle-right"></i></span><span>%title</span>',
    		false,
    		'',
    		'category'
    	); 
        
        if( $previous || $next ){?>            
            <nav class="navigation post-navigation" role="navigation">
    			<h2 class="screen-reader-text"><?php esc_html_e( 'Post Navigation', 'rara-business' ); ?></h2>
    			<div class="nav-links">
    				<?php
                        if( $previous ) echo $previous;
                        if( $next ) echo $next;
                    ?>
    			</div>
    		</nav>        
            <?php
        }
    }else{
        the_posts_pagination( array(
            'prev_next'          => false,
            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'rara-business' ) . ' </span>',
        ) );
    }
}
endif;
add_action( 'rara_business_after_post_content', 'rara_business_pagination', 20 );
add_action( 'rara_business_after_posts_content', 'rara_business_pagination' );
add_action( 'rara_business_after_protfolio_post_content', 'rara_business_pagination', 20 );

if( ! function_exists( 'rara_business_related_posts' ) ) :
/**
 * Related Posts
*/
function rara_business_related_posts(){ 
    global $post;

    $default_options = rara_business_default_theme_options(); // Get default theme options
    $ed_related_post = get_theme_mod( 'ed_related', $default_options['ed_related'] );
    $related_title   = get_theme_mod( 'related_post_title', $default_options['related_post_title'] ); 
    if( $ed_related_post ){
        $args = array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => 4,
            'ignore_sticky_posts' => true,
            'post__not_in'        => array( $post->ID ),
            'orderby'             => 'rand'
        );
        $cats = get_the_category( $post->ID );
        if( $cats ){
            $c = array();
            foreach( $cats as $cat ){
                $c[] = $cat->term_id; 
            }
            $args['category__in'] = $c;
        }
        
        $qry = new WP_Query( $args );
        
        if( $qry->have_posts() ){ ?>
        <section class="related-post">
    		<?php if( $related_title ) echo '<h2 class="section-title">' . esc_html( $related_title ) . '</h2>'; ?>
    		<div class="grid">
    			<?php 
                while( $qry->have_posts() ){ 
                    $qry->the_post(); ?>
                    <div class="col">
    					<a href="<?php the_permalink(); ?>" class="post-thumbnail">
                        <?php
                            if( has_post_thumbnail() ){
                                the_post_thumbnail( 'rara-business-blog' );
                            }else{ ?>
                                <img src="<?php echo esc_url( get_template_directory_uri() . '/images/rara-business-blog.jpg' ); ?>" alt="<?php the_title_attribute(); ?>" />
                            <?php 
                            }
                        ?>
                        </a>
    					
						<?php the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
    					
        			</div>
        			<?php 
                }
                wp_reset_postdata();  
                ?>
    		</div>
    	</section>
        <?php
        }
    }
}
endif;
add_action( 'rara_business_after_post_content', 'rara_business_related_posts', 25 );

if( ! function_exists( 'rara_business_popular_posts' ) ) :
/**
 * Popular Posts
*/
function rara_business_popular_posts(){ 
    global $post;

    $default_options = rara_business_default_theme_options(); // Get default theme options
    $ed_popular_post = get_theme_mod( 'ed_popular_posts', $default_options['ed_popular_posts'] );
    $popular_title   = get_theme_mod( 'popular_post_title', $default_options['popular_post_title'] ); 
    if( $ed_popular_post ){
        $args = array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => 4,
            'ignore_sticky_posts' => true,
            'post__not_in'        => array( $post->ID ),
            'orderby'             => 'comment_count'
        );

        $qry = new WP_Query( $args );
        
        if( $qry->have_posts() ){ ?>
        <section class="popular-post">
    		<?php if( $popular_title ) echo '<h2 class="section-title">' . esc_html( $popular_title ) . '</h2>'; ?>
    		<div class="grid">
    			<?php 
                while( $qry->have_posts() ){ 
                    $qry->the_post(); ?>
                    <div class="col">        				
    					<a href="<?php the_permalink(); ?>" class="post-thumbnail">
                        <?php
                            if( has_post_thumbnail() ){
                                the_post_thumbnail( 'rara-business-blog' );
                            }else{ ?>
                                <img src="<?php echo esc_url( get_template_directory_uri() . '/images/rara-business-blog.jpg' ); ?>" alt="<?php the_title_attribute(); ?>" />
                            <?php 
                            }
                        ?>
                        </a>
    					
						<?php the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>    					
        				
        			</div>
        			<?php 
                }
                wp_reset_postdata();  
                ?>
    		</div>
    	</section>
        <?php
        }
    }
}
endif;
add_action( 'rara_business_after_post_content', 'rara_business_popular_posts', 30 );
 
if( ! function_exists( 'rara_business_comment' ) ) :
/**
 * Comments
*/
function rara_business_comment(){
    // If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) :
		comments_template();
	endif;
}
endif;
add_action( 'rara_business_after_page_content', 'rara_business_comment' );
add_action( 'rara_business_after_post_content', 'rara_business_comment', 35 );

if( ! function_exists( 'rara_business_recent_posts' ) ) :
/**
 * Recent Posts
*/
function rara_business_recent_posts(){ 
    /** Load default theme options */
    $default_options =  rara_business_default_theme_options();
    
    $hide_date   = get_theme_mod( 'ed_post_date_meta', $default_options['ed_post_date_meta'] );
    $hide_author = get_theme_mod( 'ed_post_author_meta', $default_options['ed_post_author_meta'] );

    $args = array(
        'post_type'           => 'post',
        'posts_per_page'      => 6,
        'posts_status'        => 'publish',
        'ignore_sticky_posts' => true
    );
    
    $qry = new WP_Query( $args );
    
    if( $qry->have_posts() ){ ?>
    <section class="recent-post">
		<h2 class="section-title"><?php esc_html_e( 'Recent Posts', 'rara-business' ); ?></h2>
		<div class="grid">
			<?php 
            while( $qry->have_posts() ){ 
                $qry->the_post(); ?>
                <div class="col">    				
                    <a href="<?php the_permalink(); ?>" class="post-thumbnail">
                        <?php 
                        if( has_post_thumbnail() ){
                            the_post_thumbnail( 'rara-business-blog' );    
                        }else{
                            echo '<img src="'. esc_url( get_template_directory_uri() . '/images/rara-business-blog.jpg' ) .'" alt="'. esc_attr( get_the_title() ) .'">';    
                        }
                        ?>                        
                    </a>
                    <?php 
                        the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h3>' );
                    ?>    				
    				<div class="entry-meta">
    					<?php 
                            if( ! $hide_date ) rara_business_posted_on();
                            if( ! $hide_author ) rara_business_posted_by();
                        ?>
    				</div>
    			</div>
                <?php 
            }
            wp_reset_postdata();
            ?>
		</div>
	</section>
    <?php
    }
}
endif;
add_action( 'rara_business_recent_post', 'rara_business_recent_posts' );


if( ! function_exists( 'rara_business_content_end' ) ) :
/**
 * Content End
*/
function rara_business_content_end(){ 
    $home_sections = rara_business_get_home_sections();

    if( ! ( is_front_page() && ! is_home() && $home_sections ) ){ 
        if( ! is_404() && ! is_page_template( 'templates/portfolio.php') && ! is_tax( 'rara_portfolio_categories' ) ) echo '</div><!-- .content-grid -->'; ?>        
        </div><!-- .container -->
	</div><!-- #content -->
    <?php } 
}
endif;
add_action( 'rara_business_before_footer', 'rara_business_content_end', 20 );

if( ! function_exists( 'rara_business_footer_start' ) ) :
/**
 * Footer Start
*/
function rara_business_footer_start(){
    ?>
    <footer id="colophon" class="site-footer" itemscope itemtype="http://schema.org/WPFooter">
        <div class="container">
    <?php
}
endif;
add_action( 'rara_business_footer', 'rara_business_footer_start', 20 );

if( ! function_exists( 'rara_business_footer_top' ) ) :
/**
 * Footer Top
*/
function rara_business_footer_top(){    
    if( is_active_sidebar( 'footer-one' ) || is_active_sidebar( 'footer-two' ) || is_active_sidebar( 'footer-three' ) || is_active_sidebar( 'footer-four' ) ){ ?>
    <div class="footer-t">		
		<div class="grid">
        <?php if( is_active_sidebar( 'footer-one' ) ){ ?>
			<div class="col">
			   <?php dynamic_sidebar( 'footer-one' ); ?>	
			</div>
        <?php } ?>
		
        <?php if( is_active_sidebar( 'footer-two' ) ){ ?>
            <div class="col">
			   <?php dynamic_sidebar( 'footer-two' ); ?>	
			</div>
        <?php } ?>
        
        <?php if( is_active_sidebar( 'footer-three' ) ){ ?>
            <div class="col">
			   <?php dynamic_sidebar( 'footer-three' ); ?>	
			</div>
        <?php } ?>
        
        <?php if( is_active_sidebar( 'footer-four' ) ){ ?>
            <div class="col">
			   <?php dynamic_sidebar( 'footer-four' ); ?>	
			</div>
        <?php } ?>
        </div>		
	</div>
    <?php 
    }   
}
endif;
add_action( 'rara_business_footer', 'rara_business_footer_top', 30 );

if( ! function_exists( 'rara_business_footer_bottom' ) ) :
/**
 * Footer Bottom
*/
function rara_business_footer_bottom(){ ?>
    <div class="footer-b">		
    	<?php
            rara_business_get_footer_copyright();
            echo '<span class="by">';
            echo esc_html__( 'Rara Business | Developed By ', 'rara-business' );
            echo '<a href="' . esc_url( 'https://raratheme.com/' ) .'" rel="nofollow" target="_blank">' . esc_html__( 'Rara Theme', 'rara-business' ) . '</a>';
            echo '</span>';
            
            /* translators: 1: poweredby, 2: link, 3: span tag closed  */
            printf( esc_html__( ' %1$sPowered by %2$s%3$s', 'rara-business' ), '<span class="powered-by">', '<a href="'. esc_url( __( 'https://wordpress.org/', 'rara-business' ) ) .'" target="_blank">WordPress</a>.', '</span>' );

            if ( function_exists( 'the_privacy_policy_link' ) ) {
                the_privacy_policy_link( '<span class="policy_link">', '</span>');
            }
        ?>		
    </div>
    <?php
}
endif;
add_action( 'rara_business_footer', 'rara_business_footer_bottom', 40 );

if( ! function_exists( 'rara_business_footer_end' ) ) :
/**
 * Footer End 
*/
function rara_business_footer_end(){ ?>
        </div><!-- .container -->
    </footer><!-- #colophon -->
    <?php
}
endif;
add_action( 'rara_business_footer', 'rara_business_footer_end', 50 );

if( ! function_exists( 'rara_business_page_end' ) ) :
/**
 * Page End
*/
function rara_business_page_end(){
    ?>
    </div><!-- #page -->
    <?php
}
endif;
add_action( 'rara_business_after_footer', 'rara_business_page_end', 20 );