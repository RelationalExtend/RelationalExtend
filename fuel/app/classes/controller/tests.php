<?php

class Controller_Tests extends Controller
{
    public function action_testextensions()
    {
        // Test if extensions can be detected
        $extensions = Extension::get_core_extensions();
        echo("<br/>Core extensions:<br/>");
        var_dump($extensions);

        $extension_meta_data = Extension::get_core_extension_meta_data($extensions);
        echo("<br/>Core extensions meta data:<br/>");
        var_dump($extension_meta_data);
    }

    public function action_testextesioninstaller()
    {
        // Test the extension installer

        // Test if extensions can be detected
        $extensions = Extension::get_core_extensions();

        foreach($extensions as $extension) {
            Extension::install_extension($extension);
        }

        echo("Setup tests completed");
    }

    public function action_testextesionuninstaller($extension_id = 0)
    {
        // Test the extension uninstaller

        // Test if extensions can be detected

        if($extension_id > 0) {
            Extension::uninstall_extension($extension_id);
        }

        else {
            $extensions = Extension::get_installed_extensions();

            foreach($extensions as $extension) {
                Extension::uninstall_extension($extension->extension_id);
            }
        }

        echo("Uninstall tests complete");
    }

    public function action_getinstalledextensions()
    {
        // Test installed extensions and get meta data

        // Test if extensions can be detected
        $extensions = Extension::get_installed_extensions();
        echo("<br/>Installed extensions (all) extensions:<br/>");
        var_dump($extensions);

        foreach($extensions as $extension) {
            $extension_meta_data = Extension::get_installed_extension_meta_data($extension->extension_id);
            echo("<br/>Installed extensions meta data: extension ".$extension->extension_name." <br/>");
            var_dump($extension_meta_data);
        }

        $extensions = Extension::get_installed_extensions(true);
        echo("<br/>Installed extensions (only active):<br/>");
        var_dump($extensions);

        foreach($extensions as $extension) {
            $extension_meta_data = Extension::get_installed_extension_meta_data($extension->extension_id);
            echo("<br/>Installed extensions meta data: extension ".$extension->extension_name." <br/>");
            var_dump($extension_meta_data);
        }
    }

    public function action_activateextension($extension_id)
    {
        Extension::activate_extension($extension_id);
        echo("Activation complete");
    }

    public function action_deactivateextension($extension_id)
    {
        Extension::deactivate_extension($extension_id);
        echo("Deactivation complete");
    }

    public function action_testcorethemes()
    {
        $themes = CMSTheme::get_core_themes();
        echo("<br/>Core themes:<br/>");
        var_dump($themes);

        $themes_info = CMSTheme::get_public_theme_meta_data($themes);
        echo("<br/>Core themes metadata:<br/>");
        var_dump($themes_info);
    }

    public function action_testlayoutfile()
    {
        $layout_file_content = CMSTheme::get_layout_content(THEMEPATH."bootstrap/", "layout.html");
        echo($layout_file_content);
    }

    public function action_testpartialfile()
    {
        $partial_file_content = CMSTheme::get_partial_content(THEMEPATH."bootstrap/", "partial.html");
        echo($partial_file_content);
    }

    public function action_testjsfile()
    {
        $js_file_content = CMSTheme::get_javascript_content(THEMEPATH."bootstrap/", "sample.js");
        echo($js_file_content);
    }

    public function action_testcssfile()
    {
        $css_file_content = CMSTheme::get_style_content(THEMEPATH."bootstrap/", "sample.css");
        echo($css_file_content);
    }

    public function action_testinstallthemes()
    {
        $themes = CMSTheme::get_core_themes();

        foreach($themes as $theme) {
            CMSTheme::install_theme($theme, true);
        }

        echo("Themes setup complete");
    }

    public function action_testuninstalltheme($theme_id)
    {
        CMSTheme::uninstall_theme($theme_id);

        echo("Theme uninstall complete");
    }
}
