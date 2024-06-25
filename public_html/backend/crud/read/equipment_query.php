<?php

include_once query_generator_dir;
include_once common_funcs;

// can get both one or the other
// type id returns id
// equipment_type returns the types name
// both returns both as an array
//
function get_equipment_type($equipment_type , $pdo , $type){
    $request = array("fetch" => " * "
                    ,"table" => " equipment_types "
                    ,"counted" => 1
                    ,"specific" => " equipment_type='" . $equipment_type . "' OR id = '" . $equipment_type . "'"
                    );
    $query = get_query($request , $pdo);
    if(empty($query["items"]))
        return "error";
    switch($type){
        case "both":
            return $query["items"];
        case "name":
            return $query["items"]["equipment_type"];
        case "id":
            return $query["items"]["id"];
        default:
            return "error";
    }
}

function get_equipments_foreach($requests , $request){
    $sql_array = array();
    foreach($requests as $requester){
        if(isset($request["total_items"])){
            $requester["counted"] = 1;
        }
        array_push($sql_array , join_select_query($requester));
    }
    return $sql_array;
}

function get_equipments_query($union_sql , $request){
    $sql = "("
          .  $union_sql
          . ") ";
    if(isset($request["paging"])){
        $limit = $request["limit"];
        $page = $request["page"];
        $sql .= " LIMIT " . $limit
             .  " OFFSET " . ($page-1) * $limit;
    }
    return $sql;
}

// gets all the equipments from certain ids
function get_equipments($request , $pdo){
try{
    $sql_error = array("error" => "error");
    if(!isset($request["limit"]))
        $request["limit"] = 20;
    $sql = "";
    $ret = array();
    $equipment_all = array();
    $sql_array = array();
    // the reason this table exists is because it simplifies the querying 
    // of the equipments of a group or its users
    page_check($request);
    $requests = $request["requests"];
    $sql_array = get_equipments_foreach($requests , $request);
    if(!isset($request["total_items"])){
        $sql = "SELECT SUM(ibtt_total) as total_items FROM (";
        $sql .= union_generator($sql_array);
        $sql .= ") as sum_queries";
    }
    else{
        $sql = get_equipments_query(union_generator($sql_array) , $request);
    }
    printLog($sql);
    // request is unavailable 
    if($sql == "")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    if(!isset($request["total_items"])){
        $rows_in_query = $statement->fetch();
        $request["total_items"] = $rows_in_query[0]; 
        $request["counted"] = 1;
        $request["page"] = 1;
        $request["pages"] = ceil($request["total_items"] / $request["limit"]);
        $sql_array = get_equipments_foreach($requests , $request);
        $sql = get_equipments_query(union_generator($sql_array) , $request);
        $statement = $pdo->prepare($sql);
        $statement->execute();
    }
    $equipment_all = $statement->fetchAll(PDO::FETCH_ASSOC);
    $ret["items"] = $equipment_all;
    $ret["pages"] = $request["total_pages"];
    $ret["current_page"] = $request["page"];
    $ret["paging"] = 1; 
    $ret["total_items"] = $request["total_items"];
    return($ret);   
}catch(PDOException $e){
    $ret = array("error" => "error"
                ,"PDOException" => $e
                );
    return $ret;
}
}

function get_equipment($fetch , $equipment_id , $pdo){
try{
    if(!isset($equipment_id))
        return $sql_error;
    $sql_error = array("error" => "error");
    $table = "";
    $sql = "";
    $item = array();
    $ret = array();
    $equipment_selected = array();
    // the reason this table exists is because it simplifies the querying 
    // of the equipments of a group or its users
    $request = array("fetch" => " " . $fetch . " "
                    ,"table" => "equipment"
                    ,"counted" => 1
                    ,"specific" => "id = " . $equipment_id 
                    );
    $sql = common_select_query($request);
    // request is unavailable
    if($sql === "error")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    // $statement->bindParam(':equipment_id' , $equipment_id);
    // $statement->bindParam(':fetch' , $fetch);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $equipment_default = $statement->fetch(PDO::FETCH_ASSOC);
    if(count($equipment_default) === 0)
        return $sql_error;
    $request = array("fetch" => " * "
                    ,"counted" => 1
                    ,"specific" => "equipment_id=" . $equipment_id
                    );
    if($sql === "error")
        return $sql_error;
    $table = get_equipment_type($equipment_default["equipment_type"] , $pdo , "name");
    if($table === "error")
        return $sql_error;
    $request["table"] = $table;
    $sql = common_select_query($request);
    $statement = $pdo->prepare($sql);
    //$statment->bindParam(':table' , $table);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $equipment_specific = $statement->fetch(PDO::FETCH_ASSOC);
    array_push($item, $equipment_default , $equipment_specific);
    array_push($equipment_selected , query_merge_array($item));
    $ret["success"] = "success";
    $ret["items"] = $equipment_selected;
    return $ret;
}catch(PDOException $e){
    $ret = array("error" => "error"
                ,"PDOException" => $e
                );
    return $ret;
}
}

?>
