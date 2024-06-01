<?php 
// this function obtains the basic information of the user 
// define('profile_pdo_config' , '/var/www/html/gestequip.izrt/public_html/backend/config/pdo_config.php');

include_once query_generator_dir;
include_once common_funcs;

function get_user_search(){

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

function custom_user_filter($number){
    $filter = array("filter" => array());
    for($i = 0 ; $i < $number/2 ; $i++){
        $filter["filter"][$i] = $i;
    }
    return $filter;
}

// gets all the equipments from certain ids
function get_users($request , $pdo){
    $users = array();
    $sql_error = array("error" => "error");
    if(isset($request["error"]))
        return $sql_error;
    if(!isset($request["limit"]))
        $request["limit"] = 20;
    $ret = array();
    // the reason this table exists is because it simplifies the querying 
    // of the equipments of a group or its users
    page_check($request);
    $sql = common_select_query($request);
    // request is unavailable
    if($sql == "")
        return $sql_error;
    $statement = $pdo->prepare($sql);
    $statement->execute();
    if(!$statement)
        return $sql_error;
    if(!isset($request["total_items"])){
        $rows_in_query = $statement->fetch();
        $request["total_items"] = $rows_in_query[0];
        $request["counted"] = 1;
        $request["page"] = 1;
        $request["pages"] = ceil($request["total_items"] / $request["limit"]);
        $sql = common_select_query($request);
        $statement = $pdo->prepare($sql);
        $statement->execute();
    }
    $t_i = $request["total_items"];
    $counted = $request["counted"];
    $page = $request["page"];
    $pages = $request["pages"];
    $user_ids = $statement->fetchAll();
    foreach ($user_ids as $user_id) {
        $request = array("fetch" => " id , users_name , email , phone_number , regional_indicator "
                        ,"table" => " users "
                        ,"specific" => " id = " . $user_id["user_id"]
                        ,"counted" => 1
        );
        $sql = common_select_query($request);
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $user = $statement->fetch();
        error_log(print_r($user , true));
        $filter = custom_user_filter(count($user));
        array_push($users , merge_arrays($filter , $user));
    }
    $ret["items"] = $users;
    $ret["pages"] = $pages;
    $ret["current_page"] = $page;
    $ret["paging"] = 1; 
    $ret["total_items"] = $t_i;
    return($ret);   
}
?>
