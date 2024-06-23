<?php

function create_group($data_request , $pdo){
try{
    printLog($data_request);
    $ret = array("server_message" => ""
                ,"message" => "default"
                );
    $insert_error = "Group Not Created";
    $error_message = array();
    $loggable = array("origin" => "Group_Create"
                     ,"type" => ""
                     ,"status" => ""
                     ,"exception" => array()
                     ,"message" => array()
                     ,"action_by_user_id" => $_SESSION["id"]
                     ,"group_id" => "0"
                     ,"destination" => "group_logs"
                     );
    $success = "Created_Group";
    if($_SESSION["user_type"] !== "Admin")
        throw new Exception("Authentication");
    $validation_guard = validate_external_create_inputs($data_request ,  $pdo , $error_message);
    if($validation_guard !== 1)
        throw new Exception("Validation");
    $loggable["message"]["userInput"] = $data_request["group"];
    try{
        $sql = create_insertion_generator($data_request , " user_groups " , "group" , 1);
        $statement = $pdo->prepare($sql);
        foreach($data_request["group"] as $key => &$value){
            $statement->bindParam(":" . $key , $value);
        }
        $statement->execute();
        $group_id = $pdo->lastInsertId();
    }catch(PDOException $e){
        $loggable["exception"]["PDOMessage"] = $e->getMessage();
        $loggable["message"]["sql"] = $sql;
        throw new Exception("Server_Error_CG0001");
    }
    $request = array("fetch" => " * "
                    ,"table" => " user_groups "
                    ,"counted" => 1
                    ,"specific" => " id =" . $group_id
                    );
    $loggable["type"] = "Created_Group";
    $loggable["group_id"] = $group_id;
    $ret["server_message"] = "Group Created";
    $ret["message"] = get_query($request , $pdo);
    throw new Exception("Created_Group");
}catch(Exception $e){
    log_create($ret , $success , $e , $loggable , $error_message , $pdo);
    return $ret;
}
}


?>
