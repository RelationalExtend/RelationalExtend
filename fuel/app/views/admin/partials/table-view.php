<div class="container">
	
    <h1><?php echo($page_title); ?></h1>
    <hr/>
    <p><?php echo($page_title_content); ?></p>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 60%;">Rows</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
<?php foreach($table_rows as $table_row) { ?>
            <tr>
                <td><?php echo($table_row->description_field); ?></td>
                <td><?php echo(AdminHelpers::bootstrap_buttons($table_row->buttons)); ?></td>
            </tr>
<?php } ?>
        </tbody>
    </table>

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