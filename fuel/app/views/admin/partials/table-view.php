<?php
	if($bulk_actions_enabled) {
?>
	<script type="text/javascript">
		function checkAll(bx) {
			var cbs = document.getElementsByTagName('input');
			
			for(var i=0; i < cbs.length; i++) 
			{
				if(cbs[i].type == 'checkbox' && cbs[i].name == 'chk_ids[]') 
				{
			    	cbs[i].checked = bx.checked;
			    }
			}
		}
	</script>
<?php
	}
?>

<div class="container">
	
    <h1><?php echo($page_title); ?></h1>
    <hr/>
    <p><?php echo($page_title_content); ?></p>

	<form action = "<?php echo($controller_path."bulkactions"); ?>" method = "post">
		<input type="hidden" name="return_path" value="<?php echo($return_path); ?>" />
                <input type="hidden" name="object" value ="<?php echo($object); ?>" />
	    <table class="table">
	        <thead>
	            <tr>
	            	<?php if($bulk_actions_enabled) { ?>
	            		<th><input type="checkbox" onclick="checkAll(this);" /></th>
	            	<?php } ?>
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
	            	<?php if($bulk_actions_enabled) { ?>
	            		<td><input type="checkbox" name="chk_ids[]" value="<?php echo($table_row->id_field); ?>" /></td>
	            	<?php } ?>
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
<?php if($bulk_actions_enabled) { ?>
	    	<tr>
	    		<td><?php echo($bulk_actions);?></td>
	    	</tr>
<?php } ?>
	        <tr>
	            <td><?php echo(AdminHelpers::bootstrap_buttons($bottom_buttons)); ?></td>
	        </tr>
	    </table>
    
    </form>

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