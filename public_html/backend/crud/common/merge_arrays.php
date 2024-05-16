<?php
function merge_array($arrays){
    $combined = array();
    $j = 0;
    foreach($arrays as $part){
        $i = 1;
        foreach($part as $key => $info){
            if($i%2 !== 0){
                $combined[$key] = $info;
            }
            $i++;
        }
    }
    return $combined;
}
?>