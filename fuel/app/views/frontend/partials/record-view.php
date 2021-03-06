<div class="<?php echo($form_class); ?>">
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
<?php
	foreach($buttons as $button)
	{
?>
                    <input type="submit" value="<?php echo($button); ?>" name="submit" class="btn" style="margin-right: 5px;"/>
<?php
	}
?>
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