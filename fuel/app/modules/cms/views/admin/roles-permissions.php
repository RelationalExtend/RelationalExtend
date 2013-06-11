<div class="container-fluid">
	<div class="row-fluid">
		<div class="span3">
          	<div class="well sidebar-nav">
          		
          		<?php echo($roles_permissions_sidebar); ?>
          		
          	</div>
        </div>
        
        <div class="span9">
          	<h2>Edit role permissions</h2>
          	<hr/>
          	<p>Assign permissions to roles</p>
          	
          	<?php echo($permissions_editor); ?>
      	</div>
    </div>
	
	<hr />

	<footer>
        <p class="pull-right">Page rendered in {exec_time}s using {mem_usage}mb of memory.</p>
        <p>
            <a href="http://fuelphp.com">FuelPHP</a> is released under the MIT license.<br>
            <small>Version: <?php echo Fuel::VERSION; ?></small>
        </p>
    </footer>

</div>