<ul class="nav nav-list">
	<li class="nav-header">Roles</li>	
<?php
	foreach($roles as $role)
	{
?>
		<li><a href="<?php echo($controller_path."rolespermissions/".$role->id); ?>"><?php echo($role->role); ?></a></li>
<?php
	}
?>
</ul>