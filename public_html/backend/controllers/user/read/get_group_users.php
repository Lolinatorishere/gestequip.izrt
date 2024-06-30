<?php

function read_group_users($data_request , $pdo){
    if(empty($_SESSION["id"])
        return "Unauthorised Request";
    if(!isset($data_request["query"]["group_id"]))
        return "Invalid Group Request";
    if($data_request["query"]["group_id"] === "1")
        return "Invalid Group Request";
    $user_groups = get_auth_groups($pdo)["all_groups"];
    $guard = 0;
    foreach($user_groups as $key => $auth){
        if($data_request["query"]["group_id"] == $auth){
            $guard = 1;
            break;
        }
    }
    if($guard !== 1){
        return "Invalid Group Member";
    }
    $request = array("fetch" => " * "
                    ,"table" => " users_inside_groups "
                    ,"specific" => "group_id= " . $data_request["query"]["group_id"] . " AND user_id > 1"
                    );
    if(isset($data_request["paging"])){
        $request["paging"] = $data_request["paging"];
    }
    if(isset($data_request["page"])){
        $request["page"] = $data_request["page"];
    }
    $users = get_users($request , $pdo);
    return $users;
}
?>

