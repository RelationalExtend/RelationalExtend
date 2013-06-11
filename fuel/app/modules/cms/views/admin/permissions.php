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
                <th style="width:20%;">Permission</th>
                <th style="width:20%;">Slug</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
<?php foreach($permissions as $permission) { ?>
            <tr>
                <td><?php echo($permission->permission); ?></td>
                <td><?php echo($permission->permission_slug); ?></td>
                <td>
                	<form action="<?php echo(Uri::base().$controller_path."updatepermission/".$permission->id); ?>" method="post" class="form-inline">
                		<input type="text" placeholder="New name..." name="new_name" />
                		<button type="submit" class="btn">Change name</button>
                	</form>
            	</td>
                <td><?php echo(AdminHelpers::bootstrap_buttons(array($permission->delete_button)))?></td>
            </tr>
<?php } ?>
        </tbody>
    </table>

    <hr/>
    
    <form action="<?php echo(Uri::base().$controller_path."createpermission"); ?>" method="post">
	    <fieldset>
		    <legend>Create a new permission</legend>
		    <label>Permission name</label>
		    <input type="text" placeholder="Permission name..." name="permission_name" />
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