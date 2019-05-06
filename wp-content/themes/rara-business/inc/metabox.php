<?php 
/**
* Metabox for Sidebar Layout
*
* @package Rara_Business
*
*/ 

function rara_business_add_sidebar_layout_box(){
    $screens = array( 'post', 'page' );
    foreach( $screens as $screen ){
        add_meta_box( 
            'rara_business_sidebar_layout',
            __( 'Sidebar Layout', 'rara-business' ),
            'rara_business_sidebar_layout_callback', 
            $screen,
            'normal',
            'high'
        );
    }
}
add_action( 'add_meta_boxes', 'rara_business_add_sidebar_layout_box' );

$sidebar_layout = array(    
    'default-sidebar'=> array(
    	 'value'     => 'default-sidebar',
    	 'label'     => __( 'Default Sidebar', 'rara-business' ),
    	 'thumbnail' => get_template_directory_uri() . '/images/default-sidebar.png'
   	),
    'no-sidebar'     => array(
    	 'value'     => 'no-sidebar',
    	 'label'     => __( 'Full Width', 'rara-business' ),
    	 'thumbnail' => get_template_directory_uri() . '/images/no-sidebar.png'
   	),    
    'left-sidebar' => array(
         'value'     => 'left-sidebar',
    	 'label'     => __( 'Left Sidebar', 'rara-business' ),
    	 'thumbnail' => get_template_directory_uri() . '/images/left-sidebar.png'         
    ),
    'right-sidebar' => array(
         'value'     => 'right-sidebar',
    	 'label'     => __( 'Right Sidebar', 'rara-business' ),
    	 'thumbnail' => get_template_directory_uri() . '/images/right-sidebar.png'         
     )    
);

function rara_business_sidebar_layout_callback(){
    global $post , $sidebar_layout;
    wp_nonce_field( basename( __FILE__ ), 'rara_business_nonce' );
?>
 
<table class="form-table">
    <tr>
        <td colspan="4"><em class="f13"><?php esc_html_e( 'Choose Sidebar Template', 'rara-business' ); ?></em></td>
    </tr>

    <tr>
        <td>
        <?php  
            foreach( $sidebar_layout as $field ){  
                $layout = get_post_meta( $post->ID, 'sidebar_layout', true ); ?>

            <div class="radio-image-wrapper" style="float:left; margin-right:30px;">
                <label class="description">
                    <span><img src="<?php echo esc_url( $field['thumbnail'] ); ?>" alt="<?php echo esc_attr( $field['label'] ); ?>" /></span><br/>
                    <input type="radio" name="sidebar_layout" value="<?php echo esc_attr( $field['value'] ); ?>" <?php checked( $field['value'], $layout ); if( empty( $layout ) ){ checked( $field['value'], 'default-sidebar' ); }?>/>&nbsp;<?php echo esc_html( $field['label'] ); ?>
                </label>
            </div>
            <?php } // end foreach 
            ?>
            <div class="clear"></div>
        </td>
    </tr>
</table>
 
<?php 
}

function rara_business_savesidebar_layout( $post_id ){
      global $sidebar_layout , $post;

    // Verify the nonce before proceeding.
    if ( !isset( $_POST[ 'rara_business_nonce' ] ) || !wp_verify_nonce( $_POST[ 'rara_business_nonce' ], basename( __FILE__ ) ) )
        return;
    
    // Stop WP from clearing custom fields on autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)  
        return;

    if ('page' == $_POST['post_type']) {  
        if (!current_user_can( 'edit_page', $post_id ) )  
            return $post_id;  
    } elseif (!current_user_can( 'edit_post', $post_id ) ) {  
            return $post_id;  
    }

    foreach( $sidebar_layout as $field ){  
        //Execute this saving function
        $old = get_post_meta( $post_id, 'sidebar_layout', true ); 
        $new = sanitize_text_field( $_POST['sidebar_layout'] );
        if( $new && $new != $old ) {  
            update_post_meta( $post_id, 'sidebar_layout', $new );  
        }elseif( '' == $new && $old ) {  
            delete_post_meta( $post_id, 'sidebar_layout', $old );  
        } 
    } // end foreach     
}
add_action( 'save_post' , 'rara_business_savesidebar_layout' );