<?php
function renderTime(){
    $start = $GLOBALS["RENDER_START"];
    return round(microtime(true)-$start, 2);
}
?>