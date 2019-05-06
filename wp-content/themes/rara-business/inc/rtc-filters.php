<?php
/**
 * Filter to modify functionality of RTC plugin.
 *
 * @package Rara_Business
 */

if( ! function_exists( 'rara_business_cta_section_bgcolor_filter' ) ){
	/**
	 * Filter to add bg color of cta section widget
	 */    
	function rara_business_cta_section_bgcolor_filter(){
		return '#0aa3f3';
	}
}
add_filter( 'rrtc_cta_bg_color', 'rara_business_cta_section_bgcolor_filter' );

if( ! function_exists( 'rara_business_cta_btn_alignment_filter' ) ){
	/**
	 * Filter to add btn alignment of cta section widget
	 */    
	function rara_business_cta_btn_alignment_filter(){
		return 'centered';
	}
}
add_filter( 'rrtc_cta_btn_alignment', 'rara_business_cta_btn_alignment_filter' );

if( ! function_exists( 'rara_business_team_member_image_size' ) ){
	/**
	 * Filter to define image size in team member section widget
	 */    
	function rara_business_team_member_image_size(){
		return 'rara-business-team';
	}
}
add_filter( 'tmw_icon_img_size', 'rara_business_team_member_image_size' );

if( ! function_exists( 'rara_business_modify_testimonial_widget' ) ){
	/**
	 * Filter to add modify testimonial widget
	 */    
	function rara_business_modify_testimonial_widget( $html, $args, $instance ){
		$obj         = new RaraTheme_Companion_Functions();
        $name        = ! empty( $instance['name'] ) ? $instance['name'] : '' ;        
        $designation = ! empty( $instance['designation'] ) ? $instance['designation'] : '' ;        
        $testimonial = ! empty( $instance['testimonial'] ) ? $instance['testimonial'] : '';
        $image       = ! empty( $instance['image'] ) ? $instance['image'] : '';

        if( $image )
        {
             $attachment_id = $image;
             $icon_img_size = apply_filters('icon_img_size','rttk-thumb');
        }
        
        ob_start(); 
        ?>
        
            <div class="rtc-testimonial-holder">
                <div class="rtc-testimonial-inner-holder">
                    <div class="text-holder">
                    	<?php if( $image ){ ?>
	                        <div class="img-holder">
	                            <?php echo wp_get_attachment_image( $attachment_id, $icon_img_size, false, 
	                                        array( 'alt' => esc_attr( $name ))) ;?>
	                        </div>
                    	<?php }?>
                        <div class="testimonial-meta">
                           <?php 
                                if( $name ) { echo '<span class="name">'.$name.'</span>'; }
                                if( isset( $designation ) && $designation!='' ){
                                    echo '<span class="designation">'.esc_attr($designation).'</span>';
                                }
                            ?>
                        </div>                              
                    </div>
                    <?php if( $testimonial ) echo '<div class="testimonial-content">'.wpautop( wp_kses_post( $testimonial ) ).'</div>'; ?>
                </div>
            </div>
        <?php 
        $html = ob_get_clean();
        return $html;   
	}
}
add_filter( 'raratheme_companion_testimonial_widget_filter', 'rara_business_modify_testimonial_widget', 10, 3 );