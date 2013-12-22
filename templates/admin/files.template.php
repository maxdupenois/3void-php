<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include_once("admin-template-parts/admin-meta.part.php");?>


<link rel="stylesheet" href="/css/admin/file-upload.css" type="text/css" />
<link rel="stylesheet" href="/css/admin/file-listing.css" type="text/css" />

<script language="javascript" type="text/javascript" src="/js/classes/IFrameUploader.js" >
</script>

<?php require_once("admin-template-parts/admin-menujs.part.php");?>
<?php require_once("admin-template-parts/admin-permissionjs.part.php");?>

<script type="text/javascript" language="javascript">
    var uploader_setup_evt = new Event("uploader_setup_evt", window, "onload", uploader_setup, true);
    var file_listing_setup_evt = new Event("file_listing_setup_evt", window, "onload", file_listing_setup, true);
    Events.add(uploader_setup_evt);
    Events.add(file_listing_setup_evt);
    function uploader_setup(){
        IFrameUploaderManager.showFrames = false;
        var myUploader = new IFrameUploader("basic_uploader",
            document.getElementById("uploader"),
            "/actions/admin/uploadfile.<?=$GLOBALS['ACTION_EXTENSION']?>",
            true,
            true,
            function(result, location, msg){
                if(result==1){
                    fillFileListing();
                }
            });

        var table = createPermissionsTable({"view":"", "edit":"", "delete":""});
        var row = DOMUtils.newElement("tr", {});
        var cell0 = DOMUtils.newElement("td", { });
        var cell1 = DOMUtils.newElement("td", {
            "colspan": "2"
        });
        var uriLabel = DOMUtils.newElement("label", {
           "for" : "uri"
        });
        var uriInput = DOMUtils.newElement("input", {
           "name" : "uri",
           "type" : "text",
           "style" : "width:100%;"
        });
        uriLabel.appendChild(document.createTextNode("Uri: "));
        cell0.appendChild(uriLabel);
        cell1.appendChild(uriInput);
        row.appendChild(cell0);
        row.appendChild(cell1);
        table.appendChild(row);

        myUploader.addFormElement(table);
        myUploader.initialise();
        initPermissions();
    }
    function file_listing_setup(){
        fillFileListing();
    }
    function fillFileListing(){
        var url = "/admin/actions/file-listing.<?=$GLOBALS['ACTION_EXTENSION']?>";
        var query = new HTMLHttpQuery(url, "GET");
        query.successFunction = gotFileData;
        query.run();
    }
    var expandedFolders = new Array();
    var folders;
    function gotFileData(result){

//        if(result.)
        var filesElement = document.getElementById("file_display");

        DOMUtils.removeChildren(filesElement);
        var foldersArr = DOMUtils.getElementsByClassName(result, 'folder');
        var folder;
        var children;
        var child;
        var expanded;
        folders = new Map();
        for(var i= 0; i < foldersArr.length; i++){
            folder = foldersArr[i];
//            alert("here");
            folders.add(folder['id'], folder);
//            alert("here2");
            expanded = expandedFolders.contains(folder['id']);
            children = folder.childNodes;
            folder.insertBefore(createFolderExpander(folder['id'], expanded), children[0]);
            if(!expanded){
                for(var j=0; j < children.length; j++){
                    child = children[j];
                    if(child.className == "folder_listing"){
                        child['style']['display'] = "none";
                    }
                }
            }
        }
        filesElement.appendChild(result);
    }
    function createFolderExpander(id, expanded){
        if(expanded){
            return DOMUtils.newElement("a", {"class":"folder_expander_expanded",
                    "href":"javascript:collapseFolder('"+id+"');"});
        }else{
            return DOMUtils.newElement("a", {"class":"folder_expander_collapsed",
                    "href":"javascript:expandFolder('"+id+"');"});
        }
    }
    function expandFolder(id){
		var folder = folders.get(id);
                expandedFolders.push(id);
//                alert("expandedFolders contains "+folder['id']+" "+(expandedFolders.contains(folder['id'])?"true":"false"));
		children = folder.childNodes;
		for(var j=0; j < children.length; j++){
			child = children[j];
			if(child.className == "folder_listing"){
				child['style']['display'] = "block";
			}
			if(child.className == "folder_expander_collapsed"){
				child["href"] = "javascript:collapseFolder('"+id+"');";
				child["className"] = "folder_expander_expanded";
			}
		}
	}

	function collapseFolder(id){
		var folder = folders.get(id);
                expandedFolders.removeElement(id);
		children = folder.childNodes;
		for(var j=0; j < children.length; j++){
			child = children[j];
			if(child.className == "folder_listing"){
				child['style']['display'] = "none";
			}
			if(child.className == "folder_expander_expanded"){
				child["href"] = "javascript:expandFolder('"+id+"');";
				child["className"] = "folder_expander_collapsed";
			}
		}
	}



    function deleteFile(uri, id){
        if(confirm("Are you sure you want to delete '"+uri+"'")){
            var url = "/admin/actions/delete-file.<?=$GLOBALS['ACTION_EXTENSION']?>";
            var query = new HTMLHttpQuery(url, "POST");
            query.successFunction = gotFileData;
            query.post = "fileid="+id;
            query.run();
        }
    }




</script>


</head>

<body>
<div id="page">
			<?php include('admin-template-parts/admin-header.part.php'); ?>
	

	<div id="content">
		
			<?php include('admin-template-parts/admin-menu.part.php'); ?>
			
		
		<div id="main">
			<?php include('admin-template-parts/admin-formresult.part.php'); ?>
			
			<fieldset>
                <legend>Upload Form</legend>
                <div id="uploader"></div>
            </fieldset>

            <div id="file_display">
                
            </div>
			
		</div>
		
		<div class="clear"><!--clear--></div>
	</div>
</div>

</body>
</html>