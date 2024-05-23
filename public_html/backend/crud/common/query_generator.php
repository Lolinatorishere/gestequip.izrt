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
    if(isset($request["specific"])){
        $sql .= " WHERE "
             . $request["specific"];
        if(isset($request["paging"])){
            $limit = $request["limit"];
            $page = $request["current_page"];
            $sql .= " LIMIT " . $limit
                 .  " OFFSET " . ($page * $limit) - $limit;
        }
    }
    return $sql;
}

function common_insert_query($request){
    $i = 1;
    $sql = " INSERT INTO ";
    $sql .= " " . $request["table"] . " ";
    $sql .= " (" . $request["columns"] . ") ";
    $sql .= " VALUES ";
    if($request["multiple"] === 1){
        convert_to_array($request["values"]);
        $total = count($request["values"]);
        foreach($request["values"] as $values){
            $sql .= "( " . $values . ") ";
            if($i !== $total){
                $sql .= ",";
            }
            $i++;
        }
    }
}
?>