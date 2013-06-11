<ul class="nav nav-list">
	<li class="nav-header">Users</li>	
<?php
	foreach($users as $user)
	{
?>
		<li><a href="<?php echo($controller_path."userroles/".$user->id); ?>"><?php echo($user->username); ?></a></li>
<?php
	}
?>
</ul>