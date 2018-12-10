<?php
header("Content-Type: text/html; charset=utf-8");  
require_once 'vendor/autoload.php';
use GraphAware\Neo4j\Client\ClientBuilder;

// Default-Einstellungen
/*  @var $help HelperClass */

$navbar="includes/main_navbar.php";


if($_SESSION['navbar']!=""){$navbar="includes/".$_SESSION['navbar'].".php";}
    $task="login";
    $view2="";
    $level=0;

if(isset($_GET['task'])){ // Ohne Angabe des Task soll Starseite aufgerufen werden.
	$task=$_GET['task'];
}
	
switch($task){
	
        
    case "login":
		$view="views/login.php";    
                
                if($_POST['login']==1){
                    
                    $LoginResult = User::auth($_POST['username'], $_POST['passwort']);
                    
                    //Login simulieren
                    /*$LoginResult = 1; // Login immer erfolgreich
                    $_SESSION['uid'] = 1;
                    $_SESSION['kennung'] = 'test';
                    $_SESSION['fullName'] = 'Herr' . " " . 'Professor';*/
                    //$_SESSION['level'] = $user->gruppe;
                    
                    /******************/
                    if(!$LoginResult){
                        $error="Diese Seite existiert nicht";
                        $msg="Benutzer existiert nicht";
                    }
                    else{
                        $view="views/Themen.php";
                        $msg="Hallo ".$_SESSION['fullName'];
                    }
                }
    break;
        
        
    case "Themen":
            $view="views/Themen.php";
            $client = ClientBuilder::create()
            // ->addConnection('default', 'http://stud:stud@volz.hs-pforzheim.de:7474') // Example for HTTP connection configuration (port is optional)
            //->addConnection('bolt', 'bolt://stud:stud@volz.hs-pforzheim.de:7687') // Example for BOLT connection configuration (port is optional)
            ->addConnection('bolt', 'bolt://Config:1234@141.47.5.51:7687') // Example for BOLT connection configuration (port is optional)
            ->build();
                    
            $uebergebener_user = $_SESSION['alias'];
            //$uebergebener_user ="soenke.otten";
                                //$uebergebener_user = $_POST['username'];
                  
            
            if(isset ($_POST['addmovie']) || isset ($_POST['reactivate'])) {
                
                $Eingegebener_titel=trim($_POST['movietitle']);

                    if(empty($Eingegebener_titel)){
                          $msg= "Bitte Thema eintragen";
                        }
                        else {

                            if ($_POST['addmovie']==1 || $_POST['reactivate'] == 1 ){  
                                    // $client->run('MATCH (n:Movie {title: "'.$_POST['movietitle'] .'"}) RETURN n.title');
                                    //echo $client;

                                  $query = 'MATCH (n:Topic {title: "'.$Eingegebener_titel.'"}) RETURN n.title';
                                  $result = $client->run($query);


                                  // Abfrage: Ist das angegebene Thema schon als Knoten angelegt?
                                  $i=false;  //Hilfsvariable
                                  foreach ($result->getRecords() as $record) {


                                      if($record->value('n.title')==$Eingegebener_titel){   // Falls Thema/Knoten bereits vorhanden, dann prüfen, ob Verknüpfung besteht. 
                                          
                                         
                                          // Besteht schon eine Verknüfung?
                                            $query = 'MATCH (:Prof{alias:"'.$uebergebener_user.'"})-[r:knows{deleted:"false"}]->(p:Topic{title:"'.$Eingegebener_titel.'"}) RETURN r';
                                            $result = $client->run($query);
                                           
                                          
                                            // Es existiert noch keine Verbindung
                                            if($result->size() == 0) {
                                                $query = 'MATCH (p:Prof), (n:Topic) WHERE p.alias="'.$uebergebener_user.'" AND n.title="'.$Eingegebener_titel.'"MERGE (p)-[r:knows]->(n)SET r.deleted="false"';
                                                $result = $client->run($query);
                                                header("Refresh:0");
                                                $msg= "Verbindung zwischen $uebergebener_user und $Eingegebener_titel wurde erstellt!";     
                                                
                                             }
                                            
                                          $i=true; // Thema vorhanden, Verbindung erstellt
                                      }     
                                  }
  

                                  if($i==true){  //Wenn Verbindung bereits angelegt
                                    // Tue nichts weiter!


                                  } 
                                  else {    // Thema wurde noch nicht angelegt, also Knoten/Thema anlegen UDN Verbindung herstellen! 
                                  //$query = 'CREATE (n:Movie{title:"'.$Eingegebener_titel.'"})';
                                  $query = 'CREATE (n:Topic{title:"'.$Eingegebener_titel.'"})';
                                  $result = $client->run($query);

                              
                                        $query = 'MATCH (n:Prof), (p:Topic) WHERE n.alias="'.$uebergebener_user.'" AND p.title="'.$Eingegebener_titel.'"MERGE (n)-[r:knows]->(p)SET r.deleted="false"';
                                        $result = $client->run($query);
                                     
                                        //echo "Verbindung mit $uebergebener_user zum Thema $Eingegebener_titel wurde erstellt!";
                                        header("Refresh:0");
                                        $msg= "Verbindung zwischen $uebergebener_user und $Eingegebener_titel wurde erstellt!";

                                  }
                            }     
                    }                         
                    } 
                               
                    if ($_POST['delete']==1){  

                        $Eingegebener_titel_delete=trim($_GET['delmovie']);


                            $query = 'MATCH (:Prof{alias:"'.$uebergebener_user.'"}) -[r:knows]-(:Topic {title:"'.$Eingegebener_titel_delete.'"}) SET r.deleted="true"';
                            $result = $client->run($query);
                            header("Refresh:0");
                            $msg= "Die Verbindung zwischen $uebergebener_user und $Eingegebener_titel_delete wurde gelöscht!";
                    }
             
    break;

        	
    case "noauth":
		$view="views/noauth.php";
    break;
   
    case "logout":
		$view="views/login.php";
                User::logout();
                $msg="Sie wurden erfolgreich abgemeldet";            
	break;
      
        
	default:
	$title="Fehler";
	$error="Diese Seite existiert nicht";
	$view="views/error.php";
	
}


