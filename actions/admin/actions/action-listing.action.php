<?php if(!isset($GLOBALS['FORM_RESULTS'])) $GLOBALS['FORM_RESULTS'] = new FormResults();?>
<div style="margin:0px;padding:0px;">
    <?php
    $actions = Action::getActionFolderListing();
    $html = "";
    function listFolder($folder, $path=""){
        $html = "";
        $currentFolder = $path;
        foreach($folder as $k=>$val){
            if(is_array($val)){
                $currentFolder = $path."/".$k;
                $html .= "<li class=\"folder\" id=\"folder_".$currentFolder."\"><img class=\"folder_img\"/>$k\n";
                $html .= "<ul class=\"folder_listing\">\n";
                    $html .= listFolder($val, $currentFolder);
                $html .= "</ul></li>\n";
            }else{
                $html .= "<li>&lfloor; \n";

                if(Authorisation::isAuthorised($val->getPermissionEdit())){
                    $html .= "<a href=\"/admin/pages/edit-action.".$GLOBALS['PAGE_EXTENSION']."?action=".$val->getId()."\" "
                          ."title=\"Edit ".$val->getTitle()."\" class=\"edit_file\"> </a>\n";
                }
                if(Authorisation::isAuthorised($val->getPermissionDelete())){
                    $html .= "<a href=\"javascript:deleteAction('".$val->getTitle()."', '".$val->getId()."')\" "
                          ."title=\"Delete ".$val->getTitle()."\" class=\"delete_file\"> </a>\n";
                }

                $html .= $val->getTitle()."\n";;
                $html .= "</li>\n";
            }
        }
        return $html;
    }
    if(count($actions)>0){
        $html = listFolder($actions);
    }else{
        $html = "<li>No Actions</li>";
    }
    ?>

<?php include($GLOBALS['TEMPLATES_FOLDER'].'/admin/admin-template-parts/admin-formresult.part.php'); ?>
<ul id="files" class="file_listing">
    <?php echo $html; ?>
</ul>
</div>