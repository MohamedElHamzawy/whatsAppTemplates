<?php

add_action("rest_api_init", "whatsaAppTeamsRoutes");

function whatsaAppTeamsRoutes(){

    
        register_rest_route("whatsapp/v1", "teams", array(
            "methods" => "GET",
            "callback" => "whatsaAppteams",
        ));

        register_rest_route("whatsapp/v1", "teams", array(
            "methods" => "POST",
            "callback" => "addTeam",
            "args" => array(
                "name" => array(
                    "type" => "string",
                    "required" => true
                ))
        ));

        register_rest_route("whatsapp/v1", "teams/(?P<id>\d+)", array(
            "methods" => "POST",
            "callback" => "updateTeam",
            "args" => array(
                "members" => array(
                    "type" => "Array"
                )
            )
        ));

        register_rest_route("whatsapp/v1", "teams/(?P<id>\d+)", array(
            "methods" => "DELETE",
            "callback" => "deleteTeam"
        ));
}

function whatsaAppteams(){

    global $wpdb;
    $teams = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "whatsapp_teams");
    foreach ($teams as $team) {
        $membersIds = explode(",", $team->team_members);
        $membersNames = array();
        foreach ($membersIds as $memberId) {
            $membername = $wpdb->get_results($wpdb->prepare("SELECT name FROM " . $wpdb->prefix . "team_members WHERE id = $memberId"));
            if ($membername[0]->name != "") {
                array_push($membersNames, $membername[0]->name);
            }
        }
        $membersNames = implode(", ", $membersNames);
        $team->team_members = $membersNames;
    }

    return $teams;
}

function addTeam($body){

    global $wpdb;
    $data = array();
    $table = $wpdb->prefix . "whatsapp_teams";

    if (isset($body['name'])) {
        $data['team_name'] = $body['name'];
    }
    $wpdb->insert($table, $data);
    return array(
        'message' => 'Team added successfully'
    );
}

function updateTeam($body){
    

    global $wpdb;
    $table = $wpdb->prefix . "whatsapp_teams";

    $teamId = $body["id"];
    $membersId = $body["members"];



    $getTeam = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM " . $table . " WHERE id = $teamId"
    ));
    $oldTeamsArray = explode(",", $getTeam[0]->team_members);

    $newmembers = array_unique(array_merge($oldTeamsArray, $membersId));
    $newmembers = implode(",", array_filter($newmembers));
    $data = array(
        "team_members" => $newmembers
    );
    $wpdb->update($table, $data, array("id" => $teamId));

    return array(
        "message" => "Team updated successfully"
    );

}

function deleteTeam($body){

    global $wpdb;
    $table = $wpdb->prefix . "whatsapp_teams";
    $teamId = $body["id"];
    $wpdb->delete($table, array("id" => $teamId));
    return array(
        "message" => "Team deleted successfully"
    );
}

?>