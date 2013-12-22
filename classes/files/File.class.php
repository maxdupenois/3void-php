<?php
import("utils.FileUtils");
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of uploadsclass
 *
 * @author mpd209
 */
 import("utils.MysqlUtils");
class File {
    private $id,
            $location,
            $uri,
            $contentType,
            $size,
            $updated,
            $uploaded,
            $uploadedBy,
            $updatedBy,
            $permissionView,
            $permissionEdit,
            $permissionDelete;

    public function __construct(){
    }
    

   

    public function stream(){
        if(!Authorisation::authoriseFile($this, "view")){
            Page::unauthorised($GLOBALS['UPLOADS_BASE']."/".$this->uri);
        }
        if(file_exists($this->location)){
            $data = FileUtils::readBinary($this->location);
            header("Content-length: ".$this->size);
            header("Content-type: ".$this->contentType);
            echo $data;
            exit();
        }else{
            Page::pageNotFound();
        }
    }
    public static function streamFile($uri){
        $sql = "SELECT * FROM `files` WHERE uri = '$uri'";
        $file = File::internalGetFile($sql);
        if($file==null){
            Page::pageNotFound();
        }
        $file->stream();
    }
    public static function uploadFile($templocation, $uri, $permissionView, $permissionEdit, $permissionDelete){
        if(!Authorisation::isAuthorised("admin.file.upload")){
			$GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to upload a file");
			return null;
		}
        $uriFolderPath = dirname($uri);
        $destination = $GLOBALS['UPLOADS_FOLDER']."/".$uri;
        $destination = FileUtils::getUniqueFileName($destination);
        $directory = dirname($destination);
        //Need to get possible altered filename,
        $filename = basename($destination);
        $uri = ($uriFolderPath!="."?$uriFolderPath."/".$filename:$filename);
 
        if(!FileUtils::checkDir($directory)){
			$GLOBALS['FORM_RESULTS']->addErr("directory", "Cannot create directory '".$directory."'");
			return null;
        }
        if(!move_uploaded_file($templocation, $destination)){
			$GLOBALS['FORM_RESULTS']->addErr("move", "Cannot move file to '".$uri."'");
			return null;
        }
        $contentType = FileUtils::getAppropriateContentType($uri);
        $size = filesize($destination);
        $db = $GLOBALS['DATABASE'];
		$connection = $db->getConnection();
        $time = time();
        $sql = "INSERT INTO `files` (uri, location, `content-type`, size, updated, uploaded, "
                ."`uploaded-by`, `updated-by`, `permission-view`, "
                ."`permission-delete`,`permission-edit`) VALUES ("
                ."\"".mysql_real_escape_string($uri)."\", "
                ."\"".mysql_real_escape_string($destination)."\", "
                ."\"".$contentType."\", "
                .$size.", "
                ."\"".MysqlUtils::unixToMysql($time)."\", "
                ."\"".MysqlUtils::unixToMysql($time)."\", "
                ."\"".Authorisation::currentUserId()."\", "
                ."\"".Authorisation::currentUserId()."\", "
                ."\"".mysql_real_escape_string($permissionView)."\", "
                ."\"".mysql_real_escape_string($permissionEdit)."\", "
                ."\"".mysql_real_escape_string($permissionDelete)."\" "
                .")";

       mysql_query($sql, $connection) or die($sql."<hr/>".mysql_error());
       return File::getFileByUri($uri);
    }
    public function delete(){
		if(!Authorisation::isAuthorised($this->getPermissionDelete())){
			$GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to do this");
			return false;
		}
		$db = $GLOBALS['DATABASE'];
		$connection = $db->getConnection();
		$sql = "DELETE FROM `files` WHERE `id`='".$this->getId()."'";
		$result = mysql_query($sql, $connection);
        if(!$result){
            $GLOBALS['FORM_RESULTS']->addErr("sql",
            $sql."<hr/>".mysql_error());
            return false;
        }
		return true;
	}
    public static function getFileFolderListing(){
        return File::listingByFolder(File::getFiles());
    }
    public static function getFiles($userid=NULL){
        $files = array();
        if($userid == NULL){
            if(!isset($_SESSION['userid'])) return $files;
            $userid = $_SESSION['userid'];
            if($userid == NULL || $userid == "")return $files;
        }
        $sql = "SELECT f.* "
				."FROM files AS f "
				."WHERE "
				."( "
					."f.`permission-view` = \"\" "
					."OR ( "
						."f.`permission-view` "
						."IN ( "
							."SELECT agp.permission "
							."FROM `access-groups-permissions` AS agp "
							."LEFT JOIN users AS u ON u.group = agp.group "
							."WHERE u.id = '$userid' "
						.") "
						."AND f.`permission-view` NOT "
						."IN ( "
							."SELECT up.permission "
							."FROM `user-permissions` AS up "
							."WHERE up.accept =0 "
							."AND up.userid = '$userid' "
						.") "
						."OR f.`permission-view` "
						."IN ( "
							."SELECT up.permission "
							."FROM `user-permissions` AS up "
							."WHERE up.accept =1 "
							."AND up.userid = '$userid' "
						.") "
					.") "
				.") ORDER BY f.uri";
        $files = File::internalGetFile($sql, false);
        return $files;
    }

