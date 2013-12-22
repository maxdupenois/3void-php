<div style="margin:0px;padding:0px;">
<?php include($GLOBALS['TEMPLATES_FOLDER'].'/admin/admin-template-parts/admin-formresult.part.php'); ?>
<ul id="files" class="file_listing">
    <?php 
    $files = File::getFileFolderListing();
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
                $html .= "<li>\n";

                $html .= "<a href=\"/".$GLOBALS["UPLOADS_BASE"]."/".$val->getUri()."\" "
                      ."title=\"View ".$val->getUri()."\" class=\"view_file\" target=\"_blank\"> </a>\n";
                if(Authorisation::isAuthorised($val->getPermissionEdit())){
                    $html .= "<a href=\"/admin/pages/edit-page.".$GLOBALS['PAGE_EXTENSION']."?file=".$val->getId()."\" "
                          ."title=\"Edit ".$val->getURI()."\" class=\"edit_file\"> </a>\n";
                }
                if(Authorisation::isAuthorised($val->getPermissionDelete())){
                    $html .= "<a href=\"javascript:deleteFile('".$val->getUri()."', '".$val->getId()."')\" "
                          ."title=\"Delete ".$val->getUri()."\" class=\"delete_file\"> </a>\n";
                }

                $html .= basename($val->getUri())."\n";;
                $html .= "</li>\n";
            }
        }
        return $html;
    }
    if(count($files)>0){
        echo listFolder($files);
    }else{
        echo "<li>No files</li>";
    }
    ?>
</ul>
</div>