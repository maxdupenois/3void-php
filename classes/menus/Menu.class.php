<?php

class Menu{
	protected $name, $title, $items, $permission_view, 
	$permission_edit, $permission_delete;
	
	public function __construct($name, $title, $permission_view, 
								$permission_edit, $permission_delete){
		$this->name = $name;
		$this->title = $title;
		$this->permission_view = $permission_view;
		$this->permission_edit = $permission_edit;
		$this->permission_delete = $permission_delete;
	}
	public function getName(){
		return $this->name;
	}
	public function getTitle(){
		return $this->title;
	}
	public function setItems($items){
		$this->items = $items;
	}
	public function getItems(){
		return $this->items;
	}
	public function getPermissionView(){
		return $this->permission_view;
	}
	public function getPermissionEdit(){
		return $this->permission_edit;
	}
	
	public function getPermissionDelete(){
		return $this->permission_delete;
	}
	public static function getMenu($name, $purpose="view"){
		$db = $GLOBALS['DATABASE'];
		$connection = $db->getConnection();
		$sql = "SELECT `title`, `permission-view` AS `view`, `permission-edit` AS `edit`, `permission-delete` AS `delete` "
			  ."FROM `menus` "
			  ."WHERE name = '$name'";
		$result = mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
		$title = "";
		$permission_view = "";
		$permission_edit = "";
		$permission_delete = "";
		if($m = mysql_fetch_object($result)){
			$title = $m->title;
			$permission_view = $m->view;
			$permission_edit = $m->edit;
			$permission_delete = $m->delete;
		}
		mysql_free_result($result);
		
		$sql = "SELECT `item_id`, `href`, `title`, `contents`, `internal`, `order` "
			  ."FROM `menuitems` "
			  ."WHERE menu = '$name' "
			  ."ORDER BY `order` ASC";
		$result = mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
		$items = array();
		while($i = mysql_fetch_object($result)){
			$items[$i->order] = new MenuItem($i->item_id, $i->href, $i->title, $i->contents, $i->internal, $i->order);
		}
		mysql_free_result($result);
		
		//Initialise submenus so no sql calls are needed during the page process
		foreach($items as $item) $item->getSubMenus();
		
		$menu = new Menu($name, $title, $permission_view, $permission_edit, $permission_delete);
		$menu->setItems($items);
		
		return (Authorisation::authoriseMenu($menu, $purpose)?$menu:NULL);
	}
	
	public static function menuListing(){
		$listing = array();
		if($userid == NULL){
			$userid = $_SESSION['userid'];
			if($userid == NULL || $userid == "")return $listing;
		}
		$db = $GLOBALS['DATABASE'];
		$connection = $db->getConnection();
	
	
		$sql = "SELECT m.name, m.title, m.`permission-view` AS p_view, "
				."m.`permission-edit` AS p_edit, m.`permission-delete` AS p_delete "
				."FROM menus AS m "
				."WHERE ( "
					."m.`permission-view` = \"\" "
					."OR ( "
						."m.`permission-view` "
						."IN ( "
							."SELECT agp.permission "
							."FROM `access-groups-permissions` AS agp "
							."LEFT JOIN users AS u ON u.group = agp.group "
							."WHERE u.id = '$userid' "
						.") "
						."AND m.`permission-view` NOT "
						."IN ( "
							."SELECT up.permission "
							."FROM `user-permissions` AS up "
							."WHERE up.accept =0 "
							."AND up.userid = '$userid' "
						.") "
						."OR m.`permission-view` "
						."IN ( "
							."SELECT up.permission "
							."FROM `user-permissions` AS up "
							."WHERE up.accept =1 "
							."AND up.userid = '$userid' "
						.") "
					.") "
				.") ORDER BY m.title";
		$result = mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
		while($m = mysql_fetch_object($result)){
			$menu = new Menu($m->name, $m->title, $m->p_view, $m->p_edit, $m->p_delete);
			$listing[] = $menu;
		}
		return $listing;
	}
}

?>