<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Listingclass
 *
 * @author Max
 */
class Listing {
    public static function listingByFolder($listing, $location_func, $splitter="/"){
		$actions = array();
		$listingIndex = 0;
		foreach($listing as $item){
			$location = eval('return $item->'.$location_func.';');
			$parts = explode($splitter, $location);
			$i = 0;
			$finalPartIndex = sizeof($parts)-1;
			$dynamicString = '$folders';
			foreach($parts as $p){
				if($i != $finalPartIndex){
					$dynamicString .= "['$p']";
				}else{
					$dynamicString .= '[]=$listing['.$listingIndex.'];';
				}
				$i++;
			}
			eval($dynamicString);
			$listingIndex++;
		}
		$folders = Listing::reorderFolders($folders);
		return $folders;
	}
    public static function reorderFolders($tree){
		$folders = array();
		$pages = array();
		foreach($tree as $k=>$val){
			if(is_array($val)){
				$folders[$k] = Listing::reorderFolders($val);
			}else{
				$pages[] = $val;
			}
		}
		$reordered = array();
		foreach($folders as $f=>$a) $reordered[$f] = $a;
		foreach($pages as $p) $reordered[] = $p;
		return $reordered;
	}
}
?>
