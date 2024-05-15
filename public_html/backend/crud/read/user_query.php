<?php 
// this function obtains the basic information of the user 
// define('profile_pdo_config' , '/var/www/html/gestequip.izrt/public_html/backend/config/pdo_config.php');

include_once sql_read_queries_dir;

function get_user_search(){

}

function get_user_groups($request){
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
                continue;
        }
        $user_groups["total_items"]++;
    }
    unset($pdo);
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
        unset($pdo);
        return $sql_error;
    }

    $statement->bindParam(1 , $_SESSION["id"] , PDO::PARAM_STR);
    $statement->execute();

    if(!$statement){
        unset($pdo);
        return $sql_error;
    }

    $profile = $statement->fetch();

    if(!$statement){
        unset($pdo);
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
    unset($pdo);
    return($ret);
}

// uses a query to get the groups of which the user is part of and then 
// returns the results 

function get_the_users_groups(){
    require pdo_config_dir;
    $group_info = array();
    $sql_error = "";
    $sql = "SELECT *
            FROM users_inside_groups
            where user_id = ?";
    $statement = $pdo->prepare($sql);

    if(!$statement){
        unset($pdo);
        return $sql_error;
    }   

    $statement->bindParam(1, $_SESSION["id"] , PDO::PARAM_INT);
    $statement->execute();

    if(!$statement){
        unset($pdo);
        return $sql_error;
    }

    $groups = $statement->fetchAll();        

    if(!$statement){
        unset($pdo);
        return $sql_error;
    }

    if(count($groups)== 0){
        unset($pdo);
        return $groups;
    }

    $sql = "SELECT * 
            FROM user_groups
            WHERE id = ?";

    // creates an array containing each of the variables i want in a group and pushes it into a return
    // value 

    foreach($groups as $group ){
        $statement = $pdo->prepare($sql);
        if(!$statement){
            unset($pdo);
            return $sql_error;
        }
        $statement->bindParam(1, $group["group_id"] , PDO::PARAM_INT);
        $statement->execute();

        $group_check = $statement->fetch();        
        if(count($group_check) == 0){
            continue;
        }
        array_push($group_check , $group["user_permission_level"]);
        array_push($group_info , $group_check);
    }

    unset($pdo);
    return $group_info;
}

// returns the equipments allocated to the user 

function get_user_equipments(){
    require pdo_config_dir;
    $ret = array();
    $sql_error = "";
    // the reason this table exists is because it simplifies the querying 
    // of the equipments of a group or its users
    $sql ="SELECT *
           FROM users_inside_groups_equipments
           WHERE user_id = ?";
    $statement = $pdo->prepare($sql);

    if(!$statement){
        unset($pdo);
        return $sql_error;
    }

    $statement->bindParam(1 , $_SESSION["id"] , PDO::PARAM_STR);
    $statement->execute();

    if(!$statement){
        unset($pdo);
        return $sql_error;
    }
    $equipment_ids = $statement->fetchAll();

    if(!$statement){
        unset($pdo);
        return $sql_error;
    }

    foreach($equipment_ids as $eq_ids){
        $equipment_all = array();
        $sql = "SELECT *
                from equipment
                where id = ?";
        $statement = $pdo->prepare($sql);

        if(!$statement){
            unset($pdo);
            return $sql_error;
        }

        $statement->bindParam(1 , $eq_ids["equipment_id"] , PDO::PARAM_INT);
        $statement->execute();

        if(!$statement){
            unset($pdo);
            return $sql_error;
        }

        $equipment = $statement->fetch();
        if(!$statement){
            unset($pdo);
            return $sql_error;
        }

        $sql = "SELECT *
                FROM ";

        // todo create a metafunction that allows the switch case 
        // to search all possible equipments inside the DB

        switch($equipment["equipment_type"]){
            case 1:
                $sql .= "computers";
            break;
            
            case 2:
                $sql .= "phones";
            break;  
            
            default:
                return $sql_error;
            break;
        }
        $sql .= " WHERE equipment_id = ?";
        
        $statement = $pdo->prepare($sql);
        
        if(!$statement){
            unset($pdo);
            return $sql_error;
        }

        $statement->bindParam(1 , $equipment["id"] , PDO::PARAM_INT);
        $statement->execute();
        
        if(!$statement){
            unset($pdo);
            return $sql_error;
        }

        $equipment_spec = $statement->fetch();
        
        if(!$statement){
            unset($pdo);
            return $sql_error;
        }

        array_push($ret , $equipment , $equipment_spec);
    }                         
    unset($pdo);
    return($ret);   
}

?>