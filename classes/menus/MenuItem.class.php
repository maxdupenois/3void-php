<?php
class MenuItem{
	protected $id, $href, $title, $contents, $internal, $order, $subMenus;
	private $gotSubMenus;
	public function __construct($id, $href, $title, $contents, $internal, $order){
		$this->id = $id;
		$this->href = $href;
		$this->title = $title;
		$this->contents = $contents;
		$this->internal = $internal;
		$this->order = $order;
		$this->gotSubMenus = false;
	}
	public function getId(){
		return $this->id;
	}
	public function getHref(){
		return $this->href;
	}
	public function getTitle(){
		return $this->title;
	}
	public function getContents(){
		return $this->contents;
	}
	public function isInternal(){
		return $this->internal;
	}
	public function getOrder(){
		return $this->order;
	}
	public function getSubMenus(){
		if(!$this->gotSubMenus) $this->retrieveSubMenus();
		return $this->subMenus;
	}
	public function __toString(){
		return "<a href=\"".$this->href."\ title=\"".$this->title."\">".$this->contents."</a>";
	}
	
	private function retrieveSubMenus(){
		$db = $GLOBALS['DATABASE'];
		$connection = $db->getConnection();
		$sql = "SELECT `menu_name` AS `name` FROM `menuitems-submenus` WHERE item_id = '".$this->id."'";
		$result = mysql_query($sql, $connection) or die(mysql_error());
		$menunames = array();
		while($k = mysql_fetch_object($result)){
			$menunames[] = $k->name;
		}
		mysql_free_result($result);
		
		$this->subMenus = array();
		foreach($menunames as $name){
			$this->subMenus[$name] = Menu::getMenu($name);
		}
		
		$this->gotSubMenus = true;
	
	}
	
}
?>