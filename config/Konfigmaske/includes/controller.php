<?php

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
	
        case "search":
            $help->checkRight(1);
            $view="views/search.php";
        break;
     
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
            if($_POST['liste_loeschen']==1)
            {
                if(isset($_POST['m_oid'])){ // Ohne Angabe des Task soll Starseite aufgerufen werden.
                    $m_oid=$_POST['m_oid'];
                }

                if(isset($m_oid))
                {
                   $einkaufsliste_to_del=Liste::find_by_id($m_oid);
                   $msg="Liste wurde gelöscht: " . $einkaufsliste_to_del[0]->Bezeichnung;
                   Liste::delete($m_oid);
                }
            }
            if($_POST['verbindung_loeschen']==1)
            {
                if(isset($_POST['user_id'])) $user_id=$_POST['user_id'];
                if(isset($_POST['einkaufsliste_id'])) $einkaufsliste_id =$_POST['einkaufsliste_id'];
                if(isset($_POST['Bezeichnung'])) $Bezeichnung =$_POST['Bezeichnung'];
                
                

                if(isset($user_id) && isset($einkaufsliste_id))
                {
                   $return= lverbinden::loeschen($user_id, $einkaufsliste_id);
                   
                   if($return) $msg="Verbindung zur Liste '" . $Bezeichnung . "' wurde aufgehoben!" ;
                }
            }
            
            
            
            $help->checkRight(1);
            $view="views/Eigene_Einkaufsliste.php";
            $user_id=$_SESSION['uid'];
            $einkaufsliste=Liste::find($user_id);
            
            $einkaufsliste_fremd = Liste::find_foreign_by_user($user_id);
            $view2="views/Fremde_Einkaufsliste.php";
            

            
            break;
        
        case "logout":
		$view="views/home.php";
                User::logout();
                $msg="Sie wurden erfolgreich abgemeldet";
              
	break;
    
    
    case "Einkaufsliste_erstellen":
            $help->checkRight(1);
            $view="views/Einkaufsliste_erstellen.php";
         if($_POST['liste_anlegen']==1){
             
             $user_id=$_SESSION['uid'];
        $liste=Liste::create($user_id);
        $msg="Liste wurde angelegt";
         $help->checkRight(1);
            $view="views/Eigene_Einkaufsliste.php";
             $user_id=$_SESSION['uid'];
            $einkaufsliste=Liste::find($user_id);
            $view2="views/Fremde_Einkaufsliste.php";
         
         }
        break;
        	
	case "noauth":
		$view="views/noauth.php";
	break;
    case "einkaufsListe":
            $view="views/einkaufsListe.php";
        $user_id=$_SESSION['uid'];
        $list_id=$_GET['id'];
        
        
        if(isset($_POST['Bezeichnung'])) $Bezeichnung = $_POST['Bezeichnung'];
        
      
        if($_POST['alle_eingekauft']==1 || $_POST['alle_zueruecksetzen']==1  ){
            //$list_id = $_POST['list_id'];
            $Status_soll = $_POST['Status'];
            
            if($Status_soll == 1)  $Status_ist=0; 
            else $Status_ist = 1;
            
            $artikellist = Artikel::find_article_by_list($list_id,$Status_ist); //!$Status invertieren
            foreach ($artikellist AS $artikel)
            {
                $update=Artikel::update_status($list_id, $user_id, $artikel->Artikel_id, $Status_soll);
            }
            
            if($Status_soll == 1) $msg = "Artikel eingekauft";
            else $msg = "Artikel zurückgesetzt";
        }
        
        if($_POST['eingekauft']==1){
            $Artikel_id = $_POST['Artikel_id'];
            $Status = $_POST['Status'];
            $update=Artikel::update_status($list_id, $user_id, $Artikel_id, $Status);
            
            if($Status == 1) $msg = "Artikel eingekauft: " . $Bezeichnung ;
            else $msg = "Artikel zurückgesetzt: " . $Bezeichnung;
        }
         if($_POST['zuruecksetzen']==1){
            $Artikel_id = $_POST['Artikel_id'];
            $Status = $_POST['Status'];
            $update=Artikel::update_status($list_id, $user_id, $Artikel_id, $Status);
            
            if($Status == 1) $msg = "Artikel eingekauft: " . $Bezeichnung ;
            else $msg = "Artikel zurückgesetzt: " . $Bezeichnung;
        }
        
        if($_POST['update']==1){
            $Artikel_id = $_POST['Artikel_id'];
            $Menge= $_POST['Menge'];
            $update=Artikel::update_menge($list_id, $user_id, $Artikel_id, $Menge);
            $msg = "Artikel Menge auf " . $Menge . " geändert";
        }
        
        if($_POST['delete']==1){
            $Artikel_id = $_POST['Artikel_id'];
            $update=Artikel::delete_from_list($list_id, $user_id, $Artikel_id);
            $msg = "Artikel "  . $Bezeichnung. " gelöscht!";
        }
                
        if($_POST['artikelAnlegen']==1){
            $user_id=$_SESSION['uid'];
            
            $artikelliste=Artikel::create($user_id,$list_id);
        }
        
        
        $nocheinkaufen = Artikel::find_article_by_list($list_id, 0); 
        $eingekaufte = Artikel::find_article_by_list($list_id, 1); 
        //$nocheinkaufen=Artikel::find($user_id);
       
       
       
            break;
        case "listeverbinden":
            $help->checkRight(1);
            $view="views/Liste_Verbinden.php";
            if($_POST['listeverbinden']==1){
                $bez=$_POST['Bezeichnung'];
                $hash=$_POST['Hashtag'];        
                $liste =Liste::find_by_bez_hash($bez,$hash);
                $user_id=$_SESSION['uid'];
                
                //Jetzt die Verbindung zur fremden Liste erstellen
                lverbinden::verbinden($user_id, $liste->m_oid);
                $msg = "Verbindung zur Liste '". $bez . "' erstellt!";
               
                 $view="views/Eigene_Einkaufsliste.php";
                 $user_id=$_SESSION['uid'];
            $einkaufsliste=Liste::find($user_id);
            $einkaufsliste_fremd = Liste::find_foreign_by_user($user_id);
                  $view2="views/Fremde_Einkaufsliste.php";
                  
            }
            break;
            
        case "upload":
        {
            
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            // Check if image file is a actual image or fake image
            if(isset($_POST["submit"])) {
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if($check !== false) {
                    echo "File is an image - " . $check["mime"] . ".";
                    $uploadOk = 1;
                } else {
                    echo "File is not an image.";
                    $uploadOk = 0;
                }
            }
            
            $msg = $_FILES["fileToUpload"]["name"];
            
            break;
            

        }
	default:
	$title="Fehler";
	$error="Diese Seite existiert nicht";
	$view="views/error.php";
	
	
	
}


