<?php
session_start();
// defines section 
// to avoid other files from working without the controler

if(!defined('common_funcs'))
    define('common_funcs' , '/var/www/html/gestequip.izrt/public_html/backend/crud/common/common_functions.php');

if(!defined('pdo_config_dir'))
    define('pdo_config_dir' , '/var/www/html/gestequip.izrt/public_html/backend/config/pdo_config.php');
 
if(!defined('query_generator_dir'))
    define('query_generator_dir' , '/var/www/html/gestequip.izrt/public_html/backend/crud/common/query_generator.php');

// crud functions 
require_once "/var/www/html/gestequip.izrt/public_html/backend/crud/common/common_query.php";
require_once "/var/www/html/gestequip.izrt/public_html/backend/crud/common/describe_column.php";
require_once "/var/www/html/gestequip.izrt/public_html/backend/common/merge_arrays.php"; 
require_once "/var/www/html/gestequip.izrt/public_html/backend/crud/read/equipment_query.php";
require_once "/var/www/html/gestequip.izrt/public_html/backend/crud/read/group_query.php";
require_once "/var/www/html/gestequip.izrt/public_html/backend/crud/read/user_query.php";

// base get requests 
$tab_request = $_GET["tab"];
$request_type = $_GET["type"];
function tab_request_validation($tab){
    $trim_req = trim($tab);
    if(strlen($trim_req) !== 6){
        return 0;
    }
    switch ($trim_req){
        case "yur_eq":
            return 1;
            break;
        case "grp_eq":
            return 1;
            break;
        case "sch_eq":
            return 1;
            break;
        case "add_eq":
            return 2;
            break;
        case "all_eq":
            return 2;
            break;
        case "rem_eq":
            return 2;
            break;
        case "log_eq":
            return 3;
            break;
        default:
            return 0;
            break;
    }
}

function type_validation($type){
    $trim_type = trim($type);
    if(strlen($trim_type) !== 4){
        return 0;
    }
    switch($type){
        //user interface requested
        case "usri":
            return 1;
            break;
        //tab data requested
        case "data":
            return 2;
            break;
        default:
            return 0;
            break;
    }
}

function load_ui($ui){
    $dir = '/var/www/html/gestequip.izrt/public_html/frontend/iframes/equipment/tabs/' . $ui . '.html';
    return file_get_contents($dir);
} 

// checks if the get request is even valid
function request_crud_validation(){
    switch($_GET["crud"]){
        case "create":
            return 1;
            break;
        case "read":
            return 2;
            break;
        case "update":
            return 3;
            break;
        case "delete":
            return 4;
            break;
        default:
            return 0;
            break;
    }
}

function is_user_in_groups($groups){
    $guard = 0;
    $group_auth = array('auth' => 1 
                       ,'own_auth' => 1
                       ,'de_auth' => 1
    );
    foreach ($group_auth as $key => $value) {
        if(count($groups[$key]) != 0){
            $guard++;
        }
    }
    if($guard == 0){
        return 1;
    }
    return 0;
}

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

