<?php
function inBlog(){
    return false;
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="/css/required.css" media="print, screen"/>


<meta name="Keywords" content="3void"/>
<meta name="google-site-verification" content="JSsrgI2ulqEUQdUWA9jZ6GlBFvXCIbT9jmCC1rtrnTs" />
<meta name="google-site-verification" content="gmiPdyfAVoI5rnJJPKuLhVCrEwoQjd76K9E8HyWq96U" />
<link rel="shortcut icon" href="/favicon.ico"/>

<meta name="description" content="3Void, academic and personal website of Max Dupenois"/>
<meta name="keywords" content="GIS,Games,Max Dupenois,Code"/>
<meta name="author" content="Max Dupenois"/>

<title>3void.com<?=content("title", "", " - ")?></title>

<?php if(!inBlog()&&Authorisation::currentUser()==NULL&&$page->getURI()!="err/401") {?>
<script language="javascript" type="text/javascript" src="<?php _js("extensions/ObjectExt.js")?>" ></script>
<script language="javascript" type="text/javascript" src="<?php _js("utils/DOMUtils.js")?>" ></script>
<script language="javascript" type="text/javascript" src="<?php _js("classes/Map.js")?>" ></script>
<script language="javascript" type="text/javascript" src="<?php _js("classes/Events.js")?>" ></script>
<script language="javascript" type="text/javascript" src="<?php _js("classes/FormKeyListener.js")?>" ></script>
<script type="text/javascript">
    Events.add(new Event("add_login_form_key_listeners", window, "onload",
    function(){
        FormKeyListener.addReturnListenerToField("side_login", "email");
        FormKeyListener.addReturnListenerToField("side_login", "password");
        FormKeyListener.addCapsLockListenerToField("side_login", "email");
        FormKeyListener.addCapsLockListenerToField("side_login", "password");
    }
    , true));
</script>
<?php } ?>