<?php if(isset($GLOBALS['FORM_RESULTS'])&&$GLOBALS['FORM_RESULTS']!=NULL&&$GLOBALS['FORM_RESULTS']->getMsg('success')!=""){?>
<div><span class="success">Success: <?=$GLOBALS['FORM_RESULTS']->getMsg('success')?></span></div>
<?php }else if(isset($GLOBALS['FORM_RESULTS'])&&$GLOBALS['FORM_RESULTS']!=NULL&&$GLOBALS['FORM_RESULTS']->hasMessages()!=""){?>

<?php foreach($GLOBALS['FORM_RESULTS']->getMessages() as $key=>$val){?>
<div><span class="success">Msg: <?=$val?></span></div>
<?php }?>

<?php }?>

<?php if(isset($GLOBALS['FORM_RESULTS'])&&$GLOBALS['FORM_RESULTS']!=NULL&&$GLOBALS['FORM_RESULTS']->getErr('unauthorised')!=""){?>
<div><span class="form_error">Error: <?=$GLOBALS['FORM_RESULTS']->getErr('unauthorised')?></span></div>
<?php }else if(isset($GLOBALS['FORM_RESULTS'])&&$GLOBALS['FORM_RESULTS']!=NULL&&$GLOBALS['FORM_RESULTS']->hasErrors()!=""){?>

<?php foreach($GLOBALS['FORM_RESULTS']->getErrors() as $key=>$val){?>
<div><span class="form_error">Error: <?=$val?></span></div>
<?php }?>

<?php }?>