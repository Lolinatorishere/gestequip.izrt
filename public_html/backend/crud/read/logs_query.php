<?php
if($_SESSION["user_type"] !== "Admin")
    die();

function get_logs_by_status($data_request , $log_status , $log_table ,  $pdo){
    $request = array("fetch" => " * "
                    ,"table" => $log_table
                    ,"specific" => "log_status='" . $log_status . "'"
                    );
    if(isset($data_request["paging"])){
        $request["paging"] = $data_request["paging"];
    }
    if(isset($data_request["page"])){
        $request["page"] = $data_request["page"];
    }
    if(isset($data_request["limit"])){
        $request["limit"] = $data_request["limit"];
    }
    if(isset($data_request["total_items"])){
        $request["total_items"] = $data_request["total_items"];
    }
    return get_queries($request , $pdo);
}

function get_logs($data_request , $log_table , $pdo){
    $request = array("fetch" => " * "
                    ,"table" => $log_table
                    ,"specific" => " id > 0 "
                    );
    if(isset($data_request["paging"])){
        $request["paging"] = $data_request["paging"];
    }
    if(isset($data_request["page"])){
        $request["page"] = $data_request["page"];
    }
    if(isset($data_request["limit"])){
        $request["limit"] = $data_request["limit"];
    }
    if(isset($data_request["total_items"])){
        $request["total_items"] = $data_request["total_items"];
    }
    return get_queries($request , $pdo);
}

?>
