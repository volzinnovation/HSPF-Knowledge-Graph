<?php  

$ldap_dn="kgraph-ldap,ou=RZ,ou=FH,cn=ma,cn=ad,cn=fh-pforzheim,cn=de";
$ldap_password="KG2018!";

$ldap_con = ldap_connect("masrv07.ma.ad.fh-pforzheim.de");
ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 4);

if(ldap_bind($ldap_con, $ldap_dn, $ldap_password)) {
    echo "Verbindung erfolgt";
} else {
    echo "Verbindung nicht erfolgt";
}

?>
