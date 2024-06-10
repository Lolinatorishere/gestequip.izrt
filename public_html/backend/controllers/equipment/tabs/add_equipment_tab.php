<?php

function read_request_add($data_request , $pdo , $user_id){
    // what queries can data specific have:
    //$data_specific = array("default" => array(),types" => array(),"groups" => array(),"users" => array(),"user" => array(),"types_specific" => array());
    if(!isset($_SESSION["group_auth"]))
        return;
    $auth_groups = $_SESSION["group_auth"]["auth"];
    if(!isset($data_request["refresh"])){
        $filter = array("filter" => array("0","1","2","3"));
        $data_specific = array("types" => array()
                              ,"groups" => array()
                              ,"default" => array()
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
        $request = array("table" => "equipment");
        $default_columns = describe_table($request , $pdo);
        $default_columns["items"] = parse_equipment_type_columns($default_columns["items"]);
        $equipment_types["items"] = clean_query($filter  , $equipment_types["items"]);
        $_SESSION["equipment_types"] = $equipment_types["items"];
        $manageable_groups["items"] = clean_query($filter  , $manageable_groups["items"]);
        $data_specific["types"] = $equipment_types;
        $data_specific["groups"] = $manageable_groups;
        $data_specific["default" ] = $default_columns;
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
}

?>
