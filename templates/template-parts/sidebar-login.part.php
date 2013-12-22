<script type="text/javascript">
        function showHideLogin(){
            var login = document.getElementById("menuLoginExpansion");
            var loginExpander = document.getElementById("menuLoginExpander");
            if(login.className=="hidden"){
                login.className = "visible";
                loginExpander.className = "visible";
                loginExpander.innerHTML = "- Login";
            }else{
                login.className = "hidden";
                loginExpander.className = "hidden";
                loginExpander.innerHTML = "+ Login";
            }
        }
    </script>
    
    <?php
    if(isset($GLOBALS['FORM_RESULTS']) && $GLOBALS['FORM_RESULTS']->hasError("invalid")){
        $hiddenvisible = "visible";
        $loginText = "- Login";
    }else{
        $hiddenvisible = "hidden";
        $loginText = "+ Login";
    }
    ?>
    <div id="menuLogin">
    <a id="menuLoginExpander" class="<?=$hiddenvisible?>" title="Login" href="javascript:showHideLogin();"><?=$loginText?></a>
    <div id="menuLoginExpansion" class="<?=$hiddenvisible?>">
    <div id="errors">
        <div class="form_error" id="caps_lock_on" style="visibility:hidden;display:none;">Warning: Caps Lock On</div>
        <?php if(isset($GLOBALS['FORM_RESULTS']) && $GLOBALS['FORM_RESULTS']->hasErrors() != "") {
          foreach($GLOBALS['FORM_RESULTS']->getErrors() as $key=>$err){ ?>
        <div class="form_error">Error: <?=$err?></div>
        <?php }
        } ?>
        </div>
        <form name="side_login" action="<?php _a("/actions/login-action")?>" method="post">
            <table>
                <tr>
                    <td>
                        <label for="email">Email:</label>
                    </td>
                    <td>
                        <input name="email" type="text" class="text" value="<?=formVal("email")?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="password">Pass:</label>
                    </td>
                    <td>
                        <input name="password" type="password" class="text"/>
                    </td>
                </tr>
                <!--tr>
                    <td colspan="2"><a href="javascript:document.forms['side_login'].submit();" title="Login" class="form_button">Login</a></td>
                </tr-->
            </table>
            <?php
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
            <input name="requested_uri" type="hidden" value="<?=extp(me()->getURI())?>"/>
            <input name="query_string" type="hidden" value="<?=$query_string?>"/>
        </form>
        </div>
    </div>