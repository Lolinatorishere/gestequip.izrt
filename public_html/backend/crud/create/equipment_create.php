<?php
function equipment_create_request_authentication($request){
    $action_auth = 0;
    $auth_table = $_SESSION["group_auth"];
    foreach($auth_table["auth"] as $authorised_groups){
        if($authorised_groups === $request["group"]){
            $action_auth = 1;
            break;
        }
    }
    if($action_auth !== 1){
        return 0;
    }
    return 1;
} 

function equipment_create_request_validation($req , $pdo){
    $request = array("fetch" => " column_name , data_type "
                    ,"table" => " information_schema.columns "
                    ,"specific" => " table_name = '" . $req["equipment_type"] ."s' "
                );
    $tables = get_query($request , $pdo );
    error_log(print_r($tables , true));
}

function create_equipment($request , $pdo){
    equipment_create_request_validation($request , $pdo)
}
?>