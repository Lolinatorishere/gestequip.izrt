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
    if(isset($_POST["user"]["pass"])){
        $password = $_POST["user"]["pass"];
        $_POST["user"]["pass"] = "0";
    }
    $email = $_POST["user"]["email"];
    $data_request = sanitize_query($_POST);
    if(isset($_POST["group_id"])){
        $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["group_id"]);
        unset($data_request["selected_group"]);
    }
    if(isset($_POST["equipment_id"])){
        $data_request["equipment_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["equipment_id"]);
    }
    if(isset($_POST["user_id"])){
        $data_request["user_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["user_id"]);
    }
    $data_request["pass"] = $password;
    $data_request["email"] = $email;
    return update_request($data_request , $tab , $user_id , $pdo);
}

function tab_delete_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    if(isset($_POST["user_id"])){
        $data_request["user_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["user_id"]);
    }
    if(isset($_POST["group_id"])){
        $data_request["group_id"] = preg_replace('/[^0-9]/s' , '' , $_POST["group_id"]);
    }
    if(isset($_POST["deletion_response"])){
        $data_request["deletion_response"] = preg_replace('/[^a-zA-Z]/s' , '' , $_POST["deletion_response"]);
    }
    return delete_request($data_request , $tab , $user_id , $pdo);
}



?>