    public static function getFile($id, $purpose="view"){
        
        $sql = "SELECT * FROM `files` WHERE id = '$id'";
        $file = File::internalGetFile($sql);
        if($file==null) return null;
        if(!Authorisation::authoriseFile($file, $purpose)) {
             $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to use this file for that purpose");
            return null;
        }
        return $file;

    }
    public static function getFileByUri($uri, $purpose="view"){
        $sql = "SELECT * FROM `files` WHERE uri = '$uri'";
        $file = File::internalGetFile($sql);
        if($file==null) return null;
        if(!Authorisation::authoriseFile($file, $purpose)) return null;
        return $file;

    }
    private static function internalGetFile($sql, $single=true){
        $db = $GLOBALS['DATABASE'];
		$connection = $db->getConnection();
		$result = mysql_query($sql, $connection) or die(mysql_error());
            
        $files = array();
		while($fileDetails = mysql_fetch_object($result)) {
            $file = new File();
            $file->setId($fileDetails->id);
            $file->setUri($fileDetails->uri);
            $file->setLocation($fileDetails->location);
            $file->setContentType($fileDetails->{'content-type'});
            $file->setSize($fileDetails->size);
            $file->setUpdated(MysqlUtils::mysqlToUnix($fileDetails->updated));
            $file->setUploaded(MysqlUtils::mysqlToUnix($fileDetails->uploaded));
            $file->setUploadedBy(User::getUser($fileDetails->{'uploaded-by'}));
            $file->setUpdatedBy(User::getUser($fileDetails->{'updated-by'}));
            $file->setPermissionView($fileDetails->{'permission-view'});
            $file->setPermissionEdit($fileDetails->{'permission-edit'});
            $file->setPermissionDelete($fileDetails->{'permission-delete'});
            $files[] = $file;
        }
		mysql_free_result($result);
        if($single){
            return (count($files)>0?$files[0]:null);
        }
        return $files;
        
    }

    public static function listingByFolder($mappings){
		$folders = array();
		$mappingIndex = 0;
		foreach($mappings as $mapping){
			$uri = $mapping->getURI();
			$parts = explode("/", $uri);
			$i = 0;
			$finalPartIndex = sizeof($parts)-1;
			$dynamicString = '$folders';
			foreach($parts as $p){

				if($i != $finalPartIndex){
					$dynamicString .= "['$p']";
				}else{
					$dynamicString .= '[]=$mappings['.$mappingIndex.'];';
				}
				$i++;
			}
			eval($dynamicString);
			$mappingIndex++;
		}
		$folders = File::reorderFolders($folders);
		return $folders;
	}
	public static function reorderFolders($tree){
		$folders = array();
		$pages = array();
		foreach($tree as $k=>$val){
			if(is_array($val)){
				$folders[$k] = File::reorderFolders($val);
			}else{
				$pages[] = $val;
			}
		}
		$reordered = array();
		foreach($folders as $f=>$a) $reordered[$f] = $a;
		foreach($pages as $p) $reordered[] = $p;

		return $reordered;
	}


    /*Getters*/
    public function getId(){
        return $this->id;
    }
    public function getUri(){
        return $this->uri;
    }
    public function getLocation(){
        return $this->location;
    }
    public function getContentType(){
        return $this->contentType;
    }
    public function getSize(){
        return $this->size;
    }
    public function getUpdated(){
        return $this->updated;
    }
    public function getUploaded(){
        return $this->uploaded;
    }
    public function getUploadedBy(){
        return $this->uploadedBy;
    }
    public function getUpdatedBy(){
        return $this->updatedBy;
    }
    public function getPermissionView(){
        return $this->permissionView;
    }
    public function getPermissionEdit(){
        return $this->permissionEdit;
    }
    public function getPermissionDelete(){
        return $this->permissionDelete;
    }


    /*Setters*/
    public function setId($id){
        $this->id = $id;
    }
    public function setUri($uri){
        $this->uri = $uri;
    }
    public function setLocation($location){
        $this->location = $location;
    }
    public function setContentType($contentType){
        $this->contentType = $contentType;
    }
    public function setSize($size){
        $this->size = $size;
    }
    public function setUpdated($updated){
        $this->updated = $updated;
    }
    public function setUploaded($uploaded){
        $this->uploaded = $uploaded;
    }
    public function setUploadedBy($uploadedBy){
        $this->uploadedBy = $uploadedBy;
    }
    public function setUpdatedBy($updatedBy){
        $this->updatedBy = $updatedBy;
    }
    public function setPermissionView($permissionView){
        $this->permissionView = $permissionView;
    }
    public function setPermissionEdit($permissionEdit){
        $this->permissionEdit = $permissionEdit;
    }
    public function setPermissionDelete($permissionDelete){
        $this->permissionDelete = $permissionDelete;
    }
}
