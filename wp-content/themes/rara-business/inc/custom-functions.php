<?php
/**
 * Rara Business Custom functions 
 * 
 * @package Rara_Business
 */
 
if ( ! function_exists( 'rara_business_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function rara_business_setup() {
    /*
     * Make theme available for translation.
     * Translations can be filed in the /languages/ directory.
     * If you're building a theme based on Rara Business, use a find and replace
     * to change 'rara-business' to the name of your theme in all the template files.
     */
    load_theme_textdomain( 'rara-business', get_template_directory() . '/languages' );

    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    /*
     * Let WordPress manage the document title.
     * By adding theme support, we declare that this theme does not use a
     * hard-coded <title> tag in the document head, and expect WordPress to
     * provide it for us.
     */
    add_theme_support( 'title-tag' );

    /*
     * Enable support for Post Thumbnails on posts and pages.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support( 'post-thumbnails' );

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary', 'rara-business' ),
    ) );

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support( 'html5', array(
        'search-form',
        'comment-list',
        'gallery',
        'caption',
    ) );

    // Set up the WordPress core custom background feature.
    add_theme_support( 'custom-background', apply_filters( 'rara_business_custom_background_args', array(
        'default-color' => 'ffffff',
        'default-image' => '',
    ) ) );

    // Add excerpt support for page.
    add_post_type_support( 'page', 'excerpt' );

    // Add theme support for selective refresh for widgets.
    add_theme_support( 'customize-selective-refresh-widgets' );

    /**
     * Add support for core custom logo.
     *
     * @link https://codex.wordpress.org/Theme_Logo
     */
    add_theme_support( 'custom-logo', array(
        'header-text' => array( 'site-title', 'site-description' ),
    ) );
    
    add_theme_support( 'custom-header', apply_filters( 'rara_business_custom_header_args', array(
        'default-image'    => get_template_directory_uri().'/images/banner-image.jpg',
        'width'            => 1920,
        'height'           => 780,
        'header-text'      => false,
        'video'            => true,
    ) ) );

    register_default_headers( array(
        'default-image' => array(
            'url'           => '%s/images/banner-image.jpg',
            'thumbnail_url' => '%s/images/banner-image.jpg',
            'description'   => __( 'Default Header Image', 'rara-business' ),
        ),
    ) );
    
    /** Images sizes */
    add_image_size( 'rara-business-featured', 770, 499, true );
    add_image_size( 'rara-business-team', 370, 280, true );
    add_image_size( 'rara-business-blog', 370, 240, true );
    add_image_size( 'rara-business-portfolio', 370, 370, true );
    add_image_size( 'rara-business-schema', 600, 60 );
    
    /** Starter Content */
    $starter_content = array(
        // Specify the core-defined pages to create and add custom thumbnails to some of them.
        'posts' => array( 
            'home', 
            'blog',
            'portfolio' => array(
                'post_type'  => 'page',
                'post_title' => 'Portfolio',
                'template'   => 'templates/portfolio.php',
            )
        ),
        
        // Default to a static front page and assign the front and posts pages.
        'options' => array(
            'show_on_front' => 'page',
            'page_on_front' => '{{home}}',
            'page_for_posts' => '{{blog}}',
        ),
        
        // Set up nav menus for each of the two areas registered in the theme.
        'nav_menus' => array(
            // Assign a menu to the "top" location.
            'primary' => array(
                'name' => __( 'Primary', 'rara-business' ),
                'items' => array(
                    'page_home',
                    'page_blog',
                    'page_portfolio' => array(
                        'type'      => 'post_type',
                        'object'    => 'page',
                        'object_id' => '{{portfolio}}',
                    )
                )
            )
        ),
    );
    
    $starter_content = apply_filters( 'rara_business_starter_content', $starter_content );

    add_theme_support( 'starter-content', $starter_content );

    // Add theme support for Responsive Videos.
   add_theme_support( 'jetpack-responsive-videos' );
}
endif;
add_action( 'after_setup_theme', 'rara_business_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function rara_business_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'rara_business_content_width', 770 );
}
add_action( 'after_setup_theme', 'rara_business_content_width', 0 );

/**
 * Enqueue scripts and styles.
 */
function rara_business_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $build         = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '/build' : '';
    $suffix        = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
    $rtc_activated = rara_business_is_rara_theme_companion_activated();
    
    wp_enqueue_style( 'animate', get_template_directory_uri(). '/css' . $build . '/animate' . $suffix . '.css', array(), '3.5.2' );

    wp_enqueue_style( 'rara-business-google-fonts', rara_business_fonts_url(), array(), null );

    if( rara_business_is_woocommerce_activated() ){
        wp_enqueue_style( 'rara-business-woocommerce', get_template_directory_uri(). '/css' . $build . '/woocommerce-style' . $suffix . '.css' );
    }

    if( $rtc_activated && is_active_widget( false, false, 'rrtc_description_widget' ) ){
        wp_enqueue_style( 'perfect-scrollbar', get_template_directory_uri(). '/css' . $build . '/perfect-scrollbar' . $suffix . '.css', array(), '1.3.0' );
        wp_enqueue_script( 'perfect-scrollbar', get_template_directory_uri() . '/js' . $build . '/perfect-scrollbar' . $suffix . '.js', array( 'jquery' ), '1.3.0', true ); 
    }

    wp_enqueue_style( 'rara-business-style', get_stylesheet_uri(), array(), RARA_BUSINESS_THEME_VERSION );

    if( $rtc_activated && ( is_front_page() || is_page_template( 'templates/portfolio.php' ) ) ){
        wp_enqueue_script( 'masonry' );
        wp_enqueue_script( 'isotope-pkgd', get_template_directory_uri() . '/js' . $build . '/isotope.pkgd' . $suffix . '.js', array( 'jquery' ), '3.0.5', true );    
    }
    wp_enqueue_script( 'all', get_template_directory_uri() . '/js' . $build . '/all' . $suffix . '.js', array( 'jquery' ), '5.6.3', true );
    wp_enqueue_script( 'v4-shims', get_template_directory_uri() . '/js' . $build . '/v4-shims' . $suffix . '.js', array( 'jquery' ), '5.6.3', true );
    wp_enqueue_script( 'wow', get_template_directory_uri() . '/js' . $build . '/wow' . $suffix . '.js', array( 'jquery' ), RARA_BUSINESS_THEME_VERSION, true );

    // Register custom js
    wp_register_script( 'rara-business-custom', get_template_directory_uri() . '/js' . $build . '/custom' . $suffix . '.js', array( 'jquery' ), RARA_BUSINESS_THEME_VERSION, true );

    $localize_array = array(
        // Rtl
        'rtl'       => is_rtl(),
    );

    wp_localize_script( 'rara-business-custom', 'rb_localize_data', $localize_array );

    // Enqueued custom script with localized data.
    wp_enqueue_script( 'rara-business-custom' );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'rara_business_scripts' );

