<?php

function read_equipment_types($pdo){
    $request = array("fetch" => " * "
                    ,"table" => " equipment_types "
                    );
    return get_queries($request , $pdo);
}

?>
