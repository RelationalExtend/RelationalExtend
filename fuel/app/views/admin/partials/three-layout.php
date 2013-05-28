<div class="container">
    <div class="hero-unit">
        <h1><?php echo($page_title); ?></h1>
        <p><?php echo($page_title_content); ?></p>
    </div>

    <div class="row">
<?php

    $num_rows = count($page_rows);
    $three_row_count = 1;
    $current_row = 1;

    foreach($page_rows as $page_row)
    {
        if($three_row_count == 3) {
            $three_row_count = 1;
            echo("</div><div class='row'>");
        }

        $current_row ++;
?>
        <div class="span4">
            <h2><?php echo($page_row->heading); ?></h2>
            <?php echo($page_row->content); ?>
        </div>
<?php
        if($current_row == $num_rows) {
            echo("</div>");
        }
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