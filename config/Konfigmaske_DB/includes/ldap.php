<?php 

class LDAP{
  
    
    //VORBEREITUNG FÜR ini-Datei

    private static $ldap_password = 'KG2018!';
    private static $ldap_username = 'kgraph-ldap';
    private static $ldap_server = "masrv07.ma.ad.fh-pforzheim.de";
    private static $ldap_searchbase = 'ou=FH,dc=ma,dc=ad,dc=fh-pforzheim,dc=de';
    private static $ldap_domain = 'MA';
    private static $ldap_connection;


    public static function auth($username, $password)
    {
                        
       //Immer anmelden
       // return array(true, $username, $username);
            
        self::$ldap_connection = ldap_connect(self::$ldap_server);

        //LDAP Parameter
        if (FALSE === self::$ldap_connection){
            // Fehler
            $msg= 'Verbindung zum Server nicht möglich.';
        }

        ldap_set_option(self::$ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
        ldap_set_option(self::$ldap_connection, LDAP_OPT_REFERRALS, 0); //Für LDAP-Suchen


            // LDAP-Anbindung 
        if (TRUE === ldap_bind(self::$ldap_connection, self::$ldap_username, self::$ldap_password)){
            //Anbindung erfolgt

            if ($bind) {
                    $filter="(&(objectCategory=person)(objectClass=user)(sAMAccountName=". $username ."))";
                    $result = ldap_search(self::$ldap_connection,self::$ldap_searchbase,$filter);
                    $info = ldap_get_entries(self::$ldap_connection, $result);
                    $ldap_lastname = $info[0]["sn"][0]; // Last Name
                    $ldap_firstname = $info[0]["givenName"][0]; // given Name
                    //ldap_close(self::$ldap_connection);
            } else {
                    $msg = "Invalid email address / password";
                    echo $msg;
            }


            if (empty($username)) {   //Abfangen leerer Eingaben
                // $username leer
                $msg= "Bitte Benutzername eingeben!";
                $login_success = false;
            } 
            else if (empty ($password)) {
                // Passwort leer
                $msg= "Bitte Kennwort eingeben!";
                $login_success = false;
            } 
            else {

                // Authentifizierung des Benutzers

        
                if ($bind = ldap_bind(self::$ldap_connection, self::$ldap_domain . "\\" . $username, $password)) {
                    // Login erfolgreich   
                    echo "Erfolgreich eingeloggt.";
                    $login_success = true;
                } 
                else {
                    // Login fehlgeschlagen / Benutzer nicht vorhanden
                    echo "Kennwort oder Benutzername falsch!"; 
                    $login_success = false;
                }
            }
        }

        ldap_unbind(self::$ldap_connection); // Clean up
        
        return array($login_success, $ldap_firstname, $ldap_lastname);
    }

    public static function logout() {
        ldap_unbind($ldap_connection); // Clean up
    }
}
?>
