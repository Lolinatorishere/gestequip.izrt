<?php

function external_update_equipment($data_request , $pdo){
    $error_message = array();
    if(user_group_request_authentication($data_request , $pdo) !== 1){
        if(equipment_authentication($data_request , $pdo) !== 1)
            return 0;
    }
    //to remove later printLog here to validate equipment
    printLog(validate_external_update_inputs($data_request , $pdo));
    if(validate_external_update_inputs($data_request , $pdo) !== 1){
        return 0;
    }
}

function internal_update_equipment($request , $pdo){
try{
    $sql_error = array("error" => "error");
    $ret = array();
    if($request === "error")
        return $sql_error;
    $sql = common_update_query($request);
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $ret["success"] = "success";
    return $ret;
}catch(PDOException $e){
    error_log(print_r($e,true));
    $sql_error["PDOException"] = $e;
    return $sql_error;
}
}
?>
