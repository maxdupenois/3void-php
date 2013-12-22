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
		return DOMUtils.newElement("a", {"class":"folder_expander_collapsed",
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
			folders = DOMUtils.getElementsByClassName(document.getElementById('pages'), 'folder');

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