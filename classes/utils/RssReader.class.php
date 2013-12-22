<?php
class RssReaderElement{
    private $name;
    private $start;
    private $end;
    private $data;
    
    public function __construct($name, $start, $end, $data){
        $this->name = $name;
        $this->start = $start;
        $this->end = $end;
        $this->data = $data;
    }
    public function start(){
        return $this->start;
    }
    public function data(){
        return $this->data;
    }
    public function end(){
        return $this->end;
    }
}
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RssReaderclass
 *
 * @author Max
 */

class RssReader {
    private $parser;
    private $url;
    private $html;
    private $elementList;
    private $currentElement;
    private $currentTitle;
    private $nameToElementList = array();
    private static $parserToReader = array();
    public function __construct($url, $usedefaults = true){
        $this->url = $url;
        $this->usedefaults = $usedefaults;
        $this->parser = xml_parser_create();
        xml_set_element_handler($this->parser, "RssReader::startElementHandler",
                                               "RssReader::endElementHandler");
        xml_set_character_data_handler($this->parser,
                                               "RssReader::characterDataHandler");

        RssReader::$parserToReader[$this->parser] = $this;
        if($this->usedefaults){
            $this->setElementDisplay("CHANNEL",
                    "RssReader::defaultStart",
                    "RssReader::defaultEnd");
            $this->setElementDisplay("ITEM",
                    "RssReader::defaultStart",
                    "RssReader::defaultEnd");
            $this->setElementDisplay("TITLE",
                    "RssReader::defaultStart",
                    "RssReader::defaultEnd");
            $this->setElementDisplay("LINK",
                    "RssReader::defaultStart",
                    "RssReader::defaultEnd");
            $this->setElementDisplay("DESCRIPTION",
                    "RssReader::defaultStart",
                    "RssReader::defaultEnd");
            $this->setElementDisplay("PUBDATE",
                    "RssReader::defaultStart",
                    "RssReader::defaultEnd");
        }

    }

    public static function defaultStart($startElement, $elementList, $currentTitle){
        
        switch($startElement["name"]){
            case "CHANNEL":
                return "<div class=\"rss_reader_channel\">";
            case "ITEM":
                return "<div class=\"rss_reader_item\">";
            case "TITLE":
                return "<h2 class=\"rss_reader_title\">";
            case "DESCRIPTION":
                return "<div class=\"rss_reader_description\">";
            case "PUBDATE":
                return "<span class=\"rss_reader_pubdate\">";
            case "LINK":
                return "<a class=\"rss_reader_link\" ";
            default:
                return "";
        }
    }
    public static function defaultData($data, $currentElement, $elementList, $currentTitle){
        switch($currentElement["name"]){
            case "CHANNEL":
            case "ITEM":
            case "TITLE":
            case "DESCRIPTION":
            case "PUBDATE":
                return $data;
            case "LINK":
                return "title=\"".$data."\" href=\"".$data."\">".
                ($currentTitle==null?$data:$currentTitle);
            default:
                return "";
        }
    }
    public static function defaultEnd($endingElement, $elementList, $currentTitle){
        switch($endingElement["name"]){
            case "CHANNEL":
            case "ITEM":
            case "DESCRIPTION":
                return "</div>";
            case "TITLE":
                return "</h2>";
            case "PUBDATE":
                return "</span>";
            case "LINK":
                return "</a>";
            default:
                return "";
        }
    }
    public function setElementDisplay($name, $start, $end, 
                            $data="RssReader::defaultData"){
        $this->nameToElementList[$name] = new RssReaderElement($name, $start,
                $end, $data);
    }
    public function __destruct(){
        xml_parser_free($this->parser);
    }
    public function parse(){
        $this->setHTML("<div class=\"rss_reader\">");
        $this->elementList = array();
        $this->currentElement = null;
        if (!($fp = fopen($this->url, "rb"))) {
            $this->error("could not open XML input");
        }else{
            $xmlFile = stream_get_contents($fp);
            fclose($fp);
            if (!xml_parse($this->parser, $xmlFile, true)) {
                $this->error("XML error: '".
                    xml_error_string(xml_get_error_code($this->parser))."' ".
                    "[".xml_get_current_line_number($this->parser)."]");
            }
            //Version that doesn't preload:
            /*
            while (!feof($fp)) {
                $data = fread($fp, 8192);
                if (!xml_parse($this->parser, $data, feof($fp))) {
                    $this->error("XML error: '".
                        xml_error_string(xml_get_error_code($this->parser))."' ".
                        "[".xml_get_current_line_number($this->parser)."]");
                }
            }
            fclose($fp);
            */
        }
        $this->closeOpenElements();
        $this->appendHTML("</div>");
        return $this->html;
    }

