<ul class="nav nav-list">
	<li class="nav-header">Extensions</li>	
<?php
	foreach($extensions as $extension)
	{
?>
	<li><a href="<?php echo($controller_path."settings/".$extension->extension_id); ?>"><?php echo($extension->extension_name); ?></a></li>
<?php
	}
?>
</ul>