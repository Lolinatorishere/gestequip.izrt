<?php

// i swear to god im jumping off a cliff due to stupidity
function external_update_equipment($data_request , $pdo){
    $error_message = array();
    if(user_group_request_authentication($data_request , $pdo) !== 1){
        if(equipment_authentication($data_request , $pdo) !== 1)
            return 0;
    }
    if(validate_external_update_inputs($data_request , $pdo , $error_message) !== 1)
        return 0;
    printLog($error_message);
    if(isset($data_request["default"])){
        printLog("good shit");
        $columns = array();
        $values = array();
        foreach($data_request["default"] as $key => $value){
            array_push($columns , $key);
            array_push($values , $key);
        }
        $request = array("table" => " equipment "
                        ,"columns" => $columns
                        ,"values" => $values
                        ,"specific" => $data_request["equipment_id"]
                        );
        printLog($request);
        $sql = common_update_query($request);
        printLog($sql);
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
