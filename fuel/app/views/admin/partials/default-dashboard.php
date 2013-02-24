<div class="container">
	
	<!-- Extensions -->
	
    <h1>Active extensions</h1>
    <hr/>
	<table class="table table-bordered">
		<tr>
			<td style="width:80%">
				<strong>Extension</strong>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
<?php
	foreach($extensions as $extension) {
?>
			<tr>
				<td><?php echo($extension->extension_name); ?></td>
				<td><?php echo($extension->buttons); ?></td>
			</tr>
<?php
	}
?>
	</table>

	<!-- Themes -->
	
    <h1>Installed themes</h1>
    <hr/>
    <table class="table table-bordered">
		<tr>
			<td>
				<strong>Theme</strong>
			</td>
		</tr>
<?php
	foreach($themes as $theme) {
?>
			<tr>
				<td><?php echo($theme->theme_name); ?></td>
			</tr>
<?php
	}
?>
	</table>
</div>
 
