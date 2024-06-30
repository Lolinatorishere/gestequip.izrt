<?php

function create_information_sanitize($tab , $user_id , $pdo){
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
    return create_request($data_request , $tab , $user_id , $pdo , $origin);
}

function update_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    $data_request = sanitize_query($_POST);
    if(isset($_POST["group_id"])){
        $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["group_id"]);
    }
    if(isset($_POST["user_permission_level"])){
        $data_request["user_permission_level"] = preg_replace('/[^0-9]/s' , '' , $_POST["user_permission_level"]);
    }
    if(isset($_POST["user_id"])){
        $data_request["user_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["user_id"]);
    }
    if(isset($data_request["query"])){
        $data_request["group"] = $data_request["query"];
    }
    return update_request($data_request , $tab , $user_id , $pdo , $origin);
}

function delete_information_sanitize($tab , $user_id , $pdo){
    if(isset($_GET["rgin"])){// origin of refresh
        $origin = preg_replace('/[^a-zA-Z0-9]/s' , '' , $_GET["rgin"]);
    }else{
        return "Create origin has not been set";
    }
    $data_request = array();
    if(isset($_POST["user_id"])){
        $data_request["user_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["user_id"]);
    }
    if(isset($_POST["group_id"])){
        $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["group_id"]);
    }
    if(isset($_POST["response"])){
        $data_request["response"] = preg_replace('/[^a-zA-Z]/s' , '' , $_POST["response"]);
    }
    return delete_request($data_request , $tab , $user_id , $pdo , $origin);
}



?>
