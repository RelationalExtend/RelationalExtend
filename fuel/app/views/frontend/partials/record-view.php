<div class="<?php echo($div_container_class); ?>">
    <div class="<?php echo($div_content_class); ?>">
        <h1><?php echo($page_title); ?></h1>
        <p><?php echo($page_title_content); ?></p>
    </div>
    <div class="<?php echo($div_form_class); ?>">
        <form action="<?php echo($form_action); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="record_id" value="<?php echo($record_id); ?>"/>
            <input type="hidden" name="object" value="<?php echo($object); ?>"/>
            <input type="hidden" name="return_path" value="<?php echo($return_path); ?>"/>
<?php

    $num_rows = count($page_rows);
    $current_row = 1;

    foreach($page_rows as $page_row)
    {

        $current_row ++;

        if($page_row->object_meta_control != DBFieldMeta::CONTROL_HIDDEN) {
?>

            <?php echo(FrontendHelpers::record_label($page_row)); ?>
            <?php echo(FrontEndHelpers::record_control($page_row, $record, (isset($form_values[$page_row->object_meta_slug]) ? $form_values[$page_row->object_meta_slug] : null))); ?>
<?php
        }
        else {
?>
            <p><?php echo(FrontEndHelpers::record_control($page_row, $record)); ?></p>
<?php
        }
    }
?>
            <p style="text-align: right;">
                <input type="submit" value="<?php echo(AdminHelpers::save_button_value()); ?>"
                    name="<?php echo(FrontEndHelpers::submit_button_value()); ?>" class="btn" style="margin-right: 5px;"/>
            </p>
        </form>
    </div>
</div>

