<?php include NEWSLETTER_INCLUDES_DIR . '/codemirror.php'; ?>
<style>
    .CodeMirror {
        height: 600px;
    }
</style>
<script>
    var templateEditor;
    jQuery(function () {
        templateEditor = CodeMirror.fromTextArea(document.getElementById("options-message"), {
            lineNumbers: true,
            mode: 'htmlmixed',
            lineWrapping: true,
            extraKeys: {"Ctrl-Space": "autocomplete"}
        });
    });
</script>

<script>
    function tnp_media(name) {
        var tnp_uploader = wp.media({
            title: "Select an image",
            button: {
                text: "Select"
            },
            frame: 'post',
            multiple: false,
            displaySetting: true,
            displayUserSettings: true
        }).on("insert", function () {
            wp.media;
            var media = tnp_uploader.state().get("selection").first();
            if (media.attributes.url.indexOf("http") !== 0)
                media.attributes.url = "http:" + media.attributes.url;

            if (!media.attributes.mime.startsWith("image")) {

                templateEditor.getDoc().replaceRange(url, templateEditor.getDoc().getCursor());

            } else {
                var display = tnp_uploader.state().display(media);
                var url = media.attributes.sizes[display.attributes.size].url;

                templateEditor.getDoc().replaceRange('<img src="' + url + '">', templateEditor.getDoc().getCursor());

            }
        }).open();
    }

</script>

<input class="button-primary" type="button" onclick="newsletter_textarea_preview('options-message'); return false;" value="Switch editor/preview">

<input type="button" class="button-primary" value="Add media" onclick="tnp_media()">

<a href="https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-tags" target="_blank"><?php _e('Available tags', 'newsletter') ?></a>

<br><br>
<?php $controls->textarea_preview('message', '100%', 700, '', '', false); ?>