<?php
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
        if($i !== $total){
            $sql .= ', ';
        } 
    }
    return $sql;
}

?>
