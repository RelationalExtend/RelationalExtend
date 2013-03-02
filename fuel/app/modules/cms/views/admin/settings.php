<div class="container-fluid">
	<div class="row-fluid">
		<div class="span3">
          	<div class="well sidebar-nav">
          		
          		<?php echo($settings_sidebar); ?>
          		
          	</div>
        </div>
        
        <div class="span9">
          	<h2>Edit settings</h2>
          	<hr/>
          	<p>Edit site and extension settings</p>
          	
          	<?php echo($settings_editor); ?>
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