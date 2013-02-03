<div class="container">
    <div class="hero-unit">
        <h1><?php echo($page_title); ?></h1>
        <p><?php echo($page_title_content); ?></p>
    </div>
<?php

    $num_rows = count($page_rows);
    $current_row = 1;

    foreach($page_rows as $page_row)
    {

        $current_row ++;
?>
    <div class="row">
        <div class="span12">
            <h2><?php echo($page_row->heading); ?></h2>
            <?php echo($page_row->content); ?>
        </div>
    </div>
<?php
    }
?>
    <hr/>
    <footer>
        <p class="pull-right">Page rendered in {exec_time}s using {mem_usage}mb of memory.</p>
        <p>
            <a href="http://fuelphp.com">FuelPHP</a> is released under the MIT license.<br>
            <small>Version: <?php echo Fuel::VERSION; ?></small>
        </p>
    </footer>
</div>
