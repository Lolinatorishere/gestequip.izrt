<?php

function read_table_description($data_request , $pdo){
    $full_data = array("default" => array(), "specific" => array());
    $request = array("table" => " user_groups ");
    $default = describe_table($request , $pdo);
    $full_data["default"] = parse_equipment_type_columns($default["items"]);
    return $full_data;
}

?>
