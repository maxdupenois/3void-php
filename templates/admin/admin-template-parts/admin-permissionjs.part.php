<script type="text/javascript" language="javascript">
    <?php $permissions = Authorisation::permissionListing();?>
    var permissionMap = new Map();
    <?php foreach($permissions as $p){?>
    permissionMap.add("<?=$p->permission?>", "<?=str_replace("\"", "\\\\\"", $p->description)?>");
    <?php } ?>
    
    function initPermissions(){
        displayDescription("view");
        displayDescription("use");
        displayDescription("edit");
        displayDescription("edit_content");
        displayDescription("delete");
    }


    function createPermissionsTable(initValues){
        
        var permissionsTable = DOMUtils.newElement("table", {
           "class" : "permission_table",
           "style" : "text-align:left;width:100%;margin:10px 0px;"
        });
        if(initValues['view']!=null){
            createPermissionsBox(permissionsTable, "Permission - View:", "view", initValues['view']);
        }
        if(initValues['use']!=null){
            createPermissionsBox(permissionsTable, "Permission - Use:", "use", initValues['use']);
        }
        if(initValues['edit']!=null){
            createPermissionsBox(permissionsTable, "Permission - Edit:", "edit", initValues['edit']);
        }
        if(initValues['edit_content']!=null){
            createPermissionsBox(permissionsTable, "Permission - Edit Content:", "edit_content", initValues['edit_content']);
        }
        if(initValues['delete']!=null){
            createPermissionsBox(permissionsTable, "Permission - Delete:", "delete", initValues['delete']);
        }
        return permissionsTable;
    }

    function createPermissionsBox(table, labelTitle, type, selectedPermission){
        var row = DOMUtils.newElement("tr", { });
        var cell0 = DOMUtils.newElement("td", { });
        var cell1 = DOMUtils.newElement("td", { });
        var cell2 = DOMUtils.newElement("td", { });
        var label = DOMUtils.newElement("label", {"for":"permission_"+type})
        label.appendChild(document.createTextNode(labelTitle))
        var select = DOMUtils.newElement("select", {
           "name" : "permission_"+type,
           "id" : "permission_"+type
        });
        var permissionKeys = permissionMap.getKeys();
        var option;
        var permission;
        for(var i=0; i<permissionKeys.length; i++){
            permission = permissionKeys[i];
            option =  DOMUtils.newElement("option", {
                "value" : permission
            });
            if(selectedPermission==permission){
                option["selected"] = "selected";
            }
            option.appendChild(document.createTextNode(permission));
            select.appendChild(option);
        }
        var span  = DOMUtils.newElement("span", {
           "id" : "permission_desc_"+type,
           "class" : "description"
        });
        select["permtype"] = type;
        select["onchange"] = function(){
            displayDescription(this["permtype"]);
        };
        cell0.appendChild(label);
        cell1.appendChild(select);
        cell2.appendChild(span);
        row.appendChild(cell0);
        row.appendChild(cell1);
        row.appendChild(cell2);
        table.appendChild(row);
    }
    function displayDescription(type){
        var select = document.getElementById("permission_"+type);
        if(select){
            var option = FormUtils.getSelectedOptions(select)[0];
            var desc = permissionMap.get(option.value);
            DOMUtils.setText(document.getElementById("permission_desc_"+type), desc);
        }
    }
</script>