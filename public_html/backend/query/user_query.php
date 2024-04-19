<?php 

function request_info(){
    require __DIR__."/../config/pdo_config.php";
    $sql_error = "";
    $sql ="SELECT email, account_status, username, users_name, phone_number, regional_indicator, date_created
           FROM users
           WHERE id = ?";
    $statement = $pdo->prepare($sql);
    if(!$statement)
        return $sql_error;
    
    $statement->bindParam(1 , $_SESSION["id"] , PDO::PARAM_STR);
    $statement->execute();

    if(!$statement)
        return $sql_error;
    $profile = $statement->fetch();

    if(!$profile)
        return $sql_error;


    $name = $profile["users_name"];
    $username = $profile["username"];
    $email = $profile["email"];
    $acc_status = $profile["account_status"];
    if(isset($profile["regional_indicator"]) && isset($profile["phone_number"])){
        $phone_number = $profile["regional_indicator"] .= " ";
        $phone_number .= strval($profile["phone_number"]);
    }else{
        $phone_number = "not set";
    }
    $reg_date = $profile["date_created"];

    $ret = array('name' => $name , 'username' => $username , 'email' => $email , 'acc_status' => $acc_status , 'phone_number' => $phone_number , 'reg_date' => $reg_date );
    unset($pdo);
    return($ret);
}

function get_the_users_groups(){
    require __DIR__."/../config/pdo_config.php";
    $sql_error = "";
    $sql = "SELECT *
            FROM users_inside_groups
            where user_id = ?";
    $statement = $pdo->prepare($sql);

    if(!$statement)
        return $sql_error;
    
    $statement->bindParam(1, $_SESSION["id"] , PDO::PARAM_INT);
    $statement->execute();

    if(!$statement)
        return $sql_error;

    $groups = $statement->fetchAll();        

    if(!$statement)
        return $sql_error;

    unset($pdo);
    return $groups;
}

function get_user_or_group_equipments(){
    require __DIR__."/../config/pdo_config.php";
    $sql_error = "";
    $sql ="SELECT *
           FROM users_inside_groups_equipments
           WHERE user_id = ?";
    $statement = $pdo->prepare($sql);
    if(!$statement)
        return $sql_error;
    
    $statement->bindParam(1 , $_SESSION["id"] , PDO::PARAM_STR);
    $statement->execute();

    if(!$statement)
        return $sql_error;
    $equipment = $statement->fetch();

    if(!$equipment)
        return $sql_error;

    $ret = array();
    unset($pdo);
    return($ret);   
}

?>