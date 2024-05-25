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
    if($action_auth !== 1)
        return 0;
    return 1;
} 

function equipment_create_request_validation($request , $pdo){
    $table_check = 0;
    $table_request = array("fetch" => " column_name , data_type "
                          ,"table" => " information_schema.columns "
                          ,"specific" => " table_name = '" . $request["equipment_type"] ."s' "
                     );
    $table = get_query($table_request , $pdo);
    error_log(print_r($tables , true));
    $counted_table = count($tables["items"]);
    foreach($request["special_tables"] as $special){
        foreach($table as $column){
            if($column === $special){
                $table_check++;
                break;
            }
        }
    }
    if($table_check !== $counted_table)
        return 0;
    return 1;
}

function create_equipment($request , $pdo){
    $insert_error = "User not created";
    if(equipment_create_request_validation($request["equipment_type"] , $pdo) === 0)
        return $insert_error
    f(equipment_create_request_authentication($request) === 0)
        return $insert_error;
}
?>
