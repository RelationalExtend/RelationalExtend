<div class="container">
    <h1><?php echo($page_title); ?></h1>
    <hr/>
    <p><?php echo($page_title_content); ?></p>

    <table class="table">
        <thead>
            <tr>
                <th style="width:70%;">Theme</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
<?php foreach($themes as $theme) { ?>
            <tr>
                <td><?php echo($theme[CMSTheme::THEME_PREFIX]->{ThemeInfo::SEGMENT_THEME_NAME}); ?></td>
                <td><?php echo(AdminHelpers::bootstrap_buttons($theme["buttons"])); ?></td>
            </tr>
<?php } ?>
        </tbody>
    </table>

    <hr/>

    <footer>
        <p class="pull-right">Page rendered in {exec_time}s using {mem_usage}mb of memory.</p>
        <p>
            <a href="http://fuelphp.com">FuelPHP</a> is released under the MIT license.<br>
            <small>Version: <?php echo Fuel::VERSION; ?></small>
        </p>
    </footer>

</div>