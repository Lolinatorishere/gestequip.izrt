<?php

function delete_equipment($request , $pdo){
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
