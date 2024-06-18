<?php

function equipment_search_query_parse_inputs($queries){
    $sql = "";
    $i = 1;
    $total_parameters = count($queries);
    foreach($queries as $key => $value){
        $sql .= "`". $key ."`" . " LIKE '" . $value . "%' ";
        if($i < $total_parameters){
            $sql .= " AND ";
        }
        $i++;
    }
    return $sql;
}

function multi_query_request_generator($fetch , $table , $what_in , $specific){
    $ret = array();
    $internal_fetch = count($fetch);
    $internal_table = count($table);
    $internal_what_in = count($what_in);
    $internal_specific = count($specific);
    if($internal_fetch !== $internal_table)
        return "error";
    if($internal_fetch !== $internal_what_in)
        return "error";
    if($internal_fetch !== $internal_specific)
        return "error";
    for($i = 0 ; $i < $internal_fetch ; $i++){
        $request = array("fetch" => " " . $fetch[$i] . " "
                        ,"table" => " " . $table[$i] . " "
                        ,"counted" => 1
                        ,"specific" => $what_in[$i] . "\"" .$specific[$i] . "\""
                        );
        array_push($ret , common_select_query($request));
    }
    return $ret;
}
 
function union_generator($requests){
    $total = count($requests);
    $i = 1;
    $sql = "";
    if($total === 1){
        return $request[0];
    }
    foreach ($requests as $request){
        $sql .= $request;
        if ($i < $total){
            $sql .= " UNION ";
        }
        $i++;
    }
    return $sql;
}

// this function was also a major headache to make but less than the previous one
function user_group_sql_query_metacode($group_ids , $user_id , $sql_opperation){
    $sql = '';
    $i = 1;
    foreach($group_ids as $auth => $group_id){
        if($i >= $group_ids["total_items"])
            break;
        if($auth === "all_groups")
            break;
        if($auth === "auth"){
            foreach($group_id as $id){
                $sql .= "(group_id = " . $id . " and user_permission_level >= 0)";
                if($i < $group_ids["total_items"])$sql .= $sql_opperation;
                $i++;
            }
        }
        if($auth === "own_auth" || $auth === "de_auth"){
            foreach($group_id as $id){
                $sql .= "(group_id = " . $id . " and user_id = " . $user_id . ")";
                if($i < $group_ids["total_items"])$sql .= $sql_opperation;
                $i++;
            }
        }
    }
    return $sql;
}

function equipment_group_user_sql_metacode($queried_users){
    $unique_users = array();
    $sql = '';
    $i = 1;
    foreach($queried_users as $user){
        array_push($unique_users , $user["user_id"]);
    }
    $unique_users = array_unique($unique_users);
    $total = count($unique_users);
    foreach($unique_users as $user){
        $sql .= $user;
        if($i < $total){
            $sql .= ', ';
        }
        $i++;
    }
    return $sql;
}

// bloody hell this was a headache and a half to write
function equipment_sql_query_metacode($user_data){
    $sql = '';
    $groups = array();
    $i = 0;
    foreach($user_data as $info){
        array_push($groups , $info["group_id"]);
    }
    $groups = array_unique($groups);
    $total_groups = count($groups);
    foreach($groups as $group){
        $sql .= "( group_id = " . $group ;
        $sql_users = '';
        $total_users = 0;
        $user_array = array();
        foreach($user_data as $info){
            if($info["group_id"] === $group){
                array_push($user_array , $info["user_id"]);
            }
        }
        $user_array = array_unique($user_array);
        $total_users = count($user_array);
        for($j = 0 ; $j < $total_users ; $j++){
            $sql_users .= $user_array[$j];
            if($j+1 !== $total_users){
                $sql_users .= ", ";    
            }
        }
        if($total_users !== 0){
            $sql .= ' AND user_id IN ( ' . $sql_users . ' ) ';
        }else{
            $sql .= ' AND user_id = 0 ';
        }
        $sql .= ' ) ';
        if($i+1 !== $total_groups){
            $sql .= " OR ";
        }
        $i++;
    }
    return $sql;
}

