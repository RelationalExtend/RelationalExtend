<div style="padding:20px;">
	<?php echo(Asset::css("codemirror.css")); ?>
	<?php echo(Asset::js("codemirror/lib/codemirror.js")); ?>
	<?php echo(Asset::js("codemirror/mode/$editor_language/$editor_language.js")); ?>
	
	<style type="text/css">
  		.CodeMirror {border-top: 1px solid black; border-bottom: 1px solid black;}
    </style>

<?php
    if($confirm == 1)
    {
?>
	<div class="alert">Save successful</div>
<?php
    }
?>
	
	<form action="<?php echo(Uri::base().$controller_path."savethemecomponent/$theme_id/$theme_component/$component_id"); ?>" method="post">
		<textarea id="code" name="code"><?php echo($editor_code); ?></textarea>
		<input class="btn" type="submit" value="Save" style="margin-top:20px;" />
	</form>
		
	<script>
		var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
		  lineNumbers: true,
		  mode: "<?php echo($editor_language);?>"
		});
	</script>
</div>