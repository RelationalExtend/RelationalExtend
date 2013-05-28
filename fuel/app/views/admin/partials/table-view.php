<div class="container">
	
    <h1><?php echo($page_title); ?></h1>
    <hr/>
    <p><?php echo($page_title_content); ?></p>

    <table class="table">
        <thead>
            <tr>
                <?php if(isset($table_rows[0]->description_field)) { ?>
                	<th style="width: 40%;">Rows</th>
            	<?php } ?>
            	<?php foreach($additional_fields as $additional_field_key => $additional_field_value) { ?>
            		<?php if(isset($column_titles[$additional_field_value])) { ?>
            			<th><?php echo($column_titles[$additional_field_value]); ?></th>
        			<?php } else { ?>
            			<th><?php echo($additional_field_value); ?></th>
            		<?php } ?>
        		<?php } ?>
        		<th>Actions</th>
            </tr>
        </thead>
        <tbody>
<?php foreach($table_rows as $table_row) { ?>
            <tr>
                <?php if(isset($table_row->description_field)) { ?>
            		<td><?php echo($table_row->description_field); ?></td>
        		<?php } ?>                
                <?php foreach($additional_fields as $additional_field_key => $additional_field_value) { ?>
            		<td><?php echo($table_row->$additional_field_value); ?></td>
        		<?php } ?>
        		<?php if (isset($table_row->buttons)) { ?>
                	<td><?php echo(AdminHelpers::bootstrap_buttons($table_row->buttons)); ?></td>
                <?php } else { ?>
                	<td>No actions</td>
            	<?php } ?>
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