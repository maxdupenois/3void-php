<?php

class PageContent{
	protected $keyname, $content;
	
	public function __construct($keyname, $content){
		$this->keyname = $keyname;
		$this->content = $content;
	}
	public function getKeyname(){
		return $this->keyname;
	}
	public function getContent(){
		return $this->content;
	}
	
}

?>