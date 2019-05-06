<?php
/*
 * @var $options array contains all the options the current block we're ediging contains
 * @var $controls NewsletterControls 
 */
?>

<table class="form-table">
    <tr>
        <th><?php _e('Label and link', 'newsletter') ?></th>
        <td>
            <?php $controls->text('text', 70) ?>
            <br>
            <?php $controls->text('url', 70, 'https://...') ?>
        </td>
    </tr>
    <tr>
        <th></th>
        <td>
            <table class="tnp-button-colors">
                <tr>
                    <td>
                        <?php _e('Background', 'newsletter') ?><br>
                        <?php $controls->color('background') ?>
                    </td>
                    <td>
                        <?php _e('Label', 'newsletter') ?><br>
                        <?php $controls->color('color') ?>
                    </td>
                </tr>
            </table>
            <?php $controls->css_font('font') ?>
        </td>
    </tr>

    <tr>
        <th><?php _e('Width', 'newsletter') ?></th>
        <td>
            <?php $controls->text('width') ?>px
        </td>
    </tr>

    <tr>
        <th><?php _e('Block background', 'newsletter') ?></th>
        <td>
            <?php $controls->color('block_background') ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Block padding', 'newsletter') ?></th>
        <td>
            <table class="tnp-button-colors">
                <tr>
                    <td>
                        Top<br>
                        <?php $controls->text('block_padding_top', 4) ?> 
                    </td>
                    <td>
                        Bottom<br>
                        <?php $controls->text('block_padding_bottom', 4) ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
