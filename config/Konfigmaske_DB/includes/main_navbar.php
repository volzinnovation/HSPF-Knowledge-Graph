<div id="menupanel" data-role="panel" data-display="overlay">

    
    <?php if($_SESSION['alias']!==""){ ?>
    <a href="?task=Themen" data-role="button" data-icon="grid" data-theme="b" <?php //=$help->showActive("home")?>>Konfigseite</a>  
     <?php } ?>
    <?php if($_SESSION['alias']==""){ ?>
     <a href="?task=login"  data-role="button" data-icon="sign-in" data-theme="b"<?php //=$help->showActive("login")?> >Login</a>
     <?php } 
	 else{ ?>
          <a href="?task=logout" data-ajax="false" data-icon="bullets"<?php //=$help->showActive("login")?> >Logout <?=$_SESSION['kennung'];?></a>
 <?php } ?>
       

 


</div>
