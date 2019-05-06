<?php
/**
 * Theme functions and definitions
 *
 * @package Software_Company
 */

/**
 * After setup theme hook
 */
function software_commpany_theme_setup(){
    /*
     * Make chile theme available for translation.
     * Translations can be filed in the /languages/ directory.
     */
    load_child_theme_textdomain( 'software-company', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'software_commpany_theme_setup' );

/**
 * Load assets.
 *
 */
function software_commpany_enqueue_styles_and_scripts() {
    $my_theme = wp_get_theme();
    $version = $my_theme['Version'];
    
    wp_enqueue_style( 'rara-business-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'software-company-style', get_stylesheet_directory_uri() . '/style.css', array( 'rara-business-style' ), $version );
}
add_action( 'wp_enqueue_scripts', 'software_commpany_enqueue_styles_and_scripts' );

/**
 * Rara Magazine Theme Info
 */
function rara_business_customizer_theme_info( $wp_customize ) {
	
    $wp_customize->add_section( 'theme_info_section', array(
        'title'       => __( 'Demo & Documentation' , 'software-company' ),
        'priority'    => 6,
    ) );
    
    /** Important Links */
    $wp_customize->add_setting( 'theme_info_setting',
        array(
            'default' => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );
    
    $theme_info = '<p>';

    /* translators: 1: string, 2: preview url, 3: string */
    $theme_info .= sprintf( '%1$s<a href="%2$s" target="_blank">%3$s</a>', esc_html__( 'Demo Link : ', 'software-company' ), esc_url( __( 'https://demo.raratheme.com/software-company/', 'software-company' ) ), esc_html__( 'Click here.', 'software-company' ) );

    $theme_info .= '</p><p>';

    /* translators: 1: string, 2: documentation url, 3: string */
    $theme_info .= sprintf( '%1$s<a href="%2$s" target="_blank">%3$s</a>', esc_html__( 'Documentation Link : ', 'software-company' ), esc_url( 'https://raratheme.com/documentation/software-company/' ), esc_html__( 'Click here.', 'software-company' ) );

    $theme_info .= '</p>';

    $wp_customize->add_control( new Rara_Business_Note_Control( $wp_customize,
        'theme_info_setting', 
            array(
                'section'     => 'theme_info_section',
                'description' => $theme_info
            )
        )
    );
}
add_action( 'customize_register', 'rara_business_customizer_theme_info', 15 );

/**
 * Add demo content info
 */
function rara_business_customizer_demo_content( $wp_customize ) {
        
    $wp_customize->add_section( 'demo_content_section' , array(
        'title'       => __( 'Demo Content Import' , 'software-company' ),
        'priority'    => 7,
        ));
        
    $wp_customize->add_setting(
        'demo_content_instruction',
        array(
            'sanitize_callback' => 'wp_kses_post'
        )
    );

    /* translators: 1: string, 2: url, 3: string */
    $demo_content_description = sprintf( '%1$s<a class="documentation" href="%2$s" target="_blank">%3$s</a>', esc_html__( 'Software Company comes with demo content import feature. You can import the demo content with just one click. For step-by-step video tutorial, ', 'software-company' ), esc_url( 'https://raratheme.com/blog/import-demo-content-rara-themes/' ), esc_html__( 'Click here', 'software-company' ) );


    $wp_customize->add_control(
        new Rara_Business_Note_Control( 
            $wp_customize,
            'demo_content_instruction',
            array(
                'section'       => 'demo_content_section',
                'description'   => $demo_content_description
            )
        )
    );

    $theme_demo_content_desc = '';
    
    $theme_demo_content_desc .= '<span class="sticky_info_row download-link"><label class="row-element">' . __( 'Download Demo Content', 'software-company' ) . ': </label><a href="' . esc_url( 'https://raratheme.com/documentation/software-company/' ) . '" target="_blank">' . __( 'Click here', 'software-company' ) . '</a></span><br />';
    
    if( ! class_exists( 'RDDI_init' ) ) {
        $theme_demo_content_desc .= '<span class="sticky_info_row"><label class="row-element">' . __( 'Plugin required', 'software-company' ) . ': </label><a href="' . esc_url( 'https://wordpress.org/plugins/rara-one-click-demo-import/' ) . '" target="_blank">' . __( 'Rara One Click Demo Import', 'software-company' ) . '</a></span><br />';
    }

    $wp_customize->add_setting( 'theme_demo_content_info',array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
        ));

    // Demo content 
    $wp_customize->add_control( new Rara_Business_Note_Control( $wp_customize ,'theme_demo_content_info',array(
        'section'     => 'demo_content_section',
        'description' => $theme_demo_content_desc
        )));

}
add_action( 'customize_register', 'rara_business_customizer_demo_content', 15 );


/**
 * Modifiy customizer control from child theme
 */
function software_company_customizer_register_controls( $wp_customize ) {

    // Remove controls
    $wp_customize->remove_control('ed_header_contact_details');
    $wp_customize->remove_control('header_phone');
    $wp_customize->remove_control('header_address');
    $wp_customize->remove_control('header_email');
 
}
add_action( 'customize_register', 'software_company_customizer_register_controls', 15 );


function rara_business_header(){ 
    $default_options = rara_business_default_theme_options(); // Get default theme options

    $ed_header_contact = get_theme_mod( 'ed_header_contact_details', $default_options['ed_header_contact_details'] );
    $icon              = get_theme_mod( 'custom_link_icon', $default_options['custom_link_icon'] );
    $label             = get_theme_mod( 'custom_link_label', $default_options['custom_link_label'] );
    $ed_header_social  = get_theme_mod( 'ed_header_social_links', $default_options['ed_header_social_links'] );
    $social_links      = get_theme_mod( 'header_social_links', $default_options['header_social_links'] );
    $link              = get_theme_mod( 'custom_link', $default_options['custom_link'] );
    ?>
    
    <header id="masthead" class="site-header" itemscope itemtype="http://schema.org/WPHeader">
        <?php 
            if( ! ( $link && $label ) && ! ( $ed_header_social && ! empty( $social_links ) ) ){ 
                $class = ' hide-header-top';
            } else {
                $class ='';
            }
        ?>
        <div class="header-t<?php echo esc_attr( $class ); ?>">
            <div class="container">
                <?php 
                    rara_business_social_links( $ed_header_social, $social_links );

                    if( $link && $label ){ ?>
                        <div class="inquiry-btn">
                            <?php rara_business_custom_link( $icon, $link, $label ); ?>
                        </div>
                        <?php 
                    } 
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
                       $branding_class = ' logo-with-site-identity';                                                                                                                          
                    } else {
                        $branding_class = '';
                    }
                ?>
                <div class="site-branding<?php echo esc_attr( $branding_class ); ?>" itemscope itemtype="http://schema.org/Organization">
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
                                <p class="site-description" itemprop="description"><?php echo $description; ?></p>
                            <?php
        
                            }
                        echo '</div><!-- .text-logo -->';
                    ?>
                </div>
                <div class="right">
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

/**
 * Footer Bottom
*/
function rara_business_footer_bottom(){ ?>
    <div class="footer-b">      
        <?php
            rara_business_get_footer_copyright();
            echo '<span class="by">';
            echo esc_html__( 'Software Company | Developed By ', 'software-company' ); 
            echo '<a href="' . esc_url( 'https://raratheme.com/' ) .'" rel="nofollow" target="_blank">' . esc_html__( 'Rara Theme', 'software-company' ) . '</a>.';
            echo '</span>';
            
            /* translators: 1: poweredby, 2: link, 3: span tag closed  */
            printf( esc_html__( ' %1$sPowered by %2$s%3$s', 'software-company' ), '<span class="powered-by">', '<a href="'. esc_url( __( 'https://wordpress.org/', 'software-company' ) ) .'" target="_blank">WordPress</a>.', '</span>' );

            if ( function_exists( 'the_privacy_policy_link' ) ) {
                the_privacy_policy_link( '<span class="policy_link">', '</span>');
            }
        ?>      
    </div>
    <?php
}

 /**
 * Register custom fonts.
 */
function rara_business_fonts_url() {
    $fonts_url = '';

    /* Translators: If there are characters in your language that are not
    * supported by respective fonts, translate this to 'off'. Do not translate
    * into your own language.
    */

    $poppins_font       = _x( 'on', 'Poppins font: on or off', 'software-company' );
    $nunito_font = _x( 'on', 'Nunito font: on or off', 'software-company' );

    if ( 'off' !== $poppins_font || 'off' !== $nunito_font ) {
        $font_families = array();

        if ( 'off' !== $poppins_font ) {
            $font_families[] = 'Poppins:100,100i,300,300i,400,400i,700,700i,900,900i';

        }

        if ( 'off' !== $nunito_font ) {
            $font_families[] = 'Nunito:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i';

        }

        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),
            'subset' => urlencode( 'latin,latin-ext' ),
        );

        $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
    }

    return esc_url_raw( $fonts_url );
}