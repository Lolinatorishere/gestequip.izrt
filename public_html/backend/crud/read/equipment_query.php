<?php

// todo later

function handle_equipment_search($search , &$sql){
    return 0;
    // if($search == null)
    //     return 0;
    // switch($search["category"]){
    //     case "user":
    // }
}


function handle_sql_query($request , $user_id , $page , $limit){
    $sql = "SELECT";
    // allows me to do request paging 
    // this should ask the mysql server how many of the query exists
    if($page === 0)
        $sql .= (" COUNT(*)");
    else
        $sql .= ("*");
    $sql .= " FROM users_inside_groups_equipments";
    switch($request["type"]){
        case "user";
            $sql .= " WHERE user_id = " . $user_id;
            break;
        case "all":
            break;
        case "search":    
            if(handle_equipment_search($request["query"] , $sql) != 0)
                $sql = "";
            break;
        default:
            $sql = "";
            break;
    }
    return $sql;
}

function page_check(&$request){
    $pages = 0;
    if(!isset($request["page"]))
        $request["page"] = 1;
    if(isset($request["total_pages"]))
        $pages = $request["total_pages"];
    if($pages < 0)
        $pages = 0;
    return $pages; 
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

function get_equipments($request){
    require pdo_config_dir;
    $query_pages = 0;
    $page_limit = 20;
    $sql_error = array("error" => "error");

    if(isset($request["error"]))
        return $sql_error;

    if(!isset($request["id"]))
        return $sql_error;

    $user_id = $request["id"];
    $ret = array();
    $equipment_all = array();
    // the reason this table exists is because it simplifies the querying 
    // of the equipments of a group or its users
    $query_pages = page_check($request);
    $sql = handle_sql_query($request , $user_id , $query_pages , $page_limit);
    // request is unavailable
    if($sql == "")
        return $sql_error;

    $statement = $pdo->prepare($sql);
    $statement->execute();

    if(!$statement){
        unset($pdo);
        return $sql_error;
    }

    if($query_pages == 0){
        $rows_in_query = $statement->fetch();
        $request["total_items"] = $rows_in_query[0];
        $query_pages = ceil($rows_in_query[0]/$page_limit);
        $sql = handle_sql_query($request , $user_id , $query_pages , $page_limit);
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
    $ret["pages"] = $query_pages;
    $ret["current_page"] = $request["page"];
    $ret["total_items"] = $request["total_items"];
    unset($pdo);
    return($ret);   
}
?>