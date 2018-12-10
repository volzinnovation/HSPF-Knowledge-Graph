<?php
    require_once 'vendor/autoload.php';
    require("includes/main.php"); ?>
<html>
<head>
<meta charset="utf-8">
<title><?=$title?></title>
<link rel="stylesheet" href="css/themes/hs.css" />
<link rel="stylesheet" href="css/themes/jquery.mobile.icons.min.css" />
<link href="css/themes/jqm-icon-pack-fa.css" rel="stylesheet" type="text/css" />
<link href="css/jquery.mobile.custom.structure.min.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<script src="includes/js/jquery-2.1.3.min.js"></script>
<script src="includes/js/jquery.mobile.custom.min.js"></script>
<script src="includes/js/jquery.validate.min.js"></script>
<script src="includes/js/additional-methods.min.js"></script>

</head>
<body>
 <div data-role="header" id="header" data-position="fixed" class="ui-header ui-bar-inherit ui-header-fixed slidedown ui-fixed-hidden">
 
  
    <div id="menu">
       	  <a href="#menupanel" data-role="button" data-icon="bars" data-mini="true" data-iconpos="notext" data-inline="true" class="ui-link ui-btn ui-icon-bars ui-btn-icon-notext ui-btn-inline ui-shadow ui-corner-all ui-mini"></a>
         <?=$title ?>
    </div>
    
    <div id="logo"></div>
<!--     <div id="search">
     	<a href="?task=search" data-role="button" data-icon="search" data-mini="true" data-iconpos="notext" class="ui-link ui-btn ui-icon-search ui-btn-icon-notext ui-shadow ui-corner-all ui-mini"></a>
     </div>-->
  </div>
    
<div id="page1" class="page" data-role="page">
    <div data-role="content" class="ui-content">
    <?php require($navbar) ?>
    <?php if($msg!="") { ?>
        <div id="message" class="message">
            <?php echo($msg);?>
        </div>
    <?php } ?>
    <div id="view1">
        <?php require($view);?>
    </div>
    <div id="view2">
        <?php if($view2!=""){require($view2);}?>
    </div>
    <?php if($debugmode==0){?>
    <div id="debug">
        <?php
            if(count($debug)>0){ var_dump($debug); ?>
                <script language="javascript">
                    <?php foreach($debug as $debugitem){ ?>      
                        console.log("<?=$debugitem;?>");
                            <?php 
                    } ?>
                </script>
                    <?php 
            } ?>
    </div>
    <div id="phpErrors">
  <?php }
  //foreach($debugarray=xdebug_get_collected_errors() as $info){
  // echo($info);
  //}
  ?></div>
</div>

</div>
  
</body>



</html>
