<?php
/*
Plugin Name: Whatsapp Templates
Description: Manage Whatsapp Templates
Version: 1.0
Author: DreamCity | Moamen
*/
include 'includes/install.php';
register_activation_hook(__FILE__, 'whatsappActivation');
require(plugin_dir_path(__FILE__) . 'API/templates.php');
class Whatsapp_Templates
{
    public function __construct()
    {
        add_action("admin_menu", array($this, "addToMenu"));
    }

    function addToMenu()
    {
        $mainHook = add_menu_page('Whatsapp Templates', 'Whatsapp Templates', 'manage_options', 'whatsapp-templates', array($this, 'whatsappTemplatesPage'), 'dashicons-text', 10);
        add_action("load-{$mainHook}", array($this, "pluginFiles"));
    }

    function pluginFiles()
    {
        wp_enqueue_style('style', plugin_dir_url(__FILE__) . 'style.css');
    }

    function whatsappTemplatesPage()
    {
        include 'pages/templates.php';
    }
}
new Whatsapp_Templates();
