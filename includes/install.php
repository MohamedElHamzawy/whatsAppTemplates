<?php
function whatsappActivation()
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta(
        "CREATE TABLE " . $prefix . "whatsapp_templates (
            id BIGINT PRIMARY KEY AUTO_INCREMENT,
            template_name TEXT,
            template_status TEXT,
            content TEXT,
            teams_id TEXT
        )"
    );
    dbDelta(
        "CREATE TABLE " . $prefix . "whatsapp_teams (
            id BIGINT PRIMARY KEY AUTO_INCREMENT,
            team_name TEXT,
            team_members TEXT
        )"
    );
    dbDelta(
        "CREATE TABLE " . $prefix . "team_members (
            id BIGINT PRIMARY KEY AUTO_INCREMENT,
            name TEXT,
            phone_number TEXT
        )"
    );
}
