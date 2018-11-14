<?php

/*  @var $help HelperClass */

class User {

    public static function auth($benutzer, $password) {
        global $help;
        global $mysql;

        if ($benutzer != "" && $password != "") {
            $SQL = "SELECT * FROM user WHERE kennung='$benutzer' AND passwort='$password'";
            $user=$help->sql_queryItem($SQL);
          
            //$rs=mysqli_query($mysql,$SQL);
            if ($user != false) {
               
                $_SESSION['uid'] = $user->m_oid;
                $_SESSION['kennung'] = $user->kennung;
                $_SESSION['fullName'] = $user->vorname . " " . $user->nachname;
                $_SESSION['level'] = $user->gruppe;
                return 1;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function logout() {
        $_SESSION['uid'] = "";
        $_SESSION['kennung'] = "";
        $_SESSION['fullName'] = "";
        $_SESSION['level'] = "";
        //session_destroy();
    }
     public static function create(){ // 
	
        global $help;
        global $mysql;
         $SQL="INSERT INTO user(kennung, passwort) VALUES (?,md5(?))";
         $rs=$help->sql_stmt($SQL, 'ss',$_POST['kennung'] ,$_POST['passwort']);
      return $rs; 
     }
    public static function find_by_id($user_id){ // 
        global $help;
        $SQL="SELECT * FROM user WHERE m_oid = ". $user_id;
      return $help->sql_queryItem($SQL); 
    
	  
  }

}
