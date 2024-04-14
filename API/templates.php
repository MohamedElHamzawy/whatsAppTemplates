<?php
add_action("rest_api_init", "whatsaAppTemplatesRoutes");

function whatsaAppTemplatesRoutes()
{
    register_rest_route("whatsapp/v1", "templates", array(
        "methods" => "GET",
        "callback" => "whatsaAppTemplates",
    ));

    register_rest_route("whatsapp/v1", "templates", array(
        "methods" => "POST",
        "callback" => "addTemplate",
        "args" => array(
            "name" => array(
                "type" => "string",
                "required" => true
            ),
            "status" => array(
                "type" => "string",
                "required" => true
            ),
            "content" => array(
                "type" => "string",
                "required" => true
            )
        )
    ));

    register_rest_route("whatsapp/v1", "templates/(?P<id>\d+)", array(
        "methods" => "GET",
        "callback" => "getTemplate"
    ));

    register_rest_route("whatsapp/v1", "templates/(?P<id>\d+)", array(
        "methods" => "DELETE",
        "callback" => "deleteTemplate"
    ));

    register_rest_route("whatsapp/v1", "templates/(?P<id>\d+)", array(
        "methods" => "POST",
        "callback" => "updateTemplate",
        "args" => array(
            "teams" => array(
                "type" => "Array"
            )
        )
    ));
}

function whatsaAppTemplates()
{
    global $wpdb;
    $templates = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "whatsapp_templates");
    return $templates;
}

function addTemplate($body)
{
    global $wpdb;
    $data = array();
    $table = $wpdb->prefix . "whatsapp_templates";

    if (isset($body['name'])) {
        $data['template_name'] = $body['name'];
    }
    if (isset($body['status'])) {
        $data['template_status'] = $body['status'];
    }
    if (isset($body['content'])) {
        $data['content'] = $body['content'];
    }

    $wpdb->insert($table, $data);

    return array(
        'message' => 'Template added successfully'
    );
}

function updateTemplate($body)
{
    global $wpdb;
    $table = $wpdb->prefix . "whatsapp_templates";

    $templateId = $body["id"];
    $teamsId = $body["teams"];

    $getTemplate = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM " . $table . " WHERE id = $templateId"
    ));
    $oldTeamsArray = explode(",", $getTemplate[0]->teams_id);

    $newTeams = array_unique(array_merge($oldTeamsArray, $teamsId));
    $newTeams = implode(",", array_filter($newTeams));
    $data = array(
        "teams_id" => $newTeams
    );
    $wpdb->update($table, $data, array("id" => $templateId));

    return array(
        "message" => "Template updated successfully"
    );
}
