<?php

function external_delete_equipment($data_request , $pdo){
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
            array_push($values , $value);
        }
        $request = array("table" => " equipment "
                        ,"columns" => $columns
                        ,"values" => $values
                        ,"specific" => "id =" . $data_request["equipment_id"]
                        );
        $update = update_equipment($request , $pdo);
        printLog($update);
    }
}



function delete_equipment($request , $pdo){
    $loggable = array("type" => " Delete Equipment"
                     ,"exception" => array()
                     ,"message" => array()
                     ,"user_id" => $_SESSION["id"]
    );
try{
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    $ret = array();
    $sql = common_delete_query($request);
    if($sql == "error")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $ret["success"] = "success";
    return $ret;
}catch(PDOException $e){
    error_log(print_r($e,true));
    return $sql_error;
}
}

?>
