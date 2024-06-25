<?php

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
                $us_info = array();
                foreach ($user as $key => $value) {
                    if($key === "id"){
                        $us_info["user_id"] = $value;
                        continue;
                    }
                    $us_info[$key] = $value;
                }
                array_push($data , $us_info);
            }
        }
        array_push($data , $gp);
        foreach($groups["items"] as $group){
            if($group["id"] === $usr_grp_eq["group_id"]){
                $gp_info = array();
                foreach ($group as $key => $value) { if($key === "id"){
                        $gp_info["group_id"] = $value;
                        continue;
                    }
                    $gp_info[$key] = $value;
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

function read_request_grp($data_request , $pdo , $user_id){
    $data_request["requests"] = array();
    $user_group_info = $_SESSION["group_auth"];
    // turn the ids from the fetched groups into a string that 
    // can be read by mysql
    $group_ids = "";
    for($i = 0 ; $i < $user_group_info["total_items"] ; $i++){
        $group_ids .= $user_group_info["all_groups"][$i];
        if($i !== $user_group_info["total_items"]-1)
            $group_ids .= ', ';
    }
    // user query to check the auth level of each group to dissalow
    // non auth users from seeing all the equipment in groups that arent
    // their own
    if(is_user_in_groups($user_group_info) === 1)
        return;
    $request = array("fetch" => " distinct user_id , group_id "
                    ,"table" => "users_inside_groups"
                    ,"specific" => user_group_sql_query_metacode($user_group_info , $user_id , " OR ")
                    ,"counted" => 1
                    );
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
    // get the users in the groups
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
    $request = array("fetch" => " * "
                    ,"table" => " equipment_types "
                    ,"counted" => 1
                    );
    $equipment_types = get_queries($request , $pdo);
    for($i = 0 ; $i < count($equipment_types) ; $i++){
        $table = array("users_inside_groups_equipments" , "equipment" , $equipment_types["items"][$i]["equipment_type"]);
        $request = array("fetch" => " * "
                        ,"table" => $table
                        ,"values" => array("equipment_id" , "id" , "equipment_id")
                        ,"specific" => " users_inside_groups_equipments.user_id = " . $user_id . " AND group_id > 1 "
                        );
        array_push($data_request["requests"] , $request); 
    }

     // Main query for items
    $data_request["fetch"] = " * ";
    $data_request["table"] = " users_inside_groups_equipments ";
    $data_request["specific"] = equipment_sql_query_metacode($equipment_groups_users_info["items"]);
    $equipments_info = get_equipments($data_request , $pdo);
    $request = array("fetch" => " * "
                    ,"table" => " equipment_types"
                    ,"counted" => 1
                    );
    // Hydrate the request with aditional information to be sent to the frontend
    $group_equipments = full_group_equipment_user_data($links , $equipments_info , $users_info , $groups_info);

    $data_specific = array("group_equipments" => $group_equipments
                          ,"equipment_types" => $equipment_types
                          );
    return $data_specific;
}

?>
