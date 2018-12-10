<?php

require_once 'includes/ldap.php';
/*  @var $help HelperClass */


class User {

    public static function auth($benutzer, $password) {
         
        
        
        

        if ($benutzer != "" && $password != "") {
            
            $result = LDAP::auth($benutzer, $password);
            $loginresult = $result[0];
            if($loginresult)
            {
                $_SESSION['firstName'] = $result[1];
                $_SESSION['lastName'] = $result[2];
                $_SESSION['fullName'] = $benutzer;
                $_SESSION['alias'] = $benutzer;
            }
            return $loginresult; 
            
            
            
            
            
            
        } else {
            return false;
        }
    }
    
    
    

    public static function logout() {
        $_SESSION['fullName'] = "";
        $_SESSION['alias'] = "";
        //session_destroy();
    }
     public static function create(){ // 
	
        global $help;
       
      return $rs; 
     }
    

}
