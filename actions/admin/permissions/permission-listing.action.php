<?php
import("authorisation.Permission");
if(!isset($GLOBALS['FORM_RESULTS'])) $GLOBALS['FORM_RESULTS'] = new FormResults();?>
<div style="margin:0px;padding:0px;">
    <?php
    $permissions = Permission::getPermissionListingByFolder();
    $html = "";
    function listFolder($folder, $path=""){
        $html = "";
        $currentFolder = $path;
        foreach($folder as $k=>$val){
            if(is_array($val)){
                $currentFolder = $path.".".$k;
                $html .= "<li class=\"folder\" id=\"folder_".$currentFolder."\"><img class=\"folder_img\"/>$k\n";
                $html .= "<ul class=\"folder_listing\">\n";
                    $html .= listFolder($val, $currentFolder);
                $html .= "</ul></li>\n";
            }else{
                $html .= "<li>&lfloor; \n";

                if($val->isDeletable()&&Authorisation::isAuthorised("admin.permissions.delete")){
                    $html .= "<a href=\"javascript:deletePermission('".$val->getPermissionString()."')\" "
                          ."title=\"Delete ".$val->getPermissionString()."\" class=\"delete_file\"> </a>\n";
                }
                $lastDot = strrpos($val->getPermissionString(), ".");
                $html .= ($lastDot!==false?substr($val->getPermissionString(), ($lastDot+1)):$val->getPermissionString())."\n";
                $html .= " -- ".$val->getDescription();
                $html .= "</li>\n";
            }
        }
        return $html;
    }
    if(count($permissions)>0){
        $html = listFolder($permissions);
    }else{
        $html = "<li>No Permissions</li>";
    }
    ?>

<?php include($GLOBALS['TEMPLATES_FOLDER'].'/admin/admin-template-parts/admin-formresult.part.php'); ?>
<ul id="files" class="file_listing">
    <?php echo $html; ?>
</ul>
</div>