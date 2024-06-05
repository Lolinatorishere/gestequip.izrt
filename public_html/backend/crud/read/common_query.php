<?php

include_once query_generator_dir;

function get_queries($request , $pdo){
try{
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    $ret = array();
    $sql = common_select_query($request);
    if($sql == "error")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $ids = $statement->fetchAll(PDO::FETCH_ASSOC);
    $ret["success"] = "success";
    $ret["items"] = $ids;
    return $ret;
}catch(PDOException $e){
    error_log(print_r($e , true));
    return $sql_error;
}
}

function get_query($request , $pdo){
try{
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    $ret = array();
    $sql = common_select_query($request);
    if($sql == "error")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $ids = $statement->fetch(PDO::FETCH_ASSOC);
    $ret["success"] = "success";
    $ret["items"] = $ids;
    return $ret;
}catch(PDOException $e){
    error_log(print_r($e , true));
    return $sql_error;
}
} 

?>
