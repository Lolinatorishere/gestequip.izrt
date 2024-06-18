<?php

include_once query_generator_dir;

function log_parse($log_type , $log){
    $values = array(" :action_by_user_id "
                   ," :log_origin "
                   ," :log_type "
                   ," :log_status "
                   ," :log_message "
                   ," :user_id ");
    $columns = array("`action_by_user_id`"
                    ,"`log_origin`"
                    ,"`log_type`"
                    ,"`log_status`"
                    ,"`log_message`"
                    ,"`user_id`" );
    if(isset($log["equipment_id"])){
        array_push($values , " :equipment_id ");
        array_push($columns , "`equipment_id`");
    }
    if(isset($log["group_id"])){
        array_push($values , " :group_id ");
        array_push($columns , "`group_id`");
    }
    $request = array("multiple" => 1
                    ,"table" => $log_type
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
    $sql = log_parse($log_type , $log);
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
    $statement->bindParam(':log_origin' , $log["origin"]);
    $statement->bindParam(':log_type' , $log["type"]);
    $statement->bindParam(':log_status' , $log["status"]);
    $statement->bindParam(':log_message' , $message);
    $statement->bindParam(':action_by_user_id' , $_SESSION["id"]);
    $statement->bindParam(':user_id'  , $log["user_id"]);
    if(isset($log["equipment_id"])){
        $statement->bindParam(':equipment_id'  , $log["equipment_id"]);
    }
    if(isset($log["group_id"])){
        $statement->bindParam(':group_id'  , $log["group_id"]);
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
