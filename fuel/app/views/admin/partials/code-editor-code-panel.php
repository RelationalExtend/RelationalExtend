<div style="padding:20px;">
	<?php echo(Asset::css("codemirror.css")); ?>
	<?php echo(Asset::js("codemirror/lib/codemirror.js")); ?>
	<?php echo(Asset::js("codemirror/mode/$editor_language/$editor_language.js")); ?>
	
	<style type="text/css">
  		.CodeMirror {border-top: 1px solid black; border-bottom: 1px solid black;}
    </style>
	
	<form>
		<textarea id="code" name="code"><?php echo($editor_code); ?></textarea>
	</form>
		
	<script>
		var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
		  lineNumbers: true,
		  mode: "<?php echo($editor_language);?>"
		});
	</script>
</div>