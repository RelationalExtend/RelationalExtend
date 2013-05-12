<!DOCTYPE html>
<html>
<head>
	<?php echo($html_head); ?>
</head>
<body>
	<div id="header">
		<div class="row">
			<div id="logo"></div>
		</div>
	</div>
    <div style="padding-left: 10px; padding-right: 10px;">
        <div class="navbar">
            <div class="navbar-inner">
                <a class="brand" href="#">Relational CMS</a>
                <ul class="nav">
<?php
    foreach($menu_items as $menu_item_key => $menu_item) {
        if(!is_array($menu_item)) {
?>
                    <li<?php echo($menu_item->active); ?>><a href="<?php echo($menu_item->link); ?>"><?php echo($menu_item->text); ?></a></li>
<?php
        }
        else {
?>
                <li>
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown" id="content-menu"><?php echo(ucfirst($menu_item_key)); ?></a>
                    <div class="dropdown">
                        <ul class="dropdown-menu" role="menu" aria-labelledby="content-menu">
<?php
            foreach($menu_item as $menu_item_array_item) {
?>
                    <li<?php echo($menu_item_array_item->active); ?>><a href="<?php echo($menu_item_array_item->link); ?>"><?php echo($menu_item_array_item->text); ?></a></li>
<?php
            }
?>
                            </ul>
                        </div>
                    </li>
<?php
        }
    }
?>
                </ul>
            </div>
        </div>
    </div>

	<?php echo($html_body); ?>
</body>
</html>
