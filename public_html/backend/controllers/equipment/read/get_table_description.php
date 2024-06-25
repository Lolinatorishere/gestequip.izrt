<?php

function read_table_description($data_request , $pdo){
    $full_data = array("default" => array(), "specific" => array());
    $request = array("table" => " equipment ");
    $default = describe_table($request , $pdo);
    $full_data["default"] = parse_equipment_type_columns($default["items"]);
    $equipment_type = get_equipment_type($data_request["query"]["equipment_type"] , $pdo , "name");
    if($equipment_type === "error")
        return "Invalid Equipment Type";
    $request = array("table" => $equipment_type);
    $specific = describe_table($request , $pdo);
    $full_data["specific"] = parse_equipment_type_columns($specific["items"]);
    return $full_data;
}

?>
