<div class="container">
    <h1><?php echo($page_title); ?></h1>
    <hr/>
    <p><?php echo($page_title_content); ?></p>
    
    <div class="row">
        <div class="span12">
            <form action="<?php echo($form_action); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="record_id" value="<?php echo($record_id); ?>"/>
                <input type="hidden" name="object" value="<?php echo($object); ?>"/>
                <input type="hidden" name="return_path" value="<?php echo($return_path); ?>"/>
                
<?php 
    if(isset($form_view->additional_form_hidden_fields))
    {
        foreach($form_view->additional_form_hidden_fields as $additional_form_hidden_field_key => $additional_form_hidden_field_value)
        {
?>
                <input type="hidden" name="<?php echo($additional_form_hidden_field_key); ?>" value="<?php echo($additional_form_hidden_field_value); ?>"/>
<?php
        }
    }
?>
<?php

    $num_rows = count($page_rows);
    $current_row = 1;

    foreach($page_rows as $page_row)
    {

        $current_row ++;

        if($page_row->object_meta_control != DBFieldMeta::CONTROL_HIDDEN) {
?>

            <?php echo(AdminHelpers::record_label($page_row)); ?>
            <?php echo(AdminHelpers::record_control($page_row, $record, (isset($form_values[$page_row->object_meta_slug]) ? $form_values[$page_row->object_meta_slug] : null))); ?>
<?php
        }
        else {
?>
                <p><?php echo(AdminHelpers::record_control($page_row, $record)); ?></p>
<?php
        }
    }
?>
                <p style="text-align: right;">
                    <input type="submit" value="<?php echo(AdminHelpers::save_button_value()); ?>"
                        name="<?php echo(AdminHelpers::save_button_value()); ?>" class="btn" style="margin-right: 5px;"/>
                    <input type="submit" value="<?php echo(AdminHelpers::save_and_exit_button_value()); ?>"
                        name="<?php echo(Utility::slugify(AdminHelpers::save_and_exit_button_value())); ?>" class="btn"/>
                </p>
            </form>
        </div>
    </div>

    <hr/>
    <footer>
        <p class="pull-right">Page rendered in {exec_time}s using {mem_usage}mb of memory.</p>
        <p>
            <a href="http://fuelphp.com">FuelPHP</a> is released under the MIT license.<br>
            <small>Version: <?php echo Fuel::VERSION; ?></small>
        </p>
    </footer>
</div>

