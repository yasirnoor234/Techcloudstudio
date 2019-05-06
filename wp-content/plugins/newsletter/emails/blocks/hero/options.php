<?php
/*
 * @var $options array contains all the options the current block we're ediging contains
 * @var $controls NewsletterControls 
 */
?>

<table class="form-table">
    <tr>
        <th><?php _e('Layout', 'newsletter') ?></th>
        <td>
            <?php $controls->select('layout', array('full' => 'Full', 'left' => 'Left')) ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Image', 'newsletter') ?></th>
        <td>
            <?php $controls->media('image') ?>
        </td>
    </tr>

    <tr>
        <th>Title and Text</th>
        <td>
            <?php $controls->text('title', 70) ?>
            <?php $controls->css_font('title_font') ?>
            <br><br>
            <?php $controls->textarea('text') ?>
            <?php $controls->css_font('font') ?>
        </td>
    </tr>
    <tr>
        <th><?php _e('Button', 'newsletter') ?></th>
        <td>


            <?php $controls->text('button_label', 12, 'Button label...') ?>
            
            <?php $controls->text('url', 40, 'https://...') ?>

            <table class="tnp-button-colors">
                <tr>
                    <td>
                        <?php _e('Background', 'newsletter') ?><br>
                        <?php $controls->color('button_background') ?>
                    </td>
                    <td>
                        <?php _e('Label', 'newsletter')?><br>
                        <?php $controls->color('button_color') ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <th><?php _e('Block background', 'newsletter') ?></th>
        <td>
            <?php $controls->color('block_background') ?>
        </td>
    </tr>
</table>