    private function startElement($name, $attributes){
        $this->elementList[] = array("name"=>$name, "attributes"=>$attributes);
        $this->currentElement = array("name"=>$name, "attributes"=>$attributes);
        $function = $this->getStart($name);
        if($function=="RssReader::defaultStart"&&!$this->usedefaults) return;
        $this->appendHTML(call_user_func($function,
                $this->currentElement, $this->elementList, $this->currentTitle));
    }
    private function characterData($data){
        if($this->currentElement["name"]=="TITLE") $this->currentTitle = $data;
        $function = $this->getData($this->currentElement["name"]);
        if($function=="RssReader::defaultData"&&!$this->usedefaults) return;
        $this->appendHTML(call_user_func($function, $data,
                $this->currentElement, $this->elementList, $this->currentTitle));
    }
    private function endElement($name){
        $endingElement = array_pop($this->elementList);
        if(count($this->elementList)>0){
            $this->currentElement = $this->elementList[count($this->elementList)-1];
        }
        $function = $this->getEnd($name);
        if($function=="RssReader::defaultEnd"&&!$this->usedefaults) return;
        $this->appendHTML(call_user_func($function,
                $endingElement, $this->elementList, $this->currentTitle));
    }
    public function getStart($name){
        if(!isset($this->nameToElementList[$name])) {
            return "RssReader::defaultStart";
        }
        $element = $this->nameToElementList[$name];
        return $element->start();
    }

    public function getEnd($name){
        if(!isset($this->nameToElementList[$name]))return "RssReader::defaultEnd";
        
        $element = $this->nameToElementList[$name];
        return $element->end();
    }
    public function getData($name){
        if(!isset($this->nameToElementList[$name])) return "RssReader::defaultData";
        $element = $this->nameToElementList[$name];
        return $element->data();
    }
    private function closeOpenElements(){
        while(count($this->elementList)>0){
            $el = array_pop($this->elementList);
            $this->endElement($el['name']);
        }
    }
    private function error($error){
        $this->appendHTML("<div class=\"rss_reader_error\">");
        $this->appendHTML($error);
        $this->appendHTML("</div>");
    }

    private function setHTML($text){
        $this->html = $text;
    }

    private function appendHTML($text){
        if($this->html == null){
            $this->html = $text;
        }else{
            $this->html .= $text;
        }
    }

    public static function startElementHandler($parser, $name, $attributes){
        $reader = RssReader::$parserToReader[$parser];
        $reader->startElement(strtoupper($name), $attributes);
    }
    public static function endElementHandler($parser, $name){
        $reader = RssReader::$parserToReader[$parser];
        $reader->endElement(strtoupper($name));
    }
    public static function characterDataHandler($parser, $data){
        $reader = RssReader::$parserToReader[$parser];
        $reader->characterData($data);
    }
    public static function isElementIn($name, $elementList){
        $found = false;
        for($i =0, $max = count($elementList); $i < $max && !$found; $i++){
            $found = ($name==$elementList[$i]["name"]);
        }
        return $found;
    }
}
?>