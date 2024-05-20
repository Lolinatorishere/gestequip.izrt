<?php

include_once query_generator_dir;
// requires more stuff to compensate
function get_groups($request){
    require pdo_config_dir;
    $sql_error = "";
    $sql = common_select_query($request);
    $statement = $pdo->prepare($sql);
    if(!$statement){
        unset($pdo);
        return $sql_error;
    }
    $statement->execute();
    if(!$statement){
        unset($pdo);
        return $sql_error;
    }
    $groups = $statement->fetchAll();
    unset($pdo);
    return $groups;
}
?>