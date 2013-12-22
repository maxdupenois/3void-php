<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <?php include('template-parts/meta.part.php'); ?>
        <?php _part("template-parts/google-analytics.part.php");?>
    </head>
    <body>

        <div id="page">
	<?php include('template-parts/header.part.php'); ?>
            <div id="columns">
                <div id="left_column">
                    <p>
                    <?php if(isset($contents['content'])&&$contents['content']!=NULL)$contents['content']->evaluate(); ?>
                    </p>

                    
                </div>
                <div id="right_column">
                    <?php include('template-parts/links.part.php'); ?>
                </div>
                <div class="clear"><!--clear--></div>
            </div>
        </div>
    </body>
</html>