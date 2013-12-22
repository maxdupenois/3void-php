<?php

class TemplateContentKey{
	protected $name, $type, $order, $description;
	
	public function __construct($name, $type, $order, $description){
		$this->name = $name;
		$this->type = $type;
		$this->order = $order;
		$this->description = $description;
	}
	public function getName(){
		return $this->name;
	}
	public function getNameAsTitle(){
		$length = strlen($this->name);
		$title = "";
		for($i = 0; $i < $length; $i++){
			if(preg_match("/[A-Z]/", $this->name[$i])>0){
				if($i != 0)$title .= " ";
			}
			$title .= $this->name[$i];
		}
		return ucwords($title);
	}
	public function getType(){
		return $this->type;
	}
	public function getDescription(){
		return $this->description;
	}
	public function getOrder(){
		return $this->order;
	}
	
}

?>