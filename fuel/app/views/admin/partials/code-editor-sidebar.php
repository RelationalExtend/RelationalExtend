<ul class="nav nav-list">
	<li class="nav-header">Layouts</li>	
<?php
	foreach($layouts as $layout)
	{
?>
	<li><a href="<?php echo($controller_path."managetheme/$theme_id/".CMSTheme::LAYOUT_PREFIX."/".$layout->theme_layout_id); ?>"><?php echo($layout->theme_layout_name); ?></a></li>
<?php
	}
?>
  <li class="nav-header">Partials</li>
<?php
	foreach($partials as $partial)
	{
?>
	<li><a href="<?php echo($controller_path."managetheme/$theme_id/".CMSTheme::PARTIAL_PREFIX."/".$partial->theme_partial_id); ?>"><?php echo($partial->theme_partial_name); ?></a></li>
<?php
	}
?>
  <li class="nav-header">Javascript</li>
<?php
	foreach($javascripts as $javascript)
	{
?>
	<li><a href="<?php echo($controller_path."managetheme/$theme_id/".CMSTheme::JS_PREFIX."/".$javascript->theme_js_id); ?>"><?php echo($javascript->theme_js_name); ?></a></li>
<?php
	}
?>
  <li class="nav-header">Stylesheets</li>
<?php
	foreach($stylesheets as $stylesheet)
	{
?>
	<li><a href="<?php echo($controller_path."managetheme/$theme_id/".CMSTheme::CSS_PREFIX."/".$stylesheet->theme_css_id); ?>"><?php echo($stylesheet->theme_css_name); ?></a></li>
<?php
	}
?>
</ul>