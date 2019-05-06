( function( api ) {

    // Extends our custom "example-1" section.
    api.sectionConstructor['pro-section'] = api.Section.extend( {

        // No events for this type of section.
        attachEvents: function () {},

        // Always make the section active.
        isContextuallyActive: function () {
            return true;
        }
    } );

} )( wp.customize );

jQuery(document).ready(function($) {
	/* Move widgets to their respective sections */
	wp.customize.section( 'sidebar-widgets-services' ).panel( 'frontpage_panel' );
    wp.customize.section( 'sidebar-widgets-services' ).priority( '50' );

    wp.customize.section( 'sidebar-widgets-about' ).panel( 'frontpage_panel' );
    wp.customize.section( 'sidebar-widgets-about' ).priority( '55' );
    
    wp.customize.section( 'sidebar-widgets-choose-us' ).panel( 'frontpage_panel' );
    wp.customize.section( 'sidebar-widgets-choose-us' ).priority( '60' );

    wp.customize.section( 'sidebar-widgets-team' ).panel( 'frontpage_panel' );
    wp.customize.section( 'sidebar-widgets-team' ).priority( '65' );

    wp.customize.section( 'sidebar-widgets-testimonial' ).panel( 'frontpage_panel' );
    wp.customize.section( 'sidebar-widgets-testimonial' ).priority( '70' );

    wp.customize.section( 'sidebar-widgets-stats' ).panel( 'frontpage_panel' );
    wp.customize.section( 'sidebar-widgets-stats' ).priority( '75' );

    wp.customize.section( 'sidebar-widgets-cta' ).panel( 'frontpage_panel' );
    wp.customize.section( 'sidebar-widgets-cta' ).priority( '80' );

    wp.customize.section( 'sidebar-widgets-faq' ).panel( 'frontpage_panel' );
    wp.customize.section( 'sidebar-widgets-faq' ).priority( '85' );

    wp.customize.section( 'sidebar-widgets-client' ).panel( 'frontpage_panel' );
    wp.customize.section( 'sidebar-widgets-client' ).priority( '90' );
    
    // Scroll to section
    $('body').on('click', '#sub-accordion-panel-frontpage_panel .control-subsection .accordion-section-title', function(event) {
        var section_id = $(this).parent('.control-subsection').attr('id');
        scrollToSection( section_id );
    });
    
});

function scrollToSection( section_id ){
    var preview_section_id = "banner_section";

    var $contents = jQuery('#customize-preview iframe').contents();

    switch ( section_id ) {
        
        case 'accordion-section-header_image':
        preview_section_id = "banner-section";
        break;

        case 'accordion-section-sidebar-widgets-services':
        preview_section_id = "services-section";
        break;

        case 'accordion-section-sidebar-widgets-about':
        preview_section_id = "about-section";
        break;
        
        case 'accordion-section-sidebar-widgets-choose-us':
        preview_section_id = "choose-us";
        break;

        case 'accordion-section-sidebar-widgets-team':
        preview_section_id = "team-section";
        break;

        case 'accordion-section-sidebar-widgets-testimonial':
        preview_section_id = "testimonial-section";
        break;
        
        case 'accordion-section-sidebar-widgets-stats':
        preview_section_id = "stats-section";
        break;

        case 'accordion-section-portfolio_section':
        preview_section_id = "portfolio-section";
        break;

        case 'accordion-section-blog_section':
        preview_section_id = "blog-section";
        break;

        case 'accordion-section-sidebar-widgets-cta':
        preview_section_id = "cta-section";
        break;

        case 'accordion-section-sidebar-widgets-faq':
        preview_section_id = "faq-section";
        break;

        case 'accordion-section-sidebar-widgets-client':
        preview_section_id = "client-section";
        break;
    }

    if( $contents.find('#'+preview_section_id).length > 0 && $contents.find('.home').length > 0 ){
        $contents.find("html, body").animate({
        scrollTop: $contents.find( "#" + preview_section_id ).offset().top
        }, 1000);
    }
}