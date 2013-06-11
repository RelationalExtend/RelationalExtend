<div style="padding:20px; padding-top:0px;">
	<p><strong>User:&nbsp;</strong><?php echo($user->username); ?>&nbsp;(<?php echo($user->email); ?>)</p>
	<form action = "<?php echo($assign_roles_url); ?>" method="post">
		<input type="hidden" name="user_id" value="<?php echo($user->id); ?>"/>
		<table class="table table-bordered">
			<tr>
				<th style="width: 20px;">&nbsp;</th>
				<th>Role</th>
			</tr>
<?php foreach($roles as $role) { ?>
			<tr>
				<th><input type='checkbox' name='chkroles[]' value='<?php echo($role->id); ?>' <?php echo(in_array($role->id, $role_ids) ? 'checked' : ''); ?> /></th>
				<th><?php echo($role->role); ?></th>
			</tr>
<?php } ?>
			<tr>
				<td colspan="2" style="text-align:right;">
					<input type="submit" class="btn" value="Save roles"/>
				</td>
			</tr>
		</table>
	</form>
</div>