<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php include_once($GLOBALS['TEMPLATES_FOLDER']."/admin/admin-template-parts/admin-meta.part.php");?>
        <?php include_once($GLOBALS['TEMPLATES_FOLDER']."/admin/admin-template-parts/admin-tinymce.part.php");?>
        <link rel="stylesheet" href="/css/admin/file-listing.css" type="text/css" />

        <script language="javascript" type="text/javascript" src="/js/classes/FolderListing.js" >
        </script>

        <script language="javascript" type="text/javascript">
            /**********************
        Page Specific Script
             ***********************/
            Debug.on = false;

            var permissions_listing_evt = new Event("permissions_listing_evt", window, "onload", setupPermissionListing, true);
            Events.add(permissions_listing_evt);
            var permissionsListing;
            function setupPermissionListing(){
                var url = "/actions/admin/permissions/permissions-listing.<?=$GLOBALS['ACTION_EXTENSION']?>";
                permissionsListing = new FolderListing(document.getElementById("permissions_display"), url);
                permissionsListing.fill();
            }
            function sendForm(){
                var post = FormUtils.getFormValuesForPost(document.forms['permissions']);
//              Debug.floatingDebugBox("post", post);
                var url = "/actions/admin/permissions/add-permission.<?=$GLOBALS['ACTION_EXTENSION']?>";
                var query = new HTMLHttpQuery(url, "POST");
//                alert(post);
                query.successFunction = requestReturned;
                query.post = post;
                query.run();
            }
            function deletePermission(permission){
                if(confirm("Are you sure you want to delete '"+permission+"'")){
                    var url = "/actions/admin/permissions/delete-permission.<?=$GLOBALS['ACTION_EXTENSION']?>";
                    var query = new HTMLHttpQuery(url, "POST");
                    query.successFunction = requestReturned;
                    query.post = "permission="+permission;
                    query.run();
                }
            }
            function requestReturned(data){
                permissionsListing.gotData(data);
            }

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

            <?php include($GLOBALS['TEMPLATES_FOLDER'].'/admin/admin-template-parts/admin-header.part.php'); ?>

            <div id="content">

                <?php include($GLOBALS['TEMPLATES_FOLDER'].'/admin/admin-template-parts/admin-menu.part.php'); ?>

                <div id="main">
                    <?php include($GLOBALS['TEMPLATES_FOLDER'].'/admin/admin-template-parts/admin-formresult.part.php'); ?>

                    <form name="permissions" action="post" method="#">
                        <fieldset>
                            <legend>Add Permission</legend>
                            <table>
                                <tr>
                                    <td>Permission</td>
                                    <td><input type="text" name="permissionString"/></td>
                                </tr>
                                <tr>
                                    <td>Description</td>
                                    <td><input type="text" name="description"/></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><a class="form_button" href="javascript:sendForm()" title="Add">Add</a></td>
                                </tr>
                            </table>
                        </fieldset>
                    </form>

                    <div id="permissions_display">

                    </div>

                </div>

                <div class="clear"><!--clear--></div>
            </div>
        </div>

    </body>
</html>