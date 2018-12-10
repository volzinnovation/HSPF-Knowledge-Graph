<?php
 

/* Anwendungsübeeergreifende Dinge, wie Konstanten, DB-Verbindung, ggf. benutzerdefinierte Funktionen, die bei jedem 
  Aufruf verwendet werden. Nogtwendigerweise vor jeglichem HTML-Output.
 */

$debugmode = 1;
session_start();
if ($debugmode == 1) {
    error_reporting(E_ALL & ~E_NOTICE);
    $debug = array();
}

xdebug_start_error_collection();


require("includes/ldap.php"); // Datenbankverbindung bitte einbinden und mysql.php anpassen



$title = "Konfigurationsseite"; // Standardüberschrift

require("models/model.user.php");

require("models/model.thema.php"); 


require("includes/controller.php");
