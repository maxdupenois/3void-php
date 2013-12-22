<?php

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php include_once("admin-template-parts/admin-meta.part.php");?>
        <?php include_once("admin-template-parts/admin-tinymce.part.php");?>
        <?php require_once("admin-template-parts/admin-permissionjs.part.php");?>
        <link rel="stylesheet" href="/css/admin/file-listing.css" type="text/css" />

        <script language="javascript" type="text/javascript" src="/js/classes/FolderListing.js" >
        </script>

        <script language="javascript" type="text/javascript">
//            Debug.on = true;
            /**********************
                Page Specific Script
             ***********************/
<?php $actions = Action::actionListing();?>
                 var actionMap = new Map();
<?php foreach($actions as $a) {?>
                 actionMap.add("<?=$a->getId()?>", "<?=str_replace("\"", "\\\"", $a->getDescription())?>");
<?php } ?>

            var mapping_listing_evt = new Event("mapping_listing_evt", window, "onload", setupMappingListing, true);
            var permission_setup_evt = new Event("permission_setup_evt", window, "onload", setupPermissions, true);
            var actions_setup_evt = new Event("actions_setup_evt", window, "onload", setupActions, true);

            Events.add(mapping_listing_evt);
            Events.add(permission_setup_evt);
            Events.add(actions_setup_evt);

            var mappingListing;

            function setupPermissions(){
                var table = createPermissionsTable({"view":"", "edit":"", "delete":""});
                document.getElementById("permissionsSet").appendChild(table);
                initPermissions();
            }
            function setupActions(){
                 document.forms['create_action']['action']['onchange'] = function(){showActionDescription();};
                 showActionDescription();
            }
            function setupMappingListing(){
                var url = "/actions/admin/actions/action-mapping-listing.<?=$GLOBALS['ACTION_EXTENSION']?>";
                mappingListing = new FolderListing(document.getElementById("action_mapping_display"), url);
                mappingListing.fill();
            }

            function showActionDescription(){
                var selectElement 	=  document.forms['create_action']['action'];
                var descriptionElement 	=  document.getElementById('action_description');
                var selected 		= FormUtils.getSelectedOptions(selectElement);
                DOMUtils.setText(descriptionElement,actionMap.get(selected[0].value));
            }
            function sendForm(){
                var post = FormUtils.getFormValuesForPost(document.forms['create_action']);
//              Debug.floatingDebugBox("post", post);
                var url = "/actions/admin/actions/create-action-mapping.<?=$GLOBALS['ACTION_EXTENSION']?>";
                var query = new HTMLHttpQuery(url, "POST");
//                alert(post);
                query.successFunction = requestReturned;
                query.post = post;
                query.run();
            }
            function deleteActionMapping(title, uri){
                if(confirm("Are you sure you want to delete '"+title+"'")){
                    var url = "/actions/admin/actions/delete-action-mapping.<?=$GLOBALS['ACTION_EXTENSION']?>";
                    var query = new HTMLHttpQuery(url, "POST");
                    query.successFunction = requestReturned;
                    query.post = "actionmapping="+uri;
                    query.run();
                }
            }
            function requestReturned(data){
                
                mappingListing.gotData(data);
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
                    <?php $actions = Action::actionListing();?>
                    <?php $permissions = Authorisation::permissionListing();?>
                    <form name="create_action" action="#" method="post">
                        <fieldset>
                            <legend>Details</legend>
                            <table>
                                <tr>
                                    <td>
                                        <label for="uri">Path:</label>
                                    </td>
                                    <td>
                                        <?php $uri =(isset($GLOBALS['FORM_RESULTS'])&&$GLOBALS['FORM_RESULTS']!=NULL&&$GLOBALS['FORM_RESULTS']->getValue('uri')!=""?$GLOBALS['FORM_RESULTS']->getValue('uri'):""); ?>
                                        <input name="uri" type="text" class="text" value="<?=$uri?>" style="width:100%;"/>

                                    </td>
                                    <td>
                                        <label for="regex">re?:<input name="regex" value="" type="checkbox"/></label>
                                        <span class="form_error">
                                            <?=(isset($GLOBALS['FORM_RESULTS'])&&$GLOBALS['FORM_RESULTS']!=NULL&&$GLOBALS['FORM_RESULTS']->getErr('uri')!=""?$GLOBALS['FORM_RESULTS']->getErr('uri'):"")?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="title">Title:</label>
                                    </td>
                                    <td>
                                        <?php $title =(isset($GLOBALS['FORM_RESULTS'])&&$GLOBALS['FORM_RESULTS']!=NULL&&$GLOBALS['FORM_RESULTS']->getValue('title')!=""?$GLOBALS['FORM_RESULTS']->getValue('title'):""); ?>
                                        <input name="title" type="text" class="text" value="<?=$title?>" style="width:100%;"/>
                                    </td>
                                    <td>
                                        <span class="form_error">
                                            <?=(isset($GLOBALS['FORM_RESULTS'])&&$GLOBALS['FORM_RESULTS']!=NULL&&$GLOBALS['FORM_RESULTS']->getErr('title')!=""?$GLOBALS['FORM_RESULTS']->getErr('title'):"")?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>

                                    <td>
                                        <label for="action">Action:</label>
                                    </td>
                                    <td>
                                        <?php $action =(isset($GLOBALS['FORM_RESULTS'])&&$GLOBALS['FORM_RESULTS']!=NULL&&$GLOBALS['FORM_RESULTS']->getValue('action')!=""?$GLOBALS['FORM_RESULTS']->getValue('action'):""); ?>
                                        <select name="action" style="width:100%;">
                                            <?php foreach($actions as $a) {?>
                                            <option value="<?=$a->getId()?>" <?=($action==$a->getId()?"selected=\"selected\"":"")?> ><?=$a->getTitle()?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <span id="action_description" class="description"><?=$actions[0]->getDescription()?></span>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                        <fieldset id="permissionsSet">
                            <legend>Permissions</legend>
                            
                        </fieldset>
                        <a href="javascript:sendForm();" title="Create">Create</a>
                    </form>


                    <div id="action_mapping_display">

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

