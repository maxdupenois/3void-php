<?php
$inBlog = inBlog();
if(!$inBlog&&Authorisation::currentUser()!=NULL) {
?>
<div id="welcome">
    <span class="welcometext">Welcome <?=Authorisation::currentUser()->getUsername()?></span>
    <a class="logout" href="<?= exta("/actions/logout-action")."?curi="
        .me()->getURI()?>" title="Logout">Logout</a>
    <div class="clear"><!--clear--></div>
</div>
<?php
}else if(!$inBlog&&me()->getURI()!="err/401"){
    _part("template-parts/sidebar-login.part.php");
}
?>
<?php if(!$inBlog){?><div id="menu"><?php }?>
<ul>
    <li>Internal
        <ul>
            <li><a href="<?php _link(); ?>" title="Home">Home</a></li>
            <li><a href="<?php _p("about"); ?>" title="About">About</a></li>
            <li><a href="<?php _p("contact"); ?>" title="Contact Me">Contact Me</a></li>
            <li><a href="http://braindump.3void.com" title="Blog - Braindump">Braindump</a></li>
            <?php if(!$inBlog&&Authorisation::isAuthorised(me()->getPermissionEdit())){?>
                <li><a href="<?php _admin(extp("pages/edit-page")."?page=".me()->getUri());?>" title="Edit">Edit</a></li>
            <?php }?>

            <?php if(!$inBlog&&Authorisation::isAuthorised(me()->getPermissionEditContent())){?>
                <li><a href="<?php _admin(extp("pages/set-page-content")."?page=".me()->getUri());?>" title="Edit Content">Edit Content</a></li>

            <?php }?>
            <li><a href="<?php _admin(); ?>" title="Administration"  <?php
            echo (Authorisation::isAuthorised("admin.page.view")?"":"class=\"req_auth\"");
            ?>>Admin</a></li>
        </ul>
    </li>
    <li>Projects
        <ul>
            <li><a href="<?php _p("projects/tracert"); ?>" title="TraceRT">TraceRT</a></li>
            <li><a href="<?php _p("projects/toroidwars"); ?>" title="Toroid Wars">Toroid Wars</a></li>
            <li><a href="<?php _p("projects/gamesexeter"); ?>" title="Games:Exeter">Games:Exeter</a></li>
            <li><a href="<?php _p("projects/codetohtml"); ?>" title="CodeToHTML">Code-To-HTML</a></li>
            <li><a href="<?php _p("projects/basicgameengine"); ?>" title="CodeToHTML">Basic Game Engine</a></li>
        </ul>
    </li>
    <li>Academic
        <ul>
            <li><a href="<?php _p("academic/research"); ?>" title="Research">Research</a></li>
            <li><a href="<?php _p("academic/publications"); ?>" title="Publications">Publications</a></li>
            <li><a href="<?php _p("academic/work"); ?>" title="Work in Progress"  <?php
            echo (Authorisation::isAuthorised("academic.page.view")?"":"class=\"req_auth\"");
            ?>>Work in progress</a></li>
        </ul>
    </li>
    <?php if(!$inBlog){?>
    <li>External
        <ul>
            <li>Colleagues
                <ul>
                    <li><a href="http://emps.exeter.ac.uk/mathematics-computer-science/staff/jtc202" class="name" title="Jacqueline Christmas">Jacqueline Christmas</a></li>
                    <li><a href="http://people.exeter.ac.uk/ac206/" class="name" title="Andrew Clark">Andrew Clark</a></li>
                    <li><a href="http://people.exeter.ac.uk/km314/index.php" class="name" title="Kent McClymont">Kent McClymont</a></li>
                    <li><a href="http://people.exeter.ac.uk/djw213/" class="name" title="David Walker">David Walker</a></li>
                    <li><a href="http://emps.exeter.ac.uk/mathematics-computer-science/staff/zmw201" class="name" title="Zena Wood">Zena Wood</a></li>
                </ul>
            </li>
            <li>Supervisors
                <ul>
                    <li><a href="http://emps.exeter.ac.uk/mathematics-computer-science/staff/apgalton" class="name" title="Dr Antony Galton">Dr Antony Galton</a></li>
                    <li><a href="http://emps.exeter.ac.uk/mathematics-computer-science/staff/jz205" class="name" title="Dr Jovisa Zunic">Dr Jovisa Zunic</a></li>
                </ul>
            </li>
            <li><a href="http://emps.exeter.ac.uk/mathematics-computer-science/staff/mpd209" title="My Staff Page">My Staff Page</a></li>
            <li><a href="http://blackbox.3void.com/" class="name"  title="TraceRT">TraceRT</a></li>
            <li><a href="http://people.exeter.ac.uk/km314/toroidwars/" class="name"  title="Toroid Wars 2009">Toroid Wars 2009</a></li>
            <li><a href="http://people.exeter.ac.uk/km314/toroidwars2010/" class="name"  title="Toroid Wars 2010">Toroid Wars 2010</a></li>
            <li><a href="http://emps.exeter.ac.uk/mathematics-computer-science/research/aigames/" class="name"  title="Games:Exeter">Games:Exeter</a></li>
        </ul>
    </li>
    <?php }?>

</ul>
<?php if(!$inBlog){?></div><?php }?>