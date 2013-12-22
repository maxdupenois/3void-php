<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include_once("admin-template-parts/admin-meta.part.php");?>
<?php include_once("admin-template-parts/admin-tinymce.part.php");?>

<script language="javascript" type="text/javascript">
/**********************
Page Specific Script
***********************/
<?php if($contents['javascript']!=NULL)$contents['javascript']->evaluate(); ?>


</script>
<script language="javascript" type="text/javascript">
/**********************
General Admin Script
***********************/
	function expandMenu(which){
		document.getElementById(which+'_expander').blur();
		document.getElementById(which+'_sub_menu')['style']['display'] = "block";
		document.getElementById(which+'_expander').className = "expander_expanded";
		document.getElementById(which+'_expander').href="javascript:collapseMenu('"+which+"');";
		
	}
	function collapseMenu(which){
		document.getElementById(which+'_expander').blur();
		document.getElementById(which+'_sub_menu')['style']['display'] = "none";
		document.getElementById(which+'_expander').className = "expander_collapsed";
		document.getElementById(which+'_expander').href="javascript:expandMenu('"+which+"');";
	}
	
	/*function createFolderExpander(index){
		return Utils.newElement("a", {"class":"folder_expander_collapsed",
			"href":"javascript:expandFolder("+index+");"});
	}
	
	var folders;
	
	function expandFolder(index){
		var folder = folders[index];
		children = folder.childNodes;
		for(var j=0; j < children.length; j++){
			child = children[j];
			if(child.className == "folder_listing"){
				child['style']['display'] = "block";
			}
			if(child.className == "folder_expander_collapsed"){
				child["href"] = "javascript:collapseFolder("+index+");";
				child["className"] = "folder_expander_expanded";
			}
		}
	}
	
	function collapseFolder(index){
		var folder = folders[index];
		children = folder.childNodes;
		for(var j=0; j < children.length; j++){
			child = children[j];
			if(child.className == "folder_listing"){
				child['style']['display'] = "none";
			}
			if(child.className == "folder_expander_expanded"){
				child["href"] = "javascript:expandFolder("+index+");";
				child["className"] = "folder_expander_collapsed";
			}
		}
	}
	
	window.onload = function(){
		if(document.getElementById('pages')!=null){
			folders = Utils.getElementsByClassName(document.getElementById('pages'), 'folder');
			
			var folder;
			var children;
			var child;
			for(var i= 0; i < folders.length; i++){
				folder = folders[i];
				children = folder.childNodes;
				folder.insertBefore(createFolderExpander(i), children[0]);
    			for(var j=0; j < children.length; j++){
					child = children[j];
					if(child.className == "folder_listing"){
						child['style']['display'] = "none";
					}
				}
			}
		}
	};
	*/

</script>
</head>

<body>
<div id="page">

    <?php include('admin-template-parts/admin-header.part.php'); ?>

	<div id="content">
		
			<?php include('admin-template-parts/admin-menu.part.php'); ?>
		
		<div id="main">
			<?php include('admin-template-parts/admin-formresult.part.php'); ?>
			
			
			
			
			<?php if($contents['main']!=NULL)$contents['main']->evaluate(); ?>
			
			
			
			
			<?php /*
			<form name="menu_form" action="/admin/actions/create-menu-action.<?=$GLOBALS['ACTION_EXTENSION']?>" method="post">
			<div style="width:200px;">
				<ul id="pages" class="page_listing_menu">
				<?php $pageMappings = Page::pageListingByFolder();
				
				function listFolderForMenu($folder){
					$html = "";
					foreach($folder as $k=>$val){
						if(is_array($val)){
							$html .= "<li class=\"folder_menu\">\n";
							$html .= "<a href=\"javascript:void(0);\" title=\"$k\" class=\"menu_item menu_item_off\">\n";
							$html .= "<img class=\"folder_img\" border=\"0\"/>$k";
							$html .= "</a>\n";
							$html .= "<ul class=\"folder_listing_menu\">\n";
								$html .= listFolderForMenu($val);
							$html .= "</ul></li>\n";
						}else{
							$html .= "<li>\n";  
							$html .= "<a href=\"javascript:void(0);\" title=\"{$val->getTitle()}\" class=\"menu_item menu_item_off\">".$val->getTitle()."</a>\n";
							$html .= "</li>\n";
						}
					}
					return $html;
				}
				echo listFolderForMenu($pageMappings);
				?>
				
				</ul>
			</div>
			</form>		
			*/ ?>
		</div>
		
		<div class="clear"><!--clear--></div>
	</div>
</div>

</body>
</html>