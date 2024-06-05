<?php
function update_equipment($request , $pdo){
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
