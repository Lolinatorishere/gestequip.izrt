<?php

include_once sql_read_queries_dir;

// todo later

function handle_equipment_search($search , &$sql){
    return 0;
    // if($search == null)
    //     return 0
    // switch($search["category"]){
    //     case "user":
    // }
}

function page_check(&$request){
    $pages = 0;
    if(!isset($request["total_pages"]))
        $request["total_pages"] = 1;
    if(!isset($request["page"]))
        $request["page"] = 1;
    if($pages <= 0)
        $pages = 1;
    return;
}

function convert_equipment_to_single_array($equipment , $equipment_spec){
    $combined = array();
    $i = 1;
    foreach($equipment as $key => $standard){
        if($i%2 !== 0){
            $combined[$key] = $standard;
        }
        $i++;
    }
    $i = 1;
    foreach($equipment_spec as $key => $standard){
        if($i%2 !== 0){
            $combined[$key] = $standard;
        }
        $i++;
    }
    return $combined;
}

function get_group_equipments($request){
    require pdo_config_dir;
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    $ret = array();
    $sql = common_select_query($request);
    if($sql == "")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    $statement->execute();
    if(!$statement){
        unset($pdo);
        return $sql_error;
    }
    $total_item_ids = $statement->fetchAll();
    error_log(print_r($total_item_ids , true));
    unset($pdo);
    return $ret;
} 

// gets all the equipments from certain ids
function get_equipments($request){
    require pdo_config_dir;
    $request["limit"] = 20;
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    $ret = array();
    $equipment_all = array();
    // the reason this table exists is because it simplifies the querying 
    // of the equipments of a group or its users
    page_check($request);
    $sql = common_select_query($request);
    // request is unavailable
    if($sql == "")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    $statement->execute();
    if(!$statement){
        unset($pdo);
        return $sql_error;
    }
    if(!isset($request["total_items"])){
        $rows_in_query = $statement->fetch();
        $request["total_items"] = $rows_in_query[0];
        $request["counted"] = 1;
        $request["page"] = 1;
        $request["pages"] = ceil($request["total_items"] / $request["limit"]);
        $sql = common_select_query($request);
        $statement = $pdo->prepare($sql);
        $statement->execute();
    }
    $equipment_ids = $statement->fetchAll();
    foreach($equipment_ids as $eq_ids){
        $individual_equipment = array();
        $sql = "SELECT *
                from equipment
                where id = ?";
        $statement = $pdo->prepare($sql);
        if(!$statement){
            unset($pdo);
            return $sql_error;
        }
        $statement->bindParam(1 , $eq_ids["equipment_id"] , PDO::PARAM_INT);
        $statement->execute();
        if(!$statement){
            unset($pdo);
            return $sql_error;
        }
        $equipment = $statement->fetch();
        if(!$statement){
            unset($pdo);
            return $sql_error;
        }
        $sql = "SELECT *
                FROM ";
        // todo create a metafunction that allows the switch case 
        // to search all possible equipments inside the DB
        switch($equipment["equipment_type"]){
            case 1:
                $sql .= "computers";
            break;
            case 2:
                $sql .= "phones";
            break;  
            default:
                return $sql_error;
            break;
        }
        $sql .= " WHERE equipment_id = ?";
        $statement = $pdo->prepare($sql);
        if(!$statement){
            unset($pdo);
            return $sql_error;
        }
        $statement->bindParam(1 , $equipment["id"] , PDO::PARAM_INT);
        $statement->execute();
        if(!$statement){
            unset($pdo);
            return $sql_error;
        }
        $equipment_spec = $statement->fetch();
        if(!$statement){
            unset($pdo);
            return $sql_error;
        }
        array_push($equipment_all , convert_equipment_to_single_array($equipment , $equipment_spec));
    };
    $ret["items"] = $equipment_all;
    $ret["pages"] = $request["total_pages"];
    $ret["current_page"] = $request["page"];
    $ret["paging"] = 1; 
    $ret["total_items"] = $request["total_items"];
    unset($pdo);
    return($ret);   
}
?>