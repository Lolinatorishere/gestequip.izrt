<?php

function tab_create_information_sanitize($tab , $user_id , $pdo){
    if(!isset($_POST["selected_group"]))
        return 0;
    if(!isset($_POST["selected_user"]))
        return 0;
    if(!isset($_POST["equipment_type"]))
        return 0;
    if(!isset($_POST["default"]))
        return 0;
    if(!isset($_POST["specific"]))
        return 0;
    $equipment_type = preg_replace('/[^a-zA-Z]/s' , '' , $_POST["equipment_type"]); 
    $data_request["user_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["selected_user"]["user_id"]);
    $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["selected_group"]["group_id"]);
    $data_request["equipment_type"] = $equipment_type; 
    $default_info = $_POST["default"];
    $specific_info = $_POST["specific"];
    foreach($default_info as $key => $info){
        $data_request["default"][$key] =  $info;
    }
    foreach($specific_info as $key => $info){
        $data_request["specific"][$key] =  $info;
    }
    return create_request($data_request , $tab , $user_id , $pdo);
}

// gets the correct requests for each tab
function tab_read_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    if(isset($_GET["page"])){
        $page = preg_replace('/[^0-9]/s' , '' , $_GET["page"]); 
        $data_request["page"] = $page;
    } 
    if(isset($_GET["t_i"])){
        $total_items = preg_replace('/[^0-9]/s' , '' , $_GET["t_i"]); 
        $data_request["total_items"] = $total_items;
    }
    if(isset($_GET["pgng"])){
        $paging = preg_replace('/[^0-9]/s' , '' , $_GET["pgng"]); 
        $data_request["paging"] = $paging;
    }
    if(isset($_GET["rfsh"])){// refesh x data
        $refresh = preg_replace('/[^a-zA-Z_]/s' , '' , $_GET["rfsh"]); 
        $data_request["refresh"] = $refresh;
    }
    if(isset($_GET["rgin"])){// origin of refresh
        $origin = preg_replace('/[^a-zA-Z0-9]/s' , '' , $_GET["rgin"]); 
        $data_request["origin"] = $origin;
    }
    return read_request($tab , $data_request , $user_id , $pdo);
}

?>
