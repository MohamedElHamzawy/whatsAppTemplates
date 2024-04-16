<?php

add_action('rest_api_init', 'whatsaAppMembersRoutes');



function whatsaAppMembersRoutes() {
    register_rest_route('whatsapp/v1', '/members', array(
        'methods' => 'GET',
        'callback' => 'whatsapp_get_members',
    ));
    
    register_rest_route('whatsapp/v1' , '/members' , array(
        'methods' => 'POST',
        "callback" => "addMember",
        'args' => array(
            'name' => array(
                'type'=> 'string',
                'required' => true
            ),
            'phone' => array(
                'type'=> 'string',
                'required' => true
            )

        )
            ));
    
    register_rest_route('whatsapp/v1' , '/members/(?P<id>\d+)' , array(
        'methods' => 'DELETE',
        'callback' => 'deleteMember',
            ));
    
    
    
    
    register_rest_route('whatsapp/v1' , '/members/(?P<id>\d+)' , array(
        'methods' => 'POST',
        'callback' => 'updateMember',
        "args" => array(
            "teams" => array(
                "type" => "Array"
            )
        )
            ));
} 


    

function whatsapp_get_members() {
    global $wpdb;
    $table_name = $wpdb->prefix . "team_members";
    $results = $wpdb->get_results("SELECT * FROM $table_name");
    return $results;
}

function addMember($body){
    global $wpdb;
    $data = array();
    $table_name = $wpdb->prefix ."team_members";
    if (isset($body['name'])){
        $data['name'] = $body['name'];

    }
    if (isset($body['phone'])){
        $data['phone_number'] = $body['phone'];
    }
    $wpdb->insert($table_name,$data);
    return array(
        'message' => 'Member Added successfully',
        $table => $data

    );
}

function deleteMember($body){
    global $wpdb;
    $table_name = $wpdb->prefix . "team_members";
    $id = $body['id'];
    $wpdb->delete($table_name, array('id' => $id));
    return array(
        'message' => 'Member Deleted successfully',
        'id' => $id
    );
}

function updateMember($body){
    global $wpdb;
    $table = $wpdb->prefix . "team_members";
    $memberId = $body["id"];
    $data = array(
        "name" => $body["name"],
        "phone_number" => $body["phone"]);
    $wpdb->update($table, $data, array("id" => $memberId));

    return array(
        "message" => "Template updated successfully"
    );
}

?>

