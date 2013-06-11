<div class="container">
    <h1><?php echo($page_title); ?></h1>
    <hr/>
    <p><?php echo($page_title_content); ?></p>
    
<?php foreach($messages as $message) { ?>
	<div class="alert"><?php echo($message); ?></div>
<?php } ?>

    <table class="table">
        <thead>
            <tr>
                <th style="width:20%;">Role</th>
                <th style="width:20%;">Slug</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
<?php foreach($roles as $role) { ?>
            <tr>
                <td><?php echo($role->role); ?></td>
                <td><?php echo($role->role_slug); ?></td>
                <td>
                	<form action="<?php echo(Uri::base().$controller_path."updatepassword/".$user->username); ?>" method="post" class="form-inline">
                		<input type="text" placeholder="New name..." name="new_name" />
                		<button type="submit" class="btn">Change name</button>
                	</form>
            	</td>
                <td><?php echo(isset($user->delete_button) ? AdminHelpers::bootstrap_buttons(array($user->delete_button)) : "&nbsp;")?></td>
            </tr>
<?php } ?>
        </tbody>
    </table>

    <hr/>
    
    <form action="<?php echo(Uri::base().$controller_path."createuser"); ?>" method="post">
	    <fieldset>
		    <legend>Create a new user</legend>
		    <label>User name</label>
		    <input type="text" placeholder="User name..." name="user_name" />
		    <label>Email address</label>
		    <input type="text" placeholder="Email address..." name="email_address" />
		    <label>Password</label>
		    <input type="password" placeholder="Password..." name="password" />
		    <label>Group</label>
		    <select name="user_group">
		    	<option value="100">Super Admin</option>
		    	<option value="101">Developer</option>
		    	<option value="102">Content Manager</option>
		    </select>
		    <p><button type="submit" class="btn">Create</button></p>
	    </fieldset>
    </form>

    <footer>
        <p class="pull-right">Page rendered in {exec_time}s using {mem_usage}mb of memory.</p>
        <p>
            <a href="http://fuelphp.com">FuelPHP</a> is released under the MIT license.<br>
            <small>Version: <?php echo Fuel::VERSION; ?></small>
        </p>
    </footer>

</div>