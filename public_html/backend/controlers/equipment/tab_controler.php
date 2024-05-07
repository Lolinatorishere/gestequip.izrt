<?php
    session_start();
    //so i can validade the user
    $tab_request = $_GET["request"];
    $request_type = $_GET["type"];

    function tab_request_validation($request){
        $request = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $request); 
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
            
            case "all_eq":
                return 1;
                break;

            case "sch_eq":
                return 1;
                break;

            case "add_eq":
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
        $type = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $type);
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
        $ui = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $ui);
        $dir = '/var/www/html/gestequip.izrt/public_html/frontend/iframes/equipment/tabs/' . $ui . '.html';
        error_log($dir);
        return file_get_contents($dir);
    } 

    function request_handle($result , $tab , $type){
        if($result == 0 || $type === 0){
            // no valid esponse possible
            return array("error validation");
        }
        if($result == 2){
            // validate user type;
            if($_SESSION["user_type" !== 'Admin']){
                return array("error auth");
            }
            if($type === 1){
                return array("success" , load_ui($tab));
            }
        }
        if($type === 1){
            return array("success" , load_ui($tab));
        }
    }

$eq_result = tab_request_validation($tab_request);
$req_type = type_parse($request_type);
$ret = request_handle($eq_result , $tab_request , $req_type);
error_log(print_r($ret , true));
if($req_type === 1){
    echo json_encode(array('success' => $ret[0]
                          ,'html' => $ret[1]));
}

?>