<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php include_once($GLOBALS['TEMPLATES_FOLDER']."/admin/admin-template-parts/admin-meta.part.php");?>
        <?php include_once($GLOBALS['TEMPLATES_FOLDER']."/admin/admin-template-parts/admin-tinymce.part.php");?>
        <link rel="stylesheet" href="/css/admin/file-listing.css" type="text/css" />

        <style  type="text/css">
        table.groupTable{
            border-spacing: 5px;
        }
        th.groupLabel{
            text-align:center;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }
        td.permissionLabel{
            text-align:right;
            padding: 2px 10px 2px 10px;
        }
        td.permissionCell {
            border: 1px solid #ccc;
            width: 100px;
            height: 18px;
        }
        td.permissionCell.allow{
            /*background-color: #009900;*/
        }
        td.permissionCell.deny{
            /*background-color: #990000;*/
        }
        a.permissionSwitch{
            display:block;
            margin: 0px auto 0px auto;
            width: 50px;
            height: 16px;
            border: 1px solid #fff;
        }
        a.permissionSwitch.deny{
            background: transparent url("/images/admin/icons/switch-on.png") no-repeat center center;
        }
        a.permissionSwitch.allow{
            background: transparent url("/images/admin/icons/switch-off.png") no-repeat center center;
        }
        </style>

        <script language="javascript" type="text/javascript" src="/js/classes/AJAXTable.js" >
        </script>

        <script language="javascript" type="text/javascript">
            Debug.on = true;
            var init_group_table_evt = new Event("init_group_table", window, "onload", initGroupTable, true);
            Events.add(init_group_table_evt);
            var groupTable;

            var permissions = new Array();
            
            function constructCellContents(cellInfo, rowIndex,cellIndex){
                var permissionName = cellInfo["permissionName"];
                var groupName = cellInfo["groupName"];
                var hasPermission = cellInfo["hasPermission"];
                var a = DOMUtils.newElement("a",
                    {"href":"javascript:changePermission('"+permissionName+"',"+
                                                   "'"+groupName+"',"+
                                                   rowIndex+","+
                                                   cellIndex+","+
                                                   (hasPermission?"false":"true")+
                                                   ");",
                     "title":(hasPermission?"deny":"allow"),
                     "class": "permissionSwitch "+(hasPermission?"deny":"allow")});
                return a;
            }

            function changePermission(permissionName,groupName,rowIndex,cellIndex,changeTo){
                var url = "/actions/admin/permissions/change-group-permission.<?=$GLOBALS['ACTION_EXTENSION']?>";
                var query = new JSONHttpQuery(url, "POST");
                query.post = "permission="+encodeURI(permissionName)+"&group="+
                            encodeURI(groupName)+"&allow="+(changeTo?"true":"false");
                query.parameters = {"row":rowIndex, "cell":cellIndex,
                    "permission":permissionName, "group":groupName};
                query.successFunction = permissionChanged;
                query.run();
            }
            
            function permissionChanged(result, parameters){
                var success = result['success'];
                var messages = result['messages'];
                if(!success){
                    var msg = "";
                    for(var i=0;i<messages.length;i++){
                        msg+= messages[i]+"\n";
                    }
                    if(msg!="")alert(msg);
                    return;
                }
                var allowed = result['allowed'];
                var row = parameters["row"];
                var cell = parameters["cell"];
                var permissionName = parameters["permission"];
                var groupName = parameters["group"];
                var contents = constructCellContents({"permissionName":permissionName,
                    "groupName":groupName, "hasPermission":allowed},row,cell);
                groupTable.setCellContents(row, cell, contents);
                groupTable.getCell(row, cell).className="permissionCell "+(allowed?"allow":"deny");
            }

            function initGroupTable(){
                groupTable = new AJAXTable(document.getElementById("group_table_container"), "groupTable");
                
                var url = "/actions/admin/permissions/get-group-permission-table.<?=$GLOBALS['ACTION_EXTENSION']?>";
                groupTable.firstColumnLabeled = true;
                groupTable.cellContentsFunction = constructCellContents;
                groupTable.listedByRow = false;
                groupTable.offset = 0;
                groupTable.limit = 1;
                groupTable.fill(url);
                groupTable.show();
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

                    <form name="add_group" action="#" method="post">
                        <fieldset>
                            <legend>Add Group</legend>
                            <table>
                                <tr>
                                    <td>Name</td>
                                    <td><input type="text" name="groupName"/></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><a class="form_button" href="javascript:addGroup()" title="Add">Add</a></td>
                                </tr>
                            </table>
                        </fieldset>
                    </form>
                    
                    <div id="group_table_container">
                    </div>
                </div>

                <div class="clear"><!--clear--></div>
            </div>
        </div>

    </body>
</html>