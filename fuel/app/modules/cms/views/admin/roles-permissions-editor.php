<div style="padding:20px; padding-top:0px;">
	<p><strong>Role:&nbsp;</strong><?php echo($role->role); ?></p>
	<form action = "<?php echo($assign_permissions_url); ?>" method="post">
		<input type="hidden" name="role_id" value="<?php echo($role->id); ?>"/>
		<table class="table table-bordered">
			<tr>
				<th style="width: 20px;">&nbsp;</th>
				<th>Permission</th>
			</tr>
<?php foreach($permissions as $permission) { ?>
			<tr>
				<th><input type='checkbox' name='chkpermissions[]' value='<?php echo($permission->id); ?>' <?php echo(in_array($permission->id, $permission_ids) ? 'checked' : ''); ?> /></th>
				<th><?php echo($permission->permission); ?></th>
			</tr>
<?php } ?>
			<tr>
				<td colspan="2" style="text-align:right;">
					<input type="submit" class="btn" value="Save permissions"/>
				</td>
			</tr>
		</table>
	</form>
</div>