/**
 * Enqueue admin scripts.
 */
function rara_business_admin_scripts( $hook_suffix ) {
    // Register admin js script
    wp_register_script( 'rara-business-admin-js', get_template_directory_uri().'/inc/js/admin.js', array( 'jquery' ), RARA_BUSINESS_THEME_VERSION, true );

    // localize script to remove metabox in front page
    $show_front         = get_option( 'show_on_front' ); //What to show on the front page
    $front_page_id      = get_option( 'page_on_front' ); //The ID of the static front page.
    $frontpage_sections = rara_business_get_home_sections();

    $admin_data    = array();
    if ( $hook_suffix == 'post.php' || $hook_suffix == 'post-new.php' ) {

        if( isset( $_GET['post'] ) && ! empty( $_GET['post'] ) ) {
            $post_id = $_GET['post'];
        } else {
            $post_id = -9999;
        }

        // Get page template
        $page_template = get_page_template_slug( $post_id );

        if( ( 'page' == $show_front && $front_page_id > 0 ) && $post_id == $front_page_id && ! empty( $frontpage_sections ) ){
            $admin_data['hide_metabox'] = 1;
        } elseif ( '' != $page_template ) {
            $admin_data['hide_metabox'] = 1;
        } else {
            $admin_data['hide_metabox'] = '';
        }
    }else {
        $admin_data['hide_metabox'] = '';
    }
    wp_localize_script( 'rara-business-admin-js', 'rb_show_metabox', $admin_data );

    // Enqueued script with localized data.
    wp_enqueue_script( 'rara-business-admin-js' );
}
add_action( 'admin_enqueue_scripts', 'rara_business_admin_scripts' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function rara_business_body_classes( $classes ) {
    $default_options     = rara_business_default_theme_options(); // Get default theme options
    $banner_control      = get_theme_mod( 'ed_banner_section', $default_options['ed_banner_section'] );
    $custom_header_image = get_header_image_tag(); // get custom header image tag

    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    if( is_front_page() && ! is_home() && ( has_header_video() ||  ! empty( $custom_header_image ) ) && 'no_banner' != $banner_control ){
        $classes[] = 'homepage hasbanner';
    }

    // Add sibebar layout classes.
    $classes[] = rara_business_sidebar_layout();

    return $classes;
}
add_filter( 'body_class', 'rara_business_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function rara_business_pingback_header() {
    if ( is_singular() && pings_open() ) {
        echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
    }
}
add_action( 'wp_head', 'rara_business_pingback_header' );

if( ! function_exists( 'rara_business_change_comment_form_default_fields' ) ) :
/**
 * Change Comment form default fields i.e. author, email & url.
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function rara_business_change_comment_form_default_fields( $fields ){
    
    // get the current commenter if available
    $commenter = wp_get_current_commenter();
 
    // core functionality
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );    
 
    // Change just the author field
    $fields['author'] = '<p class="comment-form-author"><label for="author">' . esc_html__( 'Name', 'rara-business' ) . '<span class="required">*</span></label><input id="author" name="author" placeholder="' . esc_attr__( 'Name*', 'rara-business' ) . '" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>';
    
    $fields['email'] = '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'rara-business' ) . '<span class="required">*</span></label><input id="email" name="email" placeholder="' . esc_attr__( 'Email*', 'rara-business' ) . '" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>';
    
    $fields['url'] = '<p class="comment-form-url"><label for="url">' . esc_html__( 'Website', 'rara-business' ) . '</label><input id="url" name="url" placeholder="' . esc_attr__( 'Website', 'rara-business' ) . '" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>'; 
    
    return $fields;
    
}
endif;
add_filter( 'comment_form_default_fields', 'rara_business_change_comment_form_default_fields' );

if( ! function_exists( 'rara_business_change_comment_form_defaults' ) ) :
/**
 * Change Comment Form defaults
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function rara_business_change_comment_form_defaults( $defaults ){
    
    $defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Comment', 'rara-business' ) . '</label><textarea id="comment" name="comment" placeholder="' . esc_attr__( 'Comment', 'rara-business' ) . '" cols="45" rows="5" aria-required="true"></textarea></p>';
    
    return $defaults;
    
}
endif;
add_filter( 'comment_form_defaults', 'rara_business_change_comment_form_defaults' );

if ( ! function_exists( 'rara_business_excerpt_more' ) ) :
/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... * 
 */
function rara_business_excerpt_more( $more ) {
    return is_admin() ? $more : ' &hellip; ';
}
endif;
add_filter( 'excerpt_more', 'rara_business_excerpt_more' );

if ( ! function_exists( 'rara_business_excerpt_length' ) ) :
/**
 * Changes the default 55 character in excerpt 
*/
function rara_business_excerpt_length( $length ) {
    $excerpt_length = get_theme_mod( 'excerpt_length', 55 );
    return is_admin() ? $length : absint( $excerpt_length );    
}
endif;
add_filter( 'excerpt_length', 'rara_business_excerpt_length', 999 );

if ( !function_exists( 'rara_business_video_controls' ) ) :
/**
 * Customize video play/pause button in the custom header.
 *
 * @param array $settings Video settings.
 */
function rara_business_video_controls( $settings ) {
    $settings['l10n']['play'] = '<span class="screen-reader-text">' . __( 'Play background video', 'rara-business' ) . '</span>' . rara_business_get_svg( array( 'icon' => 'play' ) );
    $settings['l10n']['pause'] = '<span class="screen-reader-text">' . __( 'Pause background video', 'rara-business' ) . '</span>' . rara_business_get_svg( array( 'icon' => 'pause' ) );
    return $settings;
}
endif;
add_filter( 'header_video_settings', 'rara_business_video_controls' );

if( ! function_exists( 'rara_business_include_svg_icons' ) ) :
/**
 * Add SVG definitions to the footer.
 */
function rara_business_include_svg_icons() {
    // Define SVG sprite file.
    $svg_icons = get_parent_theme_file_path( '/images/svg-icons.svg' );

    // If it exists, include it.
    if ( file_exists( $svg_icons ) ) {
        require_once( $svg_icons );
    }
}
endif;
add_action( 'wp_footer', 'rara_business_include_svg_icons', 9999 );

if( ! function_exists( 'rara_business_get_the_archive_title' ) ) :
/**
 * Filter Archive Title
 */
function rara_business_get_the_archive_title( $title ){
    /** Load default theme options */
    $default_options =  rara_business_default_theme_options();
    $hide_prefix     = get_theme_mod( 'ed_prefix_archive', $default_options['ed_prefix_archive'] );

    if( $hide_prefix ){
        if( is_category() ){
            $title = single_cat_title( '', false );
        }elseif ( is_tag() ){
            $title = single_tag_title( '', false );
        }elseif( is_author() ){
            $title = '<span class="vcard">' . get_the_author() . '</span>';
        }elseif ( is_year() ) {
            $title = get_the_date( __( 'Y', 'rara-business' ) );
        }elseif ( is_month() ) {
            $title = get_the_date( __( 'F Y', 'rara-business' ) );
        }elseif ( is_day() ) {
            $title = get_the_date( __( 'F j, Y', 'rara-business' ) );
        }elseif ( is_post_type_archive() ) {
            $title = post_type_archive_title( '', false );
        }elseif ( is_tax() ) {
            $tax = get_taxonomy( get_queried_object()->taxonomy );
            $title = single_term_title( '', false );
        }
    }    
    return $title;
}
endif;
add_filter( 'get_the_archive_title', 'rara_business_get_the_archive_title' );

if( ! function_exists( 'rara_business_single_post_schema' ) ) :
/**
 * Single Post Schema
 *
 * @return string
 */
function rara_business_single_post_schema() {
    if ( is_singular( 'post' ) ) {
        global $post;
        $custom_logo_id = get_theme_mod( 'custom_logo' );

        $site_logo   = wp_get_attachment_image_src( $custom_logo_id , 'rara-business-schema' );
        $images      = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
        $excerpt     = rara_business_escape_text_tags( $post->post_excerpt );
        $content     = $excerpt === "" ? mb_substr( rara_business_escape_text_tags( $post->post_content ), 0, 110 ) : $excerpt;
        $schema_type = ! empty( $custom_logo_id ) && has_post_thumbnail( $post->ID ) ? "BlogPosting" : "Blog";

        $args = array(
            "@context"  => "http://schema.org",
            "@type"     => $schema_type,
            "mainEntityOfPage" => array(
                "@type" => "WebPage",
                "@id"   => esc_url( get_permalink( $post->ID ) )
            ),
            "headline"  => esc_html( get_the_title( $post->ID ) ),
            "image"     => array(
                "@type"  => "ImageObject",
                "url"    => $images[0],
                "width"  => $images[1],
                "height" => $images[2]
            ),
            "datePublished" => get_the_time( DATE_ISO8601, $post->ID ),
            "dateModified"  => get_post_modified_time(  DATE_ISO8601, __return_false(), $post->ID ),
            "author"        => array(
                "@type"     => "Person",
                "name"      => rara_business_escape_text_tags( get_the_author_meta( 'display_name', $post->post_author ) )
            ),
            "publisher" => array(
                "@type"       => "Organization",
                "name"        => get_bloginfo( 'name' ),
                "description" => get_bloginfo( 'description' ),
                "logo"        => array(
                    "@type"   => "ImageObject",
                    "url"     => $site_logo[0],
                    "width"   => $site_logo[1],
                    "height"  => $site_logo[2]
                )
            ),
            "description" => ( class_exists('WPSEO_Meta') ? WPSEO_Meta::get_value( 'metadesc' ) : $content )
        );

        if ( has_post_thumbnail( $post->ID ) ) :
            $args['image'] = array(
                "@type"  => "ImageObject",
                "url"    => $images[0],
                "width"  => $images[1],
                "height" => $images[2]
            );
        endif;

        if ( ! empty( $custom_logo_id ) ) :
            $args['publisher'] = array(
                "@type"       => "Organization",
                "name"        => get_bloginfo( 'name' ),
                "description" => get_bloginfo( 'description' ),
                "logo"        => array(
                    "@type"   => "ImageObject",
                    "url"     => $site_logo[0],
                    "width"   => $site_logo[1],
                    "height"  => $site_logo[2]
                )
            );
        endif;

        echo '<script type="application/ld+json">' , PHP_EOL;

        if ( version_compare( PHP_VERSION, '5.4.0' , '>=' ) ) {
            echo wp_json_encode( $args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) , PHP_EOL;
        } else {
            echo wp_json_encode( $args ) , PHP_EOL;
        }

        echo '</script>' , PHP_EOL;
    }
}
endif;
add_action( 'wp_head', 'rara_business_single_post_schema' );

if( ! function_exists( 'rara_business_get_comment_author_link' ) ) :
    /**
     * Filter to modify comment author link
     * @link https://developer.wordpress.org/reference/functions/get_comment_author_link/
     */
    function rara_business_get_comment_author_link( $return, $author, $comment_ID ){
        $comment = get_comment( $comment_ID );
        $url     = get_comment_author_url( $comment );
        $author  = get_comment_author( $comment );
     
        if ( empty( $url ) || 'http://' == $url )
            $return = '<span itemprop="name">'. esc_html( $author ) .'</span>';
        else
            $return = '<span itemprop="name"><a href="'. esc_url( $url ) .'" rel="external nofollow" class="url" itemprop="url">'. esc_html( $author ) .'</a></span>';

        return $return;
    }
endif;
add_filter( 'get_comment_author_link', 'rara_business_get_comment_author_link', 10, 3 );