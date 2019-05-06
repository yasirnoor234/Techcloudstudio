<?php
if( ! function_exists( 'rara_business_register_custom_controls' ) ) :
/**
 * Register Custom Controls
*/
function rara_business_register_custom_controls( $wp_customize ){
    
    // Load our custom control.
    require_once get_template_directory() . '/inc/custom-controls/note/class-note-control.php';
    require_once get_template_directory() . '/inc/custom-controls/radioimg/class-radio-image-control.php';
    require_once get_template_directory() . '/inc/custom-controls/select/class-select-control.php';
    require_once get_template_directory() . '/inc/custom-controls/slider/class-slider-control.php';
    require_once get_template_directory() . '/inc/custom-controls/toggle/class-toggle-control.php';
    require_once get_template_directory() . '/inc/custom-controls/repeater/class-repeater-setting.php';
    require_once get_template_directory() . '/inc/custom-controls/repeater/class-control-repeater.php';
            
    // Register the control type.
    $wp_customize->register_control_type( 'Rara_Business_Radio_Image_Control' );
    $wp_customize->register_control_type( 'Rara_Business_Select_Control' );
    $wp_customize->register_control_type( 'Rara_Business_Slider_Control' );
    $wp_customize->register_control_type( 'Rara_Business_Toggle_Control' );
}
endif;
add_action( 'customize_register', 'rara_business_register_custom_controls' );