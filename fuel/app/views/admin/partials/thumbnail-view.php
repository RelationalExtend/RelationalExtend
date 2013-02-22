<div class="container">
    <div class="hero-unit">
        <h1><?php echo($page_title); ?></h1>
        <p><?php echo($page_title_content); ?></p>
    </div>

    <ul class="thumbnails">
<?php foreach($table_rows as $table_row) { ?>
            <li class="span4">
                <div class="thumbnail">
                    <p><img src='<?php echo($media_path.$table_row->id_field.'_'.$table_row->thumbnail_field); ?>'/></p>
                    <p style="padding:10px;"><?php echo($table_row->description_field); ?></p>
                    <p>Link:</p>
                    <p><textarea rows="3" cols="" style='width:95%;' readonly="readonly"><?php echo($media_path.$table_row->id_field.'_'.$table_row->thumbnail_field); ?></textarea></p>
                    <p style="padding:10px;"><?php echo(AdminHelpers::bootstrap_buttons($table_row->buttons)); ?></p>
                </div>
            </li>
<?php } ?>
    </ul>

    <table class="table">
        <tr>
            <td><?php echo(AdminHelpers::bootstrap_buttons($bottom_buttons)); ?></td>
        </tr>
    </table>

<?php if($pagination_records->pagination_enabled) { ?>

    <table class="table">
        <tr>
            <td><?php echo(AdminHelpers::pagination_links($pagination_records)); ?></td>
        </tr>
    </table>

<?php } ?>

    <footer>
        <p class="pull-right">Page rendered in {exec_time}s using {mem_usage}mb of memory.</p>
        <p>
            <a href="http://fuelphp.com">FuelPHP</a> is released under the MIT license.<br>
            <small>Version: <?php echo Fuel::VERSION; ?></small>
        </p>
    </footer>

</div>