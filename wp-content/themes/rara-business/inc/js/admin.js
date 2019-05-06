jQuery(document).ready(function($){

	function check_page_templates(){
        $('.inside #page_template').each(function(i,e){
            if( $(this).val() === "templates/portfolio.php" ){
                $('#rara_business_sidebar_layout').hide();
            }else{
                $('#rara_business_sidebar_layout').show();
            }
        });
    }
    $('.inside #page_template').on( 'change', check_page_templates );
    
    // Hide metabox options when static front page is set
    if( rb_show_metabox.hide_metabox == '1' ){
        $('#rara_business_sidebar_layout').hide();
    }
} )