<?php
if($_SESSION["user_type"] !== "Admin")
    die();

function get_logs_by_status($log_status , $log_table ,  $pdo){
    $request = array("fetch" => " * "
                    ,"table" => $log_table
                    ,"specific" => "log_status=" . $log_status
                    );
    return get_queries($request , $pdo);
}

function get_logs($log_table , $pdo){
    $request = array("fetch" => " * "
                    ,"table" => $log_table
                    );
    return get_queries($request , $pdo);
}

?>
