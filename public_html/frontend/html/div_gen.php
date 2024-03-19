<?php
function make_div($div_class , $div_content){
    $ret = ("
        <div class=\"$div_class\"");
        if(isset($div_add_attr))
            $ret .= ("$div_add_attr");
        $ret .= (">");
        $ret .= ("
            $div_content
        </div>
    ");
    return $ret;
}
?>