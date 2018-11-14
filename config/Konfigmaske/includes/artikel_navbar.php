<div id="menupanel" data-role="panel" data-display="overlay">

    <a href="?task=home" data-role="button" data-icon="home" data-theme="b" <?php //=$help->showActive("home")?>>Home</a>
    <a href="?task=Konfigseite" data-role="button" data-icon="Einkaufsliste" data-theme="b" <?php //=$help->showActive("home")?>>Konfigseite</a> 
    
   
    <?php if($_SESSION['uid']==""){ ?>
     <a href="?task=login"  data-role="button" data-icon="sign-in" data-theme="b"<?php //=$help->showActive("login")?> >Login</a>
     <?php } 
	 else{ ?>
          <a href="?task=logout" data-ajax="false" data-icon="bullets"<?php //=$help->showActive("login")?> >Logout <?=$_SESSION['fullName'];?></a>
 <?php } ?>
       

 


</div>
