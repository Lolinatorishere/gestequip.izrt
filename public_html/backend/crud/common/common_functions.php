<?php
function page_check(&$request){
    $pages = 0;
    if(!isset($request["total_pages"]))
        $request["total_pages"] = 1;
    if(!isset($request["page"]))
        $request["page"] = 1;
    if($pages <= 0)
        $pages = 1;
    return;
}

?>