<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Map{
    private $keyArray;
    private $valueArray;
    private $indexCount;
    public function __construct(){
        $this->keyArray = array();
        $this->valueArray = array();
    }
    public function size(){
        return count($this->keyArray);
    }
    public function put($key, $value){
        $this->indexCount++;
        $this->keyArray[$this->indexCount] = $key;
        $this->valueArray[$this->indexCount] = $value;
    }
    public function get($key){
        $index = null;
        foreach($this->keyArray as $i => $k){
            if($k == $key){
                $index = $i;
                break;
            }
        }
        if($index == null){
            return $this->valueArray[$index];
        }
        return null;
    }
}
?>