function sql_array_query_metacode($inputs){
    $sql = '';
    $i = 1;
    $total = count($inputs);
    foreach($inputs as $input){
        $sql .= $input;
        if($i < $total){
            $sql .= ', ';
        } 
        $i++;
    }
    return $sql;
}

function convert_to_array(&$input){
    if(is_array($input))
        return $input;
    $input = array($input);
}

function common_select_query($request){
try{
    if(!isset($request["current_page"])){
        if(isset($request["paging"]))
            $request["paging"] = 1;
        $request["current_page"] = 1;
        if(isset($request["page"])){
            $request["current_page"] = $request["page"];
        }
    }
    if(!isset($request["limit"])){
        $request["limit"] = 20;
    }
    //this function will create the sql query that does:
    // returns total amount of items from a table
    // or the items with the specific requirements 
    $sql = " SELECT ";
    // allows request paging 
    // this should ask the mysql server how many of the query exists
    if(!isset($request["counted"]) && !isset($request["total_items"])){
        $sql .= (" COUNT(*)");
    }else{
        $sql .= $request["fetch"];
    }
    $sql .= " FROM " 
         . $request["table"];
    if(!isset($request["specific"]))
        return $sql;
    if(!is_array($request["specific"])){
        $sql .= " WHERE "
            . $request["specific"];
        if(isset($request["paging"])){
            $limit = $request["limit"];
            $page = $request["current_page"];
            $sql .= " LIMIT " . $limit
                .  " OFFSET " . ($page-1) * $limit;
        }
    }
    return $sql;
}catch(TypeError $e){
    error_log(print_r($e , true));
    return "error";
}
}

function common_insert_query($request){
try{
    $sql = " INSERT INTO ";
    $sql .= " " . $request["table"] . " ";
    if(isset($request["multiple"])){
        $i = 1;
        convert_to_array($request["columns"]);
        $total = count($request["columns"]);
        foreach ($request["columns"] as $column) {
            if($i === 1){
                $sql .= " ( ";
            }
            $sql .= $column;
            if($i !== $total){
                $sql .= ", ";
            }
            if($i === $total){
                $sql .= " ) ";
            }
            $i++;
        }
    }else{
        $sql .= " ( " . $request["columns"][0] . " ) ";
    }
    $sql .= " VALUES ";
    if(isset($request["multiple"])){
        $i = 1;
        convert_to_array($request["values"]);
        $total = count($request["values"]);
        foreach($request["values"] as $values){
            if($i === 1){
                $sql .= " ( ";
            } $sql .= $values ;
            if($i !== $total){
                $sql .= ",";
            }
            if($i === $total){
                $sql .= " ) ";
            }
            $i++;
        }
    }else{
        $sql .= "( " . $request["values"][0] ." )";
    }
    return $sql;
}catch(TypeError $e){
    error_log(print_r($e , true));
    return "error";
}
}

function common_delete_query($request){
try{
    if(!isset($request["specific"]))
        return "error";
    $sql = " DELETE FROM ";
    $sql .= $request["table"];
    $sql .= " WHERE ";
    $sql .= $request["specific"];
}catch(TypeError $e){
    error_log(print_r($e , true));
    return "error";
}
}

function common_update_query($request){
try{
    if(!isset($request["specific"]))
        return "error";
    $sql = " UPDATE ";
    $sql .= $request["table"];
    $sql .= " SET ";
    if(!is_array($request["columns"]))
        return "error";
    if(!is_array($request["values"]))
        return "error";
    $counted_columns = count($request["columns"]);
    $counted_values = count($request["values"]);
    $update_sql = "";
    if($counted_columns !== $counted_values)
        return "error";
    for($i = 0 ; $i < $counted_columns ; $i++) { 
        $update_sql .= "`" . $request["columns"][$i] ."`";
        $update_sql .= " = ";
        $update_sql .= "'" . $request["values"][$i] . "'";
        if($i + 1 < $counted_columns){
            $update_sql .= ", ";
        }
    }
    $sql .= $update_sql;
    $sql .= " WHERE ";
    $sql .= $request["specific"];
    return $sql;
}catch(TypeError $e){
    error_log(print_r($e , true));
    return "error";
}
}

?>
