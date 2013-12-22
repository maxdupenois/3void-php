<?php import("utils.RssReader");?>
<h3 class="gcode_header">Recent framework code updates:</h3>
<p>List of all the most recent subversion updates to the code for
    the <a href="http://code.google.com/p/dot-pattern-change-identifier-testing-framework/"
            title="Change identifer framework">Change Identifer Framework</a>
</p>
<ul id="gcode_list">
    <?php
    function gcodeStart($startElement, $elementList, $currentTitle){
        switch($startElement["name"]){
            case "ENTRY":
                return "<li class=\"gcode_entry\">";
            case "TITLE":
                if(RssReader::isElementIn("ENTRY", $elementList)){
                    return "<span class=\"gcode_title\">";
                }else{
                    return "";
                }
            case "UPDATED":
                if(RssReader::isElementIn("ENTRY", $elementList)){
                    return "<span class=\"gcode_updated\">";
                }else{
                    return "";
                }
            default:
                return "";
        }
    }
    function gcodeData($data, $currentElement, $elementList, $currentTitle){
        switch($currentElement["name"]){
            case "UPDATED":
                if(RssReader::isElementIn("ENTRY", $elementList)){
                    //Mon, 01 Feb 2010 16:23:37 +0000
                    //$format = '%a, %d %b %Y %H:%M:%S';
                    //2010-10-29T14:11:02Z
                    $format = '%FT%H:%M:%SZ';
                    $dateArr=strptime($data, $format);
                    $date = mktime($dateArr['tm_hour'], $dateArr['tm_min'],
                        $dateArr['tm_sec'], $dateArr['tm_mon']+1,
                        $dateArr['tm_mday'], 1900+$dateArr['tm_year']);

                    return date("H:i d F, Y", $date);
                }else{
                    return "";
                }
            case "TITLE":
                if(RssReader::isElementIn("ENTRY", $elementList)){
                    return $data;
                }else{
                    return "";
                }
            default:
                return "";
        }
    }
    function gcodeEnd($endingElement, $elementList, $currentTitle){
        switch($endingElement["name"]){
            case "ENTRY":
                return "</li>";
            case "TITLE":
                if(RssReader::isElementIn("ENTRY", $elementList)){
                    return "</span>";
                }else{
                    return "";
                }
            case "UPDATED":
                if(RssReader::isElementIn("ENTRY", $elementList)){
                    return "</span>";
                }else{
                    return "";
                }
            default:
                return "";
        }
    }


    $reader = new RssReader("http://code.google.com/feeds/p/dot-pattern-change-identifier-testing-framework/svnchanges/basic", false);
    $reader->setElementDisplay("ENTRY", "gcodeStart","gcodeEnd", "gcodeData");
    $reader->setElementDisplay("TITLE", "gcodeStart","gcodeEnd", "gcodeData");
    $reader->setElementDisplay("UPDATED", "gcodeStart","gcodeEnd", "gcodeData");
    echo $reader->parse();
    ?>
</ul>
