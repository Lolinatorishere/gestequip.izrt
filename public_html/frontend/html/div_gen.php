<?php
function make_div($div_conf , $div_content){
    $ret = ("
        <div
        $div_conf
        >
            $div_content
        </div>
    ");
    return $ret;
}
?>