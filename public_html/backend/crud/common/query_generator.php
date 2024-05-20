<?php

function common_select_query($request){
    //this function will create the sql query that does:
    // returns total amount of items from a table
    // or the items with the specific requirements 
    $sql = " SELECT ";
    // allows request paging 
    // this should ask the mysql server how many of the query exists
    if(!isset($request["counted"]) && !isset($request["total_items"]))
        $sql .= (" COUNT(*)");
    else
        $sql .= $request["fetch"];
    $sql .= " FROM " 
         . $request["table"] 
         . " WHERE "
         . $request["specific"];
    if(isset($request["paging"])){
    $limit = $request["limit"];
    $page = $request["current_page"];
    $sql .= " LIMIT " . $limit
         .  " OFFSET " . ($page * $limit) - $limit;
    }
    return $sql;
}
?>