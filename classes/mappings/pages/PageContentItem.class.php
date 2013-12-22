<?php

class PageContentItem{
	protected $key, $content;
	
	public function __construct($key, $content){
		$this->key = $key;
		$this->content = $content;
	}
	public function getKey(){
		return $this->key;
	}
	public function getContent(){
		return $this->content;
	}
	public function evaluate(){
		$timestamp = str_replace(" ", "",microtime());
        $path = temp($timestamp.".txt");
		$handle = fopen($path, "w");
		fwrite($handle, $this->content);
		fclose($handle);
		include($path);
		unlink($path);
	}
	public function __toString(){
		return $this->content;
	}
}

?>