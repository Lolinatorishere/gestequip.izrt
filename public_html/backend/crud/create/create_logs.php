<?php

function log_parse($log_type){
    $values = array(" :user_id " , " :log_type " , " :log_message ");
    $columns = array(" `user_id`" , "`log_type`", "`log_message`");
    switch ($log_type) {
        case 'equipment':
            array_push($values , " :equipment_id ");
            array_push($columns , "`equipment_id`");
            break;
        case 'user':
            array_push($values , " :group_id ");
            array_push($columns , "`group_id`");
            break;
        default:
        return "error";
    }
    $request = array("multiple" => 1
                    ,"table" => $log_type . "_logs"
                    ,"columns" => $columns
                    ,"values" => $values
                    );
    return common_insert_query($request);
}

function create_log($log , $log_type , $pdo){
try{
    $message = "";
    $sql_error = array("error" => "error");
    $ret = array();
    $sql = log_parse($log_type);
    if($sql == "error")
        return $sql_error;
    if($log["exception"] !== ""){
        $message_merge = array("exception" => array() , "message" => array());
        array_push($message_merge["exception"] , $log["exception"]);
        array_push($message_merge["message"] , $log["message"]);
        $message = json_encode($message_merge);
    }
    $statement = $pdo->prepare($sql);
    if(!isset($message_merge))
        $message = json_encode($log["message"]);
    $statement->bindParam(':log_message' , $message);
    $statement->bindParam(':log_type' , $log["type"]);
    $statement->bindParam(':user_id'  , $log["user_id"]);
    switch ($log_type) {
        case 'equipment':
            $statement->bindParam(':equipment_id'  , $log["equipment_id"]);
            break;
        case 'user':
            $statement->bindParam(':group_id'  , $log["group_id"]);
            break;
        default:
            return $sql_error;
    }
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $log_id = $pdo->lastInsertId();
    $ret["success"] = "success";
    $ret["items"] = $log_id;
    return $ret;
}catch(PDOException $e){
    error_log(print_r($e , true));
    return $sql_error;
}
}
?>
