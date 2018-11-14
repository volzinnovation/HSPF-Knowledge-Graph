<?php

// Kommentar entfernen und eigene Daten eintragen

 $hostname_mysql = "127.0.0.1";
$database_mysql = "Konfigmaske";
$username_mysql = "Konfigmaske";
$password_mysql = "test";


$mysql = mysqli_connect($hostname_mysql, $username_mysql, $password_mysql,$database_mysql);
if (mysqli_connect_errno())
  {
 array_push($debug,"Failed to connect to MySQL: " . mysqli_connect_error());
  }
mysqli_set_charset($mysql, "utf8");
