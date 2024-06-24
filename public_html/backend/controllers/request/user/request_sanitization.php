<?php

function tab_create_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    $password = $_POST["user"]["pass"];
    $email = $_POST["user"]["email"];
    $data_request = sanitize_query($_POST);
    if(isset($_POST["group_id"])){
        $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["group_id"]);
    }
    if(isset($_POST["virtual"])){
        $data_request["virtual"] = preg_replace('/[^a-zA-Z0-9]/s' , '' , $_POST["virtual"]);
    }
    if(isset($_POST["admin"])){
        $data_request["admin"] = preg_replace('/[^0-9]/s' , '' , $_POST["admin"]);
    }
    if(isset($data_request["user"]["pass"])){
        $data_request["user"]["pass"] = "0";
    }
    if(isset($data_request["user"]["email"])){
        $data_request["user"]["email"] = "0";
    }
    $data_request["pass"] = $password;
    $data_request["email"] = $email;
    return create_request($data_request , $tab , $user_id , $pdo);
}

function tab_update_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    if(isset($_GET["rgin"])){// origin of refresh
        $origin = preg_replace('/[^a-zA-Z0-9]/s' , '' , $_GET["rgin"]);
    }else{
        return "Create origin has not been set";
    }
    if(isset($_POST["user"]["pass"])){
        $password = $_POST["user"]["pass"];
        $_POST["user"]["pass"] = "0";
    }
    if(isset($_POST["user"]["user_id"]) || isset($_POST["user"]["group_id"])){
        unset($data_request["user"]["user_id"]);
        unset($data_request["user"]["group_id"]);
    }
    $email = $_POST["user"]["email"];
    $data_request = sanitize_query($_POST);
    if(isset($_POST["group_id"])){
        $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["group_id"]);
    }
    if(isset($_POST["user_permission_level"])){
        $data_request["user_permission_level"] = preg_replace('/[^0-9]/s' , '' , $_POST["user_permission_level"]);
    }
    if(isset($_POST["equipment_id"])){
        $data_request["equipment_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["equipment_id"]);
    }
    if(isset($_POST["user_id"])){
        $data_request["user_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["user_id"]);
    }
    $data_request["pass"] = $password;
    $data_request["email"] = $email;
    return update_request($data_request , $tab , $user_id , $pdo , $origin);
}

function tab_delete_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    if(isset($_POST["group_id"])){
        $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["group_id"]);
    }
    if(isset($_POST["deletion_response"])){
        $data_request["deletion_response"] = preg_replace('/[^a-zA-Z]/s' , '' , $_POST["deletion_response"]);
    }
    return delete_request($data_request , $tab , $user_id , $pdo);
}



?>
