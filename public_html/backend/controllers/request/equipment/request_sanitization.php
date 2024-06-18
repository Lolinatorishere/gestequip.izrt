<?php

function recursive_query_sanitize($query , $sanitize_query){
    if(!is_array($query)){
        $sanitize_query = trim(preg_replace('/[^a-zA-Z0-9-_ ]/s' , '' , $query));
    }else{
        foreach($query as $key => $input){
            if(is_array($input)){
                $to_sanitize = $sanitize_query[$key];
                $sanitize_query[$key] = recursive_query_sanitize($input , $sanitize_query[$key]);
            }else{
                if(!is_bool($sanitize_query[$key])){
                    $sanitize_query[$key] = trim(preg_replace('/[^a-zA-Z0-9-_ ]/s' , '' , $input));
                }
            }
        }
    }
    return $sanitize_query;
}

function sanitize_query($query){
    $sanitize_query = $query;
    return recursive_query_sanitize($query , $sanitize_query);
}

function tab_create_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    $data_request = sanitize_query($_POST);
    if(isset($_POST["selected_group"])){
        $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["selected_group"]["group_id"]);
        unset($data_request["selected_group"]);
    }
    if(isset($_POST["selected_user"])){
        $data_request["user_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["selected_user"]["user_id"]);
        unset($data_request["selected_user"]);
    }
    if(isset($_POST["equipment_auth_level"])){
        $data_request[""] = preg_replace('/[^0-1]/s' , '' , $_POST["selected_group"]["group_id"]);
        unset($data_request["selected_group"]);
    }
    return create_request($data_request , $tab , $user_id , $pdo);
}

// gets the correct requests for each tab
function tab_read_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    if(isset($_GET["page"])){
        $data_request["page"] = preg_replace('/[^0-9]/s' , '' , $_GET["page"]); 
    } 
    if(isset($_GET["t_i"])){
        $data_request["total_items"] = preg_replace('/[^0-9]/s' , '' , $_GET["t_i"]);
    }
    if(isset($_GET["pgng"])){
        $data_request["paging"] = preg_replace('/[^0-9]/s' , '' , $_GET["pgng"]);
    }
    if(isset($_GET["rfsh"])){// refesh x data
        $data_request["refresh"] = preg_replace('/[^a-zA-Z_]/s' , '' , $_GET["rfsh"]);
    }
    if(isset($_GET["rgin"])){// origin of refresh
        $data_request["origin"] = preg_replace('/[^a-zA-Z0-9]/s' , '' , $_GET["rgin"]);
    }
    if(isset($_GET["qury"])){// origin of query not 
        $data_request["query"] = sanitize_query(json_decode($_GET["qury"] , true));
    }
    return read_request($tab , $data_request , $user_id , $pdo);
}

function tab_update_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    $data_request = sanitize_query($_POST);
    if(isset($_POST["selected_group"])){
        $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["selected_group"]["group_id"]);
        unset($data_request["selected_group"]);
    }
    if(isset($_POST["selected_user"])){
        $data_request["user_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["selected_user"]["user_id"]);
        unset($data_request["selected_user"]);
    }
    if(isset($_POST["selected_equipment"])){
        $data_request["equipment_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["selected_equipment"]["equipment_id"]);
        unset($data_request["selected_equipment"]);
    }
    return update_request($data_request , $tab , $user_id , $pdo);
}

function tab_delete_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    if(isset($_POST["selected_group"])){
        $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["selected_group"]["group_id"]);
        unset($data_request["selected_group"]);
    }
    if(isset($_POST["selected_user"])){
        $data_request["user_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["selected_user"]["user_id"]);
        unset($data_request["selected_user"]);
    }
    if(isset($_POST["selected_equipment"])){
        $data_request["equipment_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["selected_equipment"]["equipment_id"]);
        unset($data_request["selected_equipment"]);
    }
    if(isset($_POST["deletion_response"])){
        $data_request["deletion_response"] = preg_replace('/[^0-9]/s' , '' , $_POST["deletion_response"]["equipment_id"]);
        unset($data_request["deletion_response"]);
    }
    return delete_request($data_request , $tab , $user_id , $pdo);
}



?>
