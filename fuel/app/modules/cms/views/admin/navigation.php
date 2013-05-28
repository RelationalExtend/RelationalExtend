<div class="container">
    <h1>Navigation</h1>
    <hr/>
    <p>Manage the site's navigation</p>
    
    
<?php
	if($confirm == 1) {
?>
	<div class="alert">Save successful</div>
<?php
	}
	else if($confirm == 2) {
?>
	<div class="alert">Delete successful</div>
<?php
	}
?>
    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
            </tr>
        </thead>
        <tbody>
<?php foreach($navigation_items as $navigation_item) { ?>
            <tr>
                <td>
                	<form action="<?php echo(Uri::base().$controller_path."updatenavigation/".$navigation_item->{Navigation::NAV_ITEM_ID}); ?>"
						method="post" class="form-inline">
						<input type="text" name="text" placeholder="Navigation text..." style="width:15%;" value="<?php echo($navigation_item->{Navigation::NAV_ITEM_TEXT}); ?>"/>
						<span style="padding:15px;">
							<span><input type="radio" name="type" value="<?php echo(Navigation::NAV_PAGE); ?>" <?php echo($navigation_item->{Navigation::NAV_ITEM_TYPE} == Navigation::NAV_PAGE ? "checked" : "") ?> />&nbsp;Page&nbsp;</span>
							<select name="page_object_id" style="width:15%;">
<?php
	foreach($page_items as $page_item)
	{
?>
							<option value="<?php echo($page_item["id"]); ?>" <?php if($navigation_item->{Navigation::NAV_ITEM_TYPE} == Navigation::NAV_PAGE) { echo(($navigation_item->{Navigation::NAV_ITEM_OBJECT_ID} == $page_item["id"]) ? " selected" : ""); } ?>><?php echo($page_item["page_title"]); ?></option>
<?php
	}
?>                			
							</select>
						</span>
						<span style="padding:15px;">
							<span><input type="radio" name="type" value="<?php echo(Navigation::NAV_MODULE); ?>" <?php echo($navigation_item->{Navigation::NAV_ITEM_TYPE} == Navigation::NAV_MODULE ? "checked" : "") ?>>&nbsp;Extension&nbsp;</span>
							<select name="extension_object_id" style="width:15%;">
<?php
	foreach($extension_items as $extension_item)
	{
?>
								<option value="<?php echo($extension_item->extension_id); ?>" <?php if($navigation_item->{Navigation::NAV_ITEM_TYPE} == Navigation::NAV_MODULE) { echo(($navigation_item->{Navigation::NAV_ITEM_OBJECT_ID} == $extension_item->extension_id) ? " selected" : ""); } ?>><?php echo($extension_item->extension_name); ?></option>
<?php
	}
?>                			
							</select>
						</span>	
						<input type="submit" class="btn" value="Update" style="margin-left:10px;"/>
						<a href="<?php echo(Uri::base().$controller_path."deletenavigation/".$navigation_item->{Navigation::NAV_ITEM_ID}); ?>" class="btn">Delete</a>
					</form>
	            </td>
	        </tr>
<?php } ?>
        </tbody>
    </table>
    
    <h2>Add a new navigation item</h2>
	<hr/>
	
	<!-- After -->
	
	<form action="<?php echo(Uri::base().$controller_path."addnavigationafter"); ?>"
		method="post" class="form-inline">
		<input type="text" name="text" placeholder="Navigation text..." style="width:15%;"/>
		<span style="padding:15px;">
			<span><input type="radio" name="type" value="<?php echo(Navigation::NAV_PAGE); ?>" checked />&nbsp;Page&nbsp;</span>
			<select name="page_object_id" style="width:15%;">
<?php
	foreach($page_items as $page_item)
	{
?>
				<option value="<?php echo($page_item["id"]); ?>"><?php echo($page_item["page_title"]); ?></option>
<?php
	}
?>                			
			</select>
		</span>
		<span style="padding:15px;">
			<span><input type="radio" name="type" value="<?php echo(Navigation::NAV_MODULE); ?>">&nbsp;Extension&nbsp;</span>
			<select name="extension_object_id" style="width:15%;">
<?php
	foreach($extension_items as $extension_item)
	{
?>
				<option value="<?php echo($extension_item->extension_id); ?>"><?php echo($extension_item->extension_name); ?></option>
<?php
	}
?>                			
			</select>
		</span>
		<span>After&nbsp;</span>
		<select name="after_navigation_id" style="width:15%;">
			<option value="0">--New item--</option>
<?php
	foreach($navigation_items as $navigation_item) {
?>
			<option value="<?php echo($navigation_item->{Navigation::NAV_ITEM_ID}); ?>"><?php echo($navigation_item->{Navigation::NAV_ITEM_TEXT}); ?></option>
<?php
	}
?>
	
		</select>	
		<input type="submit" class="btn" value="Add" style="margin-left:10px;"/>
	</form>
	
	<!-- Before -->
	
	<form action="<?php echo(Uri::base().$controller_path."addnavigationbefore"); ?>"
		method="post" class="form-inline">
		<input type="text" name="text" placeholder="Navigation text..." style="width:15%;"/>
		<span style="padding:15px;">
			<span><input type="radio" name="type" value="<?php echo(Navigation::NAV_PAGE); ?>" checked />&nbsp;Page&nbsp;</span>
			<select name="page_object_id" style="width:15%;">
<?php
	foreach($page_items as $page_item)
	{
?>
				<option value="<?php echo($page_item["id"]); ?>"><?php echo($page_item["page_title"]); ?></option>
<?php
	}
?>                			
			</select>
		</span>
		<span style="padding:15px;">
			<span><input type="radio" name="type" value="<?php echo(Navigation::NAV_MODULE); ?>">&nbsp;Extension&nbsp;</span>
			<select name="extension_object_id" style="width:15%;">
<?php
	foreach($extension_items as $extension_item)
	{
?>
				<option value="<?php echo($extension_item->extension_id); ?>"><?php echo($extension_item->extension_name); ?></option>
<?php
	}
?>                			
			</select>
		</span>
		<span>Before&nbsp;</span>
		<select name="before_navigation_id" style="width:15%;">
			<option value="0">--New item--</option>
<?php
	foreach($navigation_items as $navigation_item) {
?>
			<option value="<?php echo($navigation_item->{Navigation::NAV_ITEM_ID}); ?>"><?php echo($navigation_item->{Navigation::NAV_ITEM_TEXT}); ?></option>
<?php
	}
?>
	
		</select>	
		<input type="submit" class="btn" value="Add" style="margin-left:10px;"/>
	</form>

    <hr/>

    <footer>
        <p class="pull-right">Page rendered in {exec_time}s using {mem_usage}mb of memory.</p>
        <p>
            <a href="http://fuelphp.com">FuelPHP</a> is released under the MIT license.<br>
            <small>Version: <?php echo Fuel::VERSION; ?></small>
        </p>
    </footer>

</div>