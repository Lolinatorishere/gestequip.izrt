<?php 
// this function obtains the basic information of the user 
// define('profile_pdo_config' , '/var/www/html/gestequip.izrt/public_html/backend/config/pdo_config.php');

include_once query_generator_dir;
include_once common_funcs;

function get_all_auth_users($request , $pdo){
    $ret = array();
    $groups = $_SESSION["group_auth"]["auth"];
    $limit = $request["limit"];
    $users = array();
    $fetch = array();
    $table = array();
    $what_in = array();
    $specific = array();
    foreach($groups as $key => $group){
        array_push($fetch , " user_id ");
        array_push($table , " users_inside_groups ");
        array_push($what_in , " group_id = ");
        array_push($specific , $group);
    }
    $union = union_generator(multi_query_request_generator($fetch , $table , $what_in , $specific));
    if(!isset($request["total_items"])){
        $sql = "SELECT count(*) FROM (" . $union . ") AS result_table";
        $statement = $pdo->prepare($sql);
        printLog($sql);
        $statement->execute();
        $union_total = $statement->fetch()[0];
        $request["counted"] = 1;
        $request["page"] = 1;
        $request["pages"] = ceil($union_total / $limit);
    }else{
        $union_total = $request["total_items"];
    }
    $page = $request["page"];
    $pages = $request["pages"];
    $sql = "SELECT * FROM
           (" . $union . ")
           AS result_table
           LIMIT ". $limit . 
           " OFFSET " . ($page-1) * $limit;
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $users_id = $statement->fetch(PDO::FETCH_ASSOC);
        foreach($users_id as $user_id){
        $request_user = array("fetch" => " id , users_name "
                             ,"table" => " users "
                             ,"counted" => 1
                             ,"specific" => "id=" . $user_id
                             );
        array_push($users , get_query($request_user , $pdo)["items"]);
    }
    $ret["items"] = $users;
    $ret["pages"] = $pages;
    $ret["current_page"] = $page;
    $ret["paging"] = 1; 
    $ret["total_items"] = $union_total;
    return($ret);
}

// gets all the equipments from certain ids
// todo add try catches to this 
function get_users($request , $pdo){
    $users = array();
    $sudo_guard = 0;
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
    $user_ids = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach($user_ids as $user_id){
        if(isset($request["user_fetch"])){
            $user_fetch = $request["user_fetch"];
        }else{
            $user_fetch = " id , users_name , email , phone_number , regional_indicator ";
        }
        if(isset($request["sudo_group"])){
            if($_SESSION["user_type"] === "Admin"){
                $sudo_guard = 1;
                $sudo_request = array("fetch" => " admin_status "
                                     ,"table" => " sudo_group "
                                     ,"counted" => 1
                                     ,"specific" => "id_user=" . $user_id["id"]
                                     );
                $sudo_info = get_query($sudo_request , $pdo);
            }
        }
        $request = array("fetch" => $user_fetch
                        ,"table" => " users "
                        ,"specific" => " id = " . $user_id["id"]
                        ,"counted" => 1
                        );
        $user = get_query($request , $pdo);
        if($sudo_guard === 1){
            if(isset($sudo_info["items"])){
                $user["items"]["admin_status"] = $sudo_info["items"]["admin_status"];
                $sudo_guard = 0;
            }
        }
        array_push($users , merge_arrays($user));
    }
    $ret["items"] = $users;
    $ret["pages"] = $pages;
    $ret["current_page"] = $page;
    $ret["paging"] = 1; 
    $ret["total_items"] = $t_i;
    return($ret);
}

?>
