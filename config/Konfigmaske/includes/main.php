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
require("includes/mysql.php"); // Datenbankverbindung bitte einbinden und mysql.php anpassen
require("includes/helper.php");

$help = new HelperClass();
$title = "WI Konfigseite"; // Standardüberschrift

require("models/model.user.php");
//require("models/model.liste.php");
require("models/model.thema.php"); 
//require("models/model.update.php");
//require("models/model.verbinden.php");
require("includes/controller.php");