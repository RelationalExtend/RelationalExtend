<div style="padding:20px; padding-top:0px;">
	
	<p><strong>Component name:&nbsp;</strong><?php echo($component_name); ?></p>
	<?php if($component_slug != null) { ?><p><strong>Component slug:&nbsp;</strong><?php echo($component_slug); ?></p><?php } ?>
	
	<hr/>

<?php
    if($confirm == 1)
    {
?>
	<div class="alert">Save successful</div>
<?php
    }
?>
	
	<form action="<?php echo(Uri::base().$controller_path."savesettings/$extension_id"); ?>" method="post">
		<table class="table table-bordered">
			<tr>
				<th>Setting</th>
				<th>Value</th>
			</tr>

<?php
	foreach($settings as $setting) {
?>			
			<tr>
				<td><?php echo($setting->name." (".$setting->slug.")"); ?></td>
				<td><input type='text' value='<?php echo($setting->value); ?>' name='setting_<?php echo($setting->id); ?>'/></td>
			</tr>
<?php
	}
?>
		</table>
		<input class="btn" type="submit" value="Save" style="margin-top:20px;" />
	</form>
</div>