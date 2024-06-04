<?php

function convert_to_array(&$input){
    if(is_array($input))
        return $input;
    $input = array($input);
}

function common_select_query($request){
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
                .  " OFFSET " . ($page * $limit) - $limit;
        }
    }else{
        error_log($request["specific"]);
    }
    return $sql;
}

function common_insert_query($request){
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
            }
            $sql .= $values ;
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
}
?>
