<?php
    function read_equipment($data_request , $pdo){
        $full_info = array("user" => array() , "group" => array() , "equipment" => array() , "auth_level" => "0");
        if(validate_reference_existence($data_request["query"] , $pdo) !== 1)
            return "Equipment Reference does Not Exist";
        if(user_group_request_authentication($data_request["query"] , $pdo) !== 1)
            return "Unauthorised Query";
        $request = array("fetch" => " * "
                        ,"table" => " users_inside_groups_equipments "
                        ,"counted" => 1
                        ,"specific" => " user_id=" . $data_request["query"]["user_id"]
                                     . " AND group_id=" . $data_request["query"]["group_id"]
                                     . " AND equipment_id=" . $data_request["query"]["equipment_id"]
                        );
        $reference = get_query($request , $pdo)["items"];
        $full_info["auth_level"] = $reference["user_permission_level"];
        $request = array("fetch" => " id , users_name , username , email , phone_number , regional_indicator "
                        ,"table" => " users "
                        ,"counted" => 1
                        ,"specific" => " id=" . $reference["user_id"]
                        );
        $full_info["user"] = get_query($request , $pdo)["items"];
        $request = array("fetch" => " * "
                        ,"table" => " user_groups "
                        ,"counted" => 1
                        ,"specific" => " id=" . $reference["group_id"]
                        );
        $full_info["group"] = get_query($request , $pdo)["items"];
        $full_info["equipment"] = get_equipment(" * " , $reference["equipment_id"] , $pdo)["items"];
        return $full_info;
    }
?>
