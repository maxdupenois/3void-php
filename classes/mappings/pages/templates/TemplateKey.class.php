<?php

class TemplateKey{
	protected $name, $type, $order;
	
	public function __construct($name, $type, $order){
		$this->name = $name;
		$this->type = $type;
		$this->order = $order;
	}
	public function getName(){
		return $this->name;
	}
	public function getType(){
		return $this->type;
	}
	public function getOrder(){
		return $this->order;
	}
	
}

?>