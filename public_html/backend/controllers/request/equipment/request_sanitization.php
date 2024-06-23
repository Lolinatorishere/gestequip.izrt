<?php

function tab_create_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    $data_request = sanitize_query($_POST);
    if(isset($_GET["rgin"])){// origin of refresh
        $origin = preg_replace('/[^a-zA-Z0-9]/s' , '' , $_GET["rgin"]);
    }else{
        return "Create origin has not been set";
    }
    if(isset($_POST["group_id"])){
        $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["group_id"]);
    }
    if(isset($_POST["user_id"])){
        $data_request["user_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["user_id"]);
    }
    if(isset($_POST["equipment_auth_level"])){
        $data_request["eqiupment_auth_level"] = preg_replace('/[^0-1]/s' , '' , $_POST["equipment_auth_level"]);
    }
    return create_request($data_request , $tab , $user_id , $pdo , $origin);
}

function tab_update_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    if(isset($_GET["rgin"])){// origin of refresh
        $origin = preg_replace('/[^a-zA-Z0-9]/s' , '' , $_GET["rgin"]);
    }else{
        return "Create origin has not been set";
    }
    $data_request = sanitize_query($_POST);
    if(isset($_POST["group_id"])){
        $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["group_id"]);
    }else{
        return "Group_id Not Set";
    }
    if(isset($_POST["user_id"])){
        $data_request["user_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["user_id"]);
    }else{
        return "User_id Not Set";
    }
    if(isset($_POST["equipment_id"])){
        $data_request["equipment_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["equipment_id"]);
    }else{
        return "Equipment_id Not Set";
    }
    return update_request($data_request , $tab , $user_id , $pdo , $origin);
}

function tab_delete_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    if(isset($_GET["rgin"])){// origin of refresh
        $origin = preg_replace('/[^a-zA-Z0-9]/s' , '' , $_GET["rgin"]);
    }else{
        return "Create origin has not been set";
    }
    if(isset($_POST["group_id"])){
        $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["group_id"]);
    }
    if(isset($_POST["user_id"])){
        $data_request["user_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["user_id"]);
    }
    if(isset($_POST["equipment_id"])){
        $data_request["equipment_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["equipment_id"]);
    }
    if(isset($_POST["deletion_response"])){
        $data_request["deletion_response"] = preg_replace('/[^a-zA-Z]/s' , '' , $_POST["deletion_response"]);
    }
    return delete_request($data_request , $tab , $user_id , $pdo , $origin);
}

?>