// this function was also a major headache to make but less than the previous one
function user_group_sql_query_metacode($group_ids , $user_id){
    $sql = '';
    $i = 0;
    foreach($group_ids as $auth => $group_id){
        if($auth === "all_groups")
            break;
        if($i > 0 && $i < $group_ids["total_items"])$sql .= " or ";
        if($auth === "auth"){
            foreach($group_id as $id){
                $sql .= "(group_id = " . $id . " and user_permission_level >= 0)";
            }
        }
        if($auth === "own_auth" || $auth === "de_auth"){
            foreach($group_id as $id){
                $sql .= "(group_id = " . $id . " and user_id = " . $user_id . ")";
            }
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

function full_group_equipment_user_data($users_groups_equipments , $equipments , $users , $groups){
    $items = array();
    $all_items = array();
    foreach($users_groups_equipments["items"] as $usr_grp_eq){
        $data = array();
        $eq = array("eq" => "equipment:");
        $us = array("us" => "user:");
        $gp = array("gp" => "group:");
        array_push($data , $eq);
        foreach($equipments["items"] as $equipment){
            if($equipment["equipment_id"] === $usr_grp_eq["equipment_id"]){
                $eq_info = array();
                foreach($equipment as $key => $value){
                    if($key === "id")
                        continue;
                    if($key === "serial_md5")
                        continue;
                    $eq_info[$key] = $value;
                }
                array_push($data , $eq_info);
            }
        }
        array_push($data , $us);
        foreach($users["items"] as $user){
            if($user["id"] === $usr_grp_eq["user_id"]){
                $i = 1;
                $us_info = array();
                foreach ($user as $key => $value) {
                    if($key === "id"){
                        $us_info["user_id"] = $value;
                        continue;
                    }
                    if($i%2 === 0){
                        $us_info[$key] = $value;
                    }
                    $i++;
                }
                array_push($data , $us_info);
            }
        }
        array_push($data , $gp);
        foreach($groups["items"] as $group){
            if($group["id"] === $usr_grp_eq["group_id"]){
            $i = 1;
                $gp_info = array();
                foreach ($group as $key => $value) {
                    if($key === "id"){
                        $gp_info["group_id"] = $value;
                        continue;
                    }
                    if($i%2 === 0){
                        $gp_info[$key] = $value;
                    }
                    $i++;
                }
                array_push($data , $gp_info);
            }
        }
        $data = merge_arrays($us , $us_info , $gp , $gp_info , $eq , $eq_info);
        array_push($all_items , $data);
    }
    $items["items"] = $all_items;
    $items["pages"] = $equipments["pages"];
    $items["current_page"] = $equipments["current_page"];
    $items["paging"] = $equipments["paging"]; 
    $items["total_items"] = $equipments["total_items"];
    return $items;
}

function parse_equipment_type_columns($columns){
    $parsed_columns = array();
    $i = 1;
    $filter = array("filter" => array("0","1","2","3","4","5"));
    foreach($columns as $column){
        if($column["Field"] === "id")
            continue;
        if($column["Field"] === "equipment_id")
            continue;
        $column = merge_arrays($filter , $column);
        array_push($parsed_columns , $column);
    }
    return $parsed_columns;
}

function clean_query($filter , $items){
    $washed_items = array();
    foreach($items as $item){
        array_push($washed_items , merge_arrays($filter , $item));
    } 
    return $washed_items;
}

function tab_read_request($tab , &$data_request , $user_id , $pdo){
    $group_ids = '';
    $user_ids = '';
    $equipment_ids ='';
    $data = array();
    switch($tab){
        case "yur_eq":
            $data_request["fetch"] = " * ";
            $data_request["table"] = "users_inside_groups_equipments";
            $data_request["specific"] = "user_id = " . $user_id;
            $all_equipment = get_equipments($data_request , $pdo);
            if(count($all_equipment["items"]) == 0)
                break;
            return $all_equipment;
        case "grp_eq":
            $user_group_info = $_SESSION["group_auth"];
            // turn the ids from the fetched groups into a string that 
            // can be read by mysql
            for($i = 0 ; $i < $user_group_info["total_items"] ; $i++){
                $group_ids .= $user_group_info["all_groups"][$i];
                if($i !== $user_group_info["total_items"]-1)
                    $group_ids .= ', ';
            }
            // user query to check the auth level of each group to dissalow
            // non auth users from seeing all the equipment in groups that arent
            // their own
            if(is_user_in_groups($user_group_info) === 1)
                break;
            $request = array("fetch" => " distinct user_id , group_id "
                            ,"table" => "users_inside_groups"
                            ,"specific" => user_group_sql_query_metacode($user_group_info , $user_id)
                            ,"counted" => 1);
            // gets all the unique user_ids and groups
            $equipment_groups_users_info = get_queries($request , $pdo);
            // returns all the unique user_ids as a string
            $unique_users = equipment_group_user_sql_metacode($equipment_groups_users_info["items"]);
            // get the group names of where the user is part of 
            $request = array("fetch" => " id , group_name "
                            ,"table" => "user_groups"
                            ,"specific" => "id IN( " . $group_ids . " ) " 
                            ,"counted" => 1
                        );
            $groups_info = get_queries($request , $pdo);
            // get the user
            $request = array("fetch" => " id , users_name , email , phone_number , regional_indicator "
                            ,"table" => "users"
                            ,"specific" => "id IN( " . $unique_users . " ) " 
                            ,"counted" => 1
                        );
            $users_info = get_queries($request , $pdo);
            // get the items
            $request = array("fetch" => " * "
                            ,"table" => " users_inside_groups_equipments "
                            ,"specific" => equipment_sql_query_metacode($equipment_groups_users_info["items"]) 
                            ,"counted" => 1
                        );
            $links = get_queries($request , $pdo);
            // Main query for items
            $data_request["fetch"] = " * ";
            $data_request["table"] = " users_inside_groups_equipments ";
            $data_request["specific"] = equipment_sql_query_metacode($equipment_groups_users_info["items"]);
            $equipments_info = get_equipments($data_request , $pdo);
            // Hydrate the request with aditional information to be sent to the frontend
            $data = full_group_equipment_user_data($links , $equipments_info , $users_info , $groups_info);
            return $data;
        case 'add_eq':
            // what queries can data specific have:
            //$data_specific = array("types" => array(),"groups" => array(),"users" => array(),"user" => array(),"types_specific" => array());
            if(!isset($_SESSION["group_auth"]))
                break;
            $auth_groups = $_SESSION["group_auth"]["auth"];
            if(!isset($data_request["refresh"])){
                $filter = array("filter" => array("0","1","2","3"));
                $data_specific = array("types" => array()
                                      ,"groups" => array()
                                 );
                $request = array("fetch" => " * "
                                ,"table" => " equipment_types "
                                ,"counted" => 1
                            );
                $equipment_types = get_queries($request , $pdo);
                $request = array("fetch" => " * " 
                                ,"table" => " user_groups "
                                ,"specific" => " id IN ( " . sql_array_query_metacode($auth_groups) . " ) "
                                ,"limit" => 8
                            );
                $manageable_groups = get_groups($request , $pdo);
                $equipment_types["items"] = clean_query($filter  , $equipment_types["items"]);
                $_SESSION["equipment_types"] = $equipment_types["items"];
                $manageable_groups["items"] = clean_query($filter  , $manageable_groups["items"]);
                $data_specific["types"] = $equipment_types;
                $data_specific["groups"] = $manageable_groups;
                return $data_specific;
            }else{
                switch($data_request["refresh"]){
                    case "groups":
                        $data_specific = array("groups" => array());
                        $filter = array("filter" => array("0","1","2","3"));
                        $request = array("fetch" => " * " 
                                ,"table" => " user_groups "
                                ,"specific" => " id IN ( " . sql_array_query_metacode($auth_groups) . " ) "
                                ,"current_page" => $data_request["page"]
                                ,"limit" => 8
                            );
                        $manageable_groups = get_groups($request , $pdo);
                        $manageable_groups["items"] = clean_query($filter , $manageable_groups["items"]);
                        $data_specific["groups"] = $manageable_groups;
                        return $data_specific;
                    case "grp_usrs":
                        foreach ($auth_groups as $auth) {
                            if($auth == $data_request["origin"]){
                                $guard = 0;
                            }
                        }
                        if(!isset($guard))
                            break;
                        $data_specific = array("users" => array());
                        $request = array("fetch" => " * "
                                        ,"table" => " users_inside_groups "
                                        ,"specific" => " group_id = " . $data_request["origin"]
                                        ,"limit" => 8
                                    );
                        $group_users = get_users($request , $pdo);
                        $data_specific["users"] = $group_users;
                        return $data_specific;
                    case "eq_tables":
                        if(!isset($_SESSION["equipment_types"]))
                            break;
                        foreach($_SESSION["equipment_types"] as $type) {
                            if($data_request["origin"] === $type["equipment_type"]){
                                $guard = 0;
                                break;
                            }
                        }
                        if(!isset($guard))
                            break;
                        $data_specific = array("types_specific" => array());
                        $request = array("table" => $data_request["origin"] . "s");
                        $columns = describe_table($request , $pdo);
                        $columns["items"] = parse_equipment_type_columns($columns["items"]);
                        $data_specific["types_specific"] = $columns;
                        return $data_specific;
                    default:
                        break; 
                }
            }
            break;
        }
    $data_request["error"] = "error";
    return $data_request;
}

function tab_create_request($data_request , $tab , $user_id , $pdo){
    switch($tab){
        case "add_eq":
            return create_equipment($data_request , $pdo);
    }
}

function tab_create_information($tab , $user_id , $pdo){
    $data_request = array();
    if(!isset($_POST["equipment"]))
        return 0;
    if(!isset($_POST["normal"]))
        return 0;
    if(!isset($_POST["special"]))
        return 0;
    if(!isset($_POST["user"]))
        return 0;
    if(!isset($_POST["group"]))
        return 0;
    $equipment_type = preg_replace('/[^a-zA-Z]/s' , '' , $_POST["equipment"]); 
    $user_info = $_POST["user"];
    $group_info = $_POST["group"];
    $data_request["equipment_type"] = $equipment_type; 
    $normal_info = $_POST["normal"];
    $special_info = $_POST["special"];
    foreach($normal_info as $key => $info){
        $data_request["normal"][$key] = " \'" . $info . "\' ";
    }
    foreach($special_info as $key => $info){
        $data_request["special"][$key] = " \'" . $info . "\' ";
    }
    return tab_create_request($data_request , $tab , $user_id , $pdo);
}


// gets the correct requests for each tab
function tab_read_information($tab , $user_id , $pdo){
    $data_request = array();
    if(isset($_GET["page"])){
        $page = preg_replace('/[^0-9]/s' , '' , $_GET["page"]); 
        $data_request["page"] = $page;
    } 
    if(isset($_GET["t_i"])){
        $total_items = preg_replace('/[^0-9]/s' , '' , $_GET["t_i"]); 
        $data_request["total_items"] = $total_items;
    }
    if(isset($_GET["pgng"])){
        $paging = preg_replace('/[^0-9]/s' , '' , $_GET["pgng"]); 
        $data_request["paging"] = $paging;
    }
    if(isset($_GET["rfsh"])){// refesh x data
        $refresh = preg_replace('/[^a-zA-Z_]/s' , '' , $_GET["rfsh"]); 
        $data_request["refresh"] = $refresh;
    }
    if(isset($_GET["rgin"])){// origin of refresh
        $origin = preg_replace('/[^a-zA-Z0-9]/s' , '' , $_GET["rgin"]); 
        $data_request["origin"] = $origin;
    }
    return tab_read_request($tab , $data_request , $user_id , $pdo);
}

// function to handle crud requests from tabs
function data_request($tab , $pdo , $user_id){
    //default return value
    $ret = array(
        'success' => 'false');
    if(!isset($_GET["crud"]))
        return $ret;
    $crud = request_crud_validation();
    if($crud === 0)
        return $ret;
    if($crud === 1) //create new info
        $data = tab_create_information($tab , $user_id , $pdo);
    if($crud === 2) //read info
        $data = tab_read_information($tab , $user_id , $pdo);
    $ret = $data;
    return $ret;

}
function tab_auth_handle($auth_level){
    if($auth_level === 1)
        return 1;
    if($_SESSION["user_type"] === 'Admin')
        return 1;
    if($auth_level === 2){
        if($_SESSION["user_type"] === 'Manager')
            return 1;
    }
    return 0;
} 

function request_handle($tab_valid , $tab , $type_valid){
    // validity guard clause
    if($tab_valid === 0)
        return array("error");
    // auth guard clause
    if(tab_auth_handle($tab_valid) === 0)
        return array("error");
    // checks if the type_valid is equivilent to data
    if($type_valid === 2){
        require_once pdo_config_dir;
        $user_id = $_SESSION["id"];
        $ret = data_request($tab , $pdo , $user_id);
        unset($pdo);
        if(isset($ret["error"]))
            return array("unavailable" , $ret);
        return array("success" , $ret);
    }
    return array("success" , load_ui($tab));
}

$req_tab = tab_request_validation($tab_request);
$req_type = type_validation($request_type);
$ret = request_handle($req_tab , $tab_request , $req_type);
if($req_type === 1){
    echo json_encode(array('ui' => $ret[0]
                          ,'html' => $ret[1]));
}
if($req_type === 2){
    echo json_encode(array('data' => $ret[0]
                          ,'tab' => $tab_request
                          ,'information' => $ret[1]));
}
unset($pdo);
