<?php

include_once query_generator_dir;

function get_queries($request , $pdo){
try{
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    if(!isset($request["limit"])){
        $request["limit"] = 20;
    }
    $ret = array();
    $sql = common_select_query($request);
    if($sql == "error")
        return $sql_error;
    if(!isset($request["counted"])){
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $total = $statement->fetch();
        $request["total_items"] = $total[0];
        $request["counted"] = 1;
        $request["page"] = 1;
        $request["pages"] = ceil($request["total_items"] / $request["limit"]);
        $ret["total_items"] = $request["total_items"];
        $ret["counted"] = 1;
        $ret["page"] = $request["page"];
        $ret["pages"] = $request["pages"];
        $sql = common_select_query($request);
    }
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
