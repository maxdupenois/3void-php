<?php require_once("../../includes/js-required.inc.php");?>
var PermissionListing = {
    permissionMap : new Map(),
    permissionUse : null,
    permissionEdit : null,
    permissionEditContent : null,
    permissionDelete : null,
    permissionUseDesc : null,
    permissionEditDesc : null,
    permissionEditContentDesc : null,
    permissionDeleteDesc : null,
    init : function(){
        <?php $permissions = Authorisation::permissionListing();?>
        <?php foreach($permissions as $p){?>
        PermissionListing.permissionMap.add("<?=$p->permission?>", "<?=str_replace("\"", "\\\\\"", $p->description)?>");
        <?php } ?>

        PermissionListing.permissionUse = document.getElementById('permission_use');
        PermissionListing.permissionEdit = document.getElementById('permission_edit');
        PermissionListing.permissionEditContent = document.getElementById('permission_edit_content');
        PermissionListing.permissionDelete = document.getElementById('permission_delete');


        PermissionListing.permissionUseDesc = document.getElementById('permission_use_description');
        PermissionListing.permissionEditDesc = document.getElementById('permission_edit_description');
        PermissionListing.permissionEditContentDesc = document.getElementById('permission_edit_content_description');
        PermissionListing.permissionDeleteDesc = document.getElementById('permission_delete_description');

        if(PermissionListing.permissionUse != null){
            PermissionListing.permissionUse['onchange'] =
                function(){PermissionListing.showPermissionDescription(
                    PermissionListing.permissionUse,
                    PermissionListing.permissionUseDesc
                    );};

            PermissionListing.showPermissionDescription(
                    PermissionListing.permissionUse,
                    PermissionListing.permissionUseDesc);
        }
        if(PermissionListing.permissionEdit != null){
            PermissionListing.permissionUse['onchange'] =
                function(){permissionEdit.showPermissionDescription(
                    PermissionListing.permissionEdit,
                    PermissionListing.permissionEditDesc
                    );};

            PermissionListing.showPermissionDescription(
                    PermissionListing.permissionEdit,
                    PermissionListing.permissionEditDesc);
        }
        if(PermissionListing.permissionEditContent != null){
            PermissionListing.permissionEditContent['onchange'] =
                function(){PermissionListing.showPermissionDescription(
                    PermissionListing.permissionEditContent,
                    PermissionListing.permissionEditContentDesc
                    );};

            PermissionListing.showPermissionDescription(
                    PermissionListing.permissionEditContent,
                    PermissionListing.permissionEditContentDesc);
        }
        if(PermissionListing.permissionDelete != null){
            PermissionListing.permissionDelete['onchange'] =
                function(){PermissionListing.showPermissionDescription(
                    PermissionListing.permissionDelete,
                    PermissionListing.permissionDeleteDesc
                    );};

            PermissionListing.showPermissionDescription(
                    PermissionListing.permissionDelete,
                    PermissionListing.permissionDeleteDesc);
        }
    },
    showPermissionDescription : function(permission, desc){
        var selected 		= FormUtils.getSelectedOptions(permission);
        DOMUtils.setText(desc,PermissionListing.permissionMap.get(selected[0].value));

    }
};
var permissionSetupEvent = new Event("permission_setup", window, "onload", PermissionListing.init, true);
Events.add(permissionSetupEvent);