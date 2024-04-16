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
        $teamsHook = add_submenu_page('whatsapp-templates', 'Whatsapp Teams' , 'Whatsapp Templates' , 'manage_options' ,'whatsapp-templates' ,array($this,'whatsappTemplatesPage'));
        $teamsHook = add_submenu_page('whatsapp-templates', 'Whatsapp Teams' , 'Whatsapp Teams' , 'manage_options' ,'whatsapp-teams' ,array($this,'teamsSubPage'));
        add_action("load-{$mainHook}", array($this, "pluginFiles"));
        add_action("load-{$teamsHook}", array($this, "pluginFiles"));
    }

    function pluginFiles()
    {
        wp_enqueue_style('style', plugin_dir_url(__FILE__) . 'style.css');
    }

    function whatsappTemplatesPage()
    {
        include 'pages/templates.php';
    }
    function teamsSubPage()
    {
        include 'pages/teams.php';
    }
}
new Whatsapp_Templates();
