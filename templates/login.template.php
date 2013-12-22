<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <?php include('template-parts/meta.part.php'); ?>
        <script language="javascript" type="text/javascript" src="<?php _js("extensions/ObjectExt.js")?>" ></script>
        <script language="javascript" type="text/javascript" src="<?php _js("utils/DOMUtils.js")?>" ></script>
        <script language="javascript" type="text/javascript" src="<?php _js("classes/Map.js")?>" ></script>
        <script language="javascript" type="text/javascript" src="<?php _js("classes/Events.js")?>" ></script>
        <script language="javascript" type="text/javascript" src="<?php _js("classes/FormKeyListener.js")?>" ></script>
        <script type="text/javascript">
            Events.add(new Event("add_login_form_key_listeners", window, "onload",
            function(){
                FormKeyListener.addReturnListenerToField("login", "email");
                FormKeyListener.addReturnListenerToField("login", "password");
                FormKeyListener.addCapsLockListenerToField("login", "email");
                FormKeyListener.addCapsLockListenerToField("login", "password");
            }
            , true));
        </script>
        <?php _part("template-parts/google-analytics.part.php");?>
    </head>
    <body>

        <div id="page">
            <?php include('template-parts/header.part.php'); ?>
            <div id="columns">
                <div id="left_column">
                    <?=(isset($contents['content'])?$contents['content']:"")?>
                    <br/>
                    <div id="errors">
                    <div class="form_error" id="caps_lock_on" style="visibility:hidden;display:none;">Warning: Caps Lock On</div>
                    <?php if(isset($GLOBALS['FORM_RESULTS']) && $GLOBALS['FORM_RESULTS']->hasErrors() != "") {
                      foreach($GLOBALS['FORM_RESULTS']->getErrors() as $key=>$err){ ?>
                    <div class="form_error">Error: <?=$err?></div>
                    <?php }
                    } ?>
                    </div>
                    <form name="login" action="<?php _a("/actions/login-action")?>" method="post">
                        <table>
                            <tr>
                                <td>
                                    <label for="email">Email: </label>
                                </td>
                                <td>
                                    <input name="email" type="text" class="text" value="<?=formVal("email")?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="password">Password:</label>
                                </td>
                                <td>
                                    <input name="password" type="password" class="text"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><a href="javascript:document.forms['login'].submit();" title="Login" class="form_button">Login</a></td>
                            </tr>
                        </table>
                        <?php
                        $requested = (isset($GLOBALS['REQUESTED_URI'])&&$GLOBALS['REQUESTED_URI']!=""?$GLOBALS['REQUESTED_URI']:$page->getURI());
                        if(!isset($_GET['file'])&&!preg_match("/.*\.".$GLOBALS['PAGE_EXTENSION']."/", $requested) > 0){
                            $requested .= ".".$GLOBALS['PAGE_EXTENSION'];
                        }
                        $query_string = "";
                        $first = true;
                        foreach($_GET as $n=>$v) {
                            if($n!="lerr" && $n!="uri" && $n!="file") {
                                if(!$first) $query_string .="&";
                                $query_string .= $n."=".$v;
                                $first =false;
                            }
                        }

                        ?>
                        <input name="requested_uri" type="hidden" value="<?=$requested?>"/>
                        <input name="query_string" type="hidden" value="<?=$query_string?>"/>
                    </form>

                </div>
                <div id="right_column">
                    <?php include('template-parts/links.part.php'); ?>
                </div>
                <div class="clear"><!--clear--></div>
            </div>
        </div>
    </body>
</html>