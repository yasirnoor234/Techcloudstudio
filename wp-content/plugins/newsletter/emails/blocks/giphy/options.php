<?php
/*
 * @var $options array contains all the options the current block we're ediging contains
 * @var $controls NewsletterControls 
 */
?>

<table class="form-table">
    <tr>
        <th><?php _e('Search Giphy', 'newsletter') ?></th>
        <td>
            <?php $controls->text('q', 40, 'Search a picture...') ?>
            <br>
            <div style="clear: both; max-height: 300px; overflow: scroll" id="tnp-giphy-results"></div>
        </td>
    </tr>
    <tr>
        <th><?php _e('Selected', 'newsletter') ?></th>
        <td>
            <?php $controls->hidden('giphy_url') ?>
            <br>
            <div id="giphy-preview">
                <?php if (!empty($controls->data['giphy_url'])) { ?>
                <img src="<?php echo esc_attr($controls->data['giphy_url'])?>" style="max-width: 300px">
                <?php } ?>
            </div>
        </td>
    </tr>
    <tr>
        <th><?php _e('Background', 'newsletter') ?></th>
        <td>
            <?php $controls->block_background() ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Padding', 'newsletter') ?></th>
        <td>
            <?php $controls->block_padding() ?>
        </td>
    </tr>
</table>

<script type="text/javascript">

    function choose_gif(url) {
        //jQuery("#tnp-giphy-results").html("");
        jQuery("#options-giphy_url").val(url);
        jQuery("#giphy-preview").html('<img src="' + url + '" style="max-width: 300px">');
    }

    jQuery("#options-q").keyup(
            function () {
                if (typeof(tid) != "undefined") {
                    window.clearTimeout(tid);
                }
                tid = window.setTimeout(function () {
                    var rating = "r";
                    var limit = 20;
                    var offset = 0;
                    
                            jQuery.get("https://api.giphy.com/v1/gifs/search", {limit: limit, rating: rating, api_key: "57FLbVJJd7oQBZ0fEiRnzhM2VtZp5OP1", q: jQuery("#options-q").val()}, function (data) {
                                jQuery("#tnp-giphy-results").html("");
                                jQuery.each(data.data, function (index, value) {
                                    jQuery("#tnp-giphy-results").append('<div style="overflow: hidden; width: 100px; height: 100px; float: left; margin: 5px"><img src="' + value.images.fixed_width_small.url + '" onclick="choose_gif(\'' + value.images.fixed_height.url + '\')" style="float:left; max-width: 100%"></div>');
                                });
                            }, "json");
                        }, 500);
            });
jQuery("#options-q").trigger('keyup');

</script>
