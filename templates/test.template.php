<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="/css/admin/required.css" rel="stylesheet" type="text/css" media="screen, print" />
<title><?=$contents['title']?></title>


<script language="javascript" type="text/javascript">
/**********************
Page Specific Script
***********************/
<?php if($contents['javascript']!=NULL)$contents['javascript']->evaluate(); ?>


</script>

</head>

<body>
<div id="page">
	<div id="header">
		<div id="headertext"><a href="/admin/index.html" title="3VOID"><span class="three">3</span>VOID</a></div>
		
		<h3><?=$contents['section']?></h3>
		
	</div>

	<div id="content">
		
		<div id="main">
			
			<?php if($contents['main']!=NULL)$contents['main']->evaluate(); ?>
			
		</div>
		
		<div class="clear"><!--clear--></div>
	</div>
</div>

</body>
</html>