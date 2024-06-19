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

?>
