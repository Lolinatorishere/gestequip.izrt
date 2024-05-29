<?php

function describe_table($request , $pdo){
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    $ret = array();
    $sql = "DESCRIBE " . $request["table"];
    if($sql == "")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $ids = $statement->fetchAll();
    $ret["success"] = "success";
    $ret["items"] = $ids;
    return $ret;
}
?>
