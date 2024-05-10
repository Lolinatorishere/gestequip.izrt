<?php

//obtains the authentication level for every group the user is part of
function user_group_auth($id , $pdo){

    $ret = array("success" => "false");
    $sql = "SELECT * 
            FROM users_inside_groups
            WHERE user_id = ?";

    $statement = $pdo->prepare($sql);

    if(!$statement){
        return $ret;
    }

    $statement->bindParam(1 , $id , PDO::PARAM_INT);
    $statement->execute();

    if(!$statement){
        return $ret;
    }

    $ret = $statement->fetchAll();
    return $ret;
}
?>