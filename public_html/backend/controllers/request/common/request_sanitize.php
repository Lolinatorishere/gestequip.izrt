<?php

function recursive_query_sanitize($query , $sanitize_query){
    if(!is_array($query)){
        $sanitize_query = trim(preg_replace('/[^a-zA-Z0-9-_ ]/s' , '' , $query));
    }else{
        foreach($query as $key => $input){
            if(is_array($input)){
                $to_sanitize = $sanitize_query[$key];
                $sanitize_query[$key] = recursive_query_sanitize($input , $sanitize_query[$key]);
            }else{
                if(!is_bool($sanitize_query[$key])){
                    $sanitize_query[$key] = trim(preg_replace('/[^a-zA-Z0-9-_ ]/s' , '' , $input));
                }
            }
        }
    }
    return $sanitize_query;
}

function sanitize_query($query){
    $sanitize_query = $query;
    return recursive_query_sanitize($query , $sanitize_query);
}

// gets the correct requests for each tab
function read_information_sanitize($tab , $user_id , $pdo){
    $data_request = array();
    if(isset($_GET["page"])){
        $data_request["page"] = preg_replace('/[^0-9]/s' , '' , $_GET["page"]); 
    } 
    if(isset($_GET["t_i"])){
        $data_request["total_items"] = preg_replace('/[^0-9]/s' , '' , $_GET["t_i"]);
    }
    if(isset($_GET["pgng"])){
        $data_request["paging"] = preg_replace('/[^0-9]/s' , '' , $_GET["pgng"]);
    }
    if(isset($_GET["rfsh"])){// refesh x data
        $data_request["refresh"] = preg_replace('/[^a-zA-Z_]/s' , '' , $_GET["rfsh"]);
    }
    if(isset($_GET["rgin"])){// origin of refresh
        $data_request["origin"] = preg_replace('/[^a-zA-Z0-9]/s' , '' , $_GET["rgin"]);
    }
    if(isset($_POST["query"])){// origin of query not 
        $data_request["query"] = sanitize_query($_POST["query"]);
    }
    return read_request($tab , $data_request , $user_id , $pdo);
}


?>
