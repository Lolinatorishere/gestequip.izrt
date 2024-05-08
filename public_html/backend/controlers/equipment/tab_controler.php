<?php
session_start();
//so i can validade the user
$tab_request = $_GET["request"];
$request_type = $_GET["type"];

$tab_request = preg_replace('/[^a-zA-Z0-9_ -]/s' , '' , $tab_request); 
$request_type = preg_replace('/[^a-zA-Z0-9_ -]/s' , '' , $request_type);

function tab_request_validation($request){
    $trim_req = trim($request);
    if(strlen($trim_req) !== 6){
        return 0;
    }
    switch ($trim_req){
        case "yur_eq":
            return 1;
            break;

        case "grp_eq":
            return 1;
            break;
       
        case "sch_eq":
            return 1;
            break;

        case "add_eq":
            return 2;
            break;
         
        case "all_eq":
            return 2;
            break;

        case "rem_eq":
            return 2;
            break;

        case "log_eq":
            return 2;
            break;

        default:
            error_log("default");
            return 0;
            break;
    }
}

function type_parse($type){
    $trim_type = trim($type);
    if(strlen($trim_type) !== 4){
        return 0;
    }

    switch($type){

        //user interface requested
        case "usri":
            return 1;
            break;

        //tab data requested
        case "data":
            return 2;
            break;

        default:
            return 0;
            break;
    }
}

function load_ui($ui){
    $dir = '/var/www/html/gestequip.izrt/public_html/frontend/iframes/equipment/tabs/' . $ui . '.html';
    error_log($dir);
    return file_get_contents($dir);
} 

function crud_request_validation(){
    switch($_GET["crud"]){
        case "create":
            return 1;
            break;
        
        case "read":
            return 2;
            break;
        
        case "update":
            return 3;
            break;
        
        case "delete":
            return 4;
            break;
        
        default:
            return 0;
            break;
    }
}




// function to handle crud requests from tabs

function data_request($tab){
    //default return value
    $ret = array(
        'success' => 'false');
        
    if(!isset($_GET["crud"]))
        return $ret;
    $crud = crud_request_validation();
    if($crud === 0)
        return $ret;
    error_log($crud);
    return $ret;
}

function request_handle($result , $tab , $type){
    if($result === 0 || $type === 0){
        // no valid esponse possible
        return array("error");
    }
    if($result === 2){
        // validate user type;
        if(!isset($_SESSION["user_type"])){
            return array("error");
        }
        if($_SESSION["user_type" !== 'Admin']){
            return array("error");
        }
    }
    if($type === 2){
        $ret = data_request($tab);
        return array("success" , $ret);
    }
    return array("success" , load_ui($tab));
}

$req_tab = tab_request_validation($tab_request);
$req_type = type_parse($request_type);
$ret = request_handle($req_tab , $tab_request , $req_type);
if($req_type === 1){
    echo json_encode(array('success' => $ret[0]
                          ,'html' => $ret[1]));
}
if($req_type === 2){
}
?>