<?php 
// this function obtains the basic information of the user 
// define('profile_pdo_config' , '/var/www/html/gestequip.izrt/public_html/backend/config/pdo_config.php');

include_once query_generator_dir;

function get_user_search(){

}

function get_userinfo_groups($request , $pdo){
    $sql_error = "";
    $sql = common_select_query($request);
    $statement = $pdo->prepare($sql);
    if(!$statement)
        return $sql_error;
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $groups =  $statement->fetchAll();
    $user_groups = array("auth" => array()
                        ,"own_auth" => array()
                        ,"de_auth" => array()
                        ,"all_groups" => array()
                        ,"total_items" => 0);
    foreach($groups as $group){
        switch($group["user_permission_level"]){
            case 0: // user is a group manager
                array_push($user_groups["auth"] , $group["group_id"])  ;
                array_push($user_groups["all_groups"] , $group["group_id"]);
                break;
            case 1: // user is permited to alter own equipment
                array_push($user_groups["own_auth"] , $group["group_id"]);
                array_push($user_groups["all_groups"] , $group["group_id"]);
                break;
            case 2: // user is only permited to view own equipment
                array_push($user_groups["de_auth"] , $group["group_id"]);
                array_push($user_groups["all_groups"] , $group["group_id"]);
                break;
            default:
                break;
        }
        $user_groups["total_items"]++;
    }
    return $user_groups;
}

function user_info(){
    require pdo_config_dir;
    $sql_error = "";
    $sql ="SELECT email, account_status, username, users_name, phone_number, regional_indicator, date_created
           FROM users
           WHERE id = ?";
    $statement = $pdo->prepare($sql);

    if(!$statement){
        return $sql_error;
    }

    $statement->bindParam(1 , $_SESSION["id"] , PDO::PARAM_STR);
    $statement->execute();

    if(!$statement){
        return $sql_error;
    }

    $profile = $statement->fetch();

    if(!$statement){
        return $sql_error;
    }

    $name = $profile["users_name"];
    $username = $profile["username"];
    $email = $profile["email"];
    $acc_status = $profile["account_status"];

    // checks for if the phone number and regional indicator are set
    // if they are set it concatenates the values to be easily readable by
    // the frontend and returns "not set" if the either values arent set

    if(isset($profile["regional_indicator"]) && isset($profile["phone_number"])){
        $phone_number = $profile["regional_indicator"] .= " ";
        $phone_number .= strval($profile["phone_number"]);
    }else{
        $phone_number = "not set";
    }
    $reg_date = $profile["date_created"];

    $ret = array('name' => $name
                ,'username' => $username 
                ,'email' => $email 
                ,'acc_status' => $acc_status 
                ,'phone_number' => $phone_number 
                ,'reg_date' => $reg_date);
    return($ret);
}

function get_users($request , $pdo){
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    $ret = array();
    $sql = common_select_query($request);
    if($sql == "")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    $ids = $statement->fetchAll();
    $ret["success"] = "success";
    $ret["items"] = $ids;
    return $ret;
}
?>