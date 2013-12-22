<?php if($GLOBALS['TINY_MCE_LOCATION'] != "") {?>
<script language="javascript" type="text/javascript" src="<?=$GLOBALS['TINY_MCE_LOCATION']?>">
</script>
<script language="javascript" type="text/javascript" >
tinyMCE_GZ.init({
	plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,inlinepopups,"+
	"insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,"+
	"visualchars,nonbreaking,xhtmlxtras,imagemanager,filemanager",
	themes : 'simple,advanced',
	languages : 'en',
	disk_cache : true,
	debug : false
});
</script>
<script language="javascript" type="text/javascript" >
tinyMCE.init({
// General options
mode : "none",
theme : "advanced",
plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,inlinepopups,"+
	"insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,"+
	"visualchars,nonbreaking,xhtmlxtras,imagemanager,filemanager",

// Theme options
theme_advanced_buttons1 : "fullscreen, newdocument,|,spellchecker,iespell,|,search,replace,|,cut,copy,paste,pastetext,pasteword|,undo,redo,|,cleanup,code,",
theme_advanced_buttons2 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,sub,sup,",
theme_advanced_buttons3 : "styleselect,formatselect,fontselect,fontsizeselect|,outdent,indent,blockquote,",
theme_advanced_buttons4 : "bullist,numlist,|,link,unlink,anchor,image,charmap,nonbreaking, media,advhr,|,insertdate,inserttime,preview",
theme_advanced_buttons5 : "tablecontrols",
theme_advanced_toolbar_location : "external",
theme_advanced_toolbar_align : "left",
theme_advanced_statusbar_location : "bottom",
theme_advanced_resizing : true,

// Example content CSS (should be your site CSS)
content_css : "<?=$GLOBALS['TINY_MCE_TEXT_CSS']?>",

});
</script>
<?php }?>