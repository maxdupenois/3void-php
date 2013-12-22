<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php include_once("admin-template-parts/admin-meta.part.php");?>
        <?php include_once("admin-template-parts/admin-tinymce.part.php");?>
        <link rel="stylesheet" href="/css/admin/file-upload.css" type="text/css" />
        <link rel="stylesheet" href="/css/admin/file-listing.css" type="text/css" />
        <?php require_once("admin-template-parts/admin-permissionjs.part.php");?>

        <script language="javascript" type="text/javascript" src="/js/classes/IFrameUploader.js" >
        </script>

        <script language="javascript" type="text/javascript" src="/js/classes/FolderListing.js" >
        </script>

        <script language="javascript" type="text/javascript"  >


            var uploader_setup_evt = new Event("uploader_setup_evt", window, "onload", uploader_setup, true);
            var action_listing_evt = new Event("action_listing_evt", window, "onload", setupActionListing, true);
            Events.add(uploader_setup_evt);
            Events.add(action_listing_evt);
            function uploader_setup(){
                IFrameUploaderManager.showFrames = true;
                var myUploader = new IFrameUploader("action_upload",
                document.getElementById("uploader"),
                "/actions/admin/actions/upload-action.<?=$GLOBALS['ACTION_EXTENSION']?>",
                true,
                true,
                uploadReturned);

//                var table = DOMUtils.newElement("table", {
//                    "style" : "text-align:left;width:100%;margin:10px 0px;"
//                });
                var table = createPermissionsTable({"use":"", "edit":"", "delete":""});
                var rowTitle = DOMUtils.newElement("tr", {});
                var cellTitle0 = DOMUtils.newElement("td", { });
                var cellTitle1 = DOMUtils.newElement("td",{
                    "colspan" : "2"
                });

                var labelTitle = DOMUtils.newElement("label", {
                    "for" : "title"
                });
                var inputTitle = DOMUtils.newElement("input", {
                    "name" : "title",
                    "type" : "text",
                    "style" : "width:100%;"
                })

                var rowLoc = DOMUtils.newElement("tr", {});
                var cellLoc0 = DOMUtils.newElement("td", { });
                var cellLoc1 = DOMUtils.newElement("td",{
                    "colspan" : "2"
                });
//                var cellRegex = DOMUtils.newElement("td", { });
                var labelLoc = DOMUtils.newElement("label", {
                    "for" : "location"
                });
                var inputLoc = DOMUtils.newElement("input", {
                    "name" : "location",
                    "type" : "text",
                    "style" : "width:100%;"
                });
                var rowDesc = DOMUtils.newElement("tr", {});
                var cellDesc0 = DOMUtils.newElement("td", { });
                var cellDesc1 = DOMUtils.newElement("td",{
                    "colspan" : "2"
                });
//                var cellRegex = DOMUtils.newElement("td", { });
                var labelDesc = DOMUtils.newElement("label", {
                    "for" : "description"
                });
                var inputDesc = DOMUtils.newElement("input", {
                    "name" : "description",
                    "type" : "text",
                    "style" : "width:100%;"
                });
//                var regexLabel = DOMUtils.newElement("label", {
//                    "for" : "regex"
//                });
//                var regexInput = DOMUtils.newElement("input", {
//                    "name" : "regex",
//                    "type" : "checkbox"
//                });
                labelTitle.appendChild(document.createTextNode("Title: "));
                labelLoc.appendChild(document.createTextNode("Location: "));
                labelDesc.appendChild(document.createTextNode("Description: "));
//                regexLabel.appendChild(document.createTextNode("Regex?: "));
                cellTitle0.appendChild(labelTitle);
                cellTitle1.appendChild(inputTitle);

                cellDesc0.appendChild(labelLoc);
                cellDesc1.appendChild(inputLoc);

                cellLoc0.appendChild(labelDesc);
                cellLoc1.appendChild(inputDesc);
//                regexLabel.appendChild(regexInput);
//                cellRegex.appendChild(regexLabel);

                rowTitle.appendChild(cellTitle0);
                rowTitle.appendChild(cellTitle1);

                rowDesc.appendChild(cellDesc0);
                rowDesc.appendChild(cellDesc1);

                rowLoc.appendChild(cellLoc0);
                rowLoc.appendChild(cellLoc1);
//                rowUri.appendChild(cellRegex);

                table.appendChild(rowTitle);
                table.appendChild(rowDesc);
                table.appendChild(rowLoc);

                myUploader.addFormElement(table);
                myUploader.initialise();
                initPermissions();
            }
            var actionListing;
            function uploadReturned(result, location, msg){
                if(msg!=null&&msg!=""){
                    alert(msg);
                }
                if(result==1){
                    actionListing.fill();
                }
            }
            function setupActionListing(){
                var url = "/actions/admin/actions/action-listing.<?=$GLOBALS['ACTION_EXTENSION']?>";
                actionListing = new FolderListing(document.getElementById("action_display"), url);
                actionListing.fill();
            }

            function deleteAction(title, id){
                if(confirm("Are you sure you want to delete '"+title+"'")){
                    var url = "/actions/admin/actions/delete-action.<?=$GLOBALS['ACTION_EXTENSION']?>";
                    var query = new HTMLHttpQuery(url, "POST");
                    query.successFunction = deleteActionReturned;
                    query.post = "actionid="+id;
                    query.run();
                }
            }
            function deleteActionReturned(data){
                actionListing.gotData(data);
            }
        </script>
        <script language="javascript" type="text/javascript">
            /**********************
Page Specific Script
             ***********************/

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


        </script>
    </head>

    <body>
        <div id="page">

            <?php include('admin-template-parts/admin-header.part.php'); ?>

            <div id="content">

                <?php include('admin-template-parts/admin-menu.part.php'); ?>

                <div id="main">
                    <?php include('admin-template-parts/admin-formresult.part.php'); ?>


                    <!--
************************
Start Page Specific Code
*************************
                    -->
                    <fieldset>
                        <legend>Upload Action</legend>
                        <div id="uploader"></div>
                    </fieldset>

                    <div id="action_display">

                    </div>
                    <!--
************************
End Page Specific Code
*************************
-->

                </div>

                <div class="clear"><!--clear--></div>
            </div>
        </div>

    </body>
</html>

