<div id="menu_bar">
		<ul class="header_menu">
			<li><a href="/" title="Home" <?=($_GET['uri'] == "index.html"? "class=\"selected\"": "")?>>Home</a></li>
			<li><a href="/aboutus.html" title="About Us" <?=($_GET['uri'] == "aboutus.html"? "class=\"selected\"": "")?> >About Us</a></li>
			<li><a href="/products/" title="Products" <?=(preg_match("/products\/.*/", $_GET['uri'])? "class=\"selected\"": "")?> >Products</a></li>
			<li><a href="/services.html" title="Services" <?=($_GET['uri'] == "services.html"? "class=\"selected\"": "")?> >Services</a></li>
			<li><a href="/contact.html" title="Contact" <?=($_GET['uri'] == "contact.html"? "class=\"selected\"": "")?> >Contact</a></li>
			<li><a href="/articles/" title="Articles" <?=(preg_match("/articles\/.*/", $_GET['uri'])? "class=\"selected\"": "")?> >Articles</a></li>
			<li><a href="/feedback.html" title="Feedback" <?=($_GET['uri'] == "feedback.html"? "class=\"selected\"": "")?> >Feedback</a></li>
			<?php if($current_user!=NULL){?>
			<li><a href="/admin/" title="Administration" target="_blank">Admin</a></li>
			<?php }?>
			<div class="clear"><!--clear--></div>
		</ul>
	</div>
<?php if($current_user!=NULL) 
echo "<div id=\"welcome\"><a href=\"/actions/logout-action.html?curi=".$page->getURI()."\" title=\"Logout\">Logout</a> You are logged in as ".$current_user->getUsername()."</div>"; ?>