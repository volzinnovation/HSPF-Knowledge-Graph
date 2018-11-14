<?php

class HelperClass{
    /**
     * Wandelt Kommazahl ins kaufmännische Format um
     * 
     * Üblicherweis zur Ausgabe in der View
     * @param float $value zu konvertierende Zahl
     * @return String im kaufmännischen Format z.B. 1.234,25 €
     */
    public static function currency($value)
    {
        return number_format($value,2,",",".")." €";
    }
    
    public static function createX(){
     global $help;
     $SQL="INSERT INTO user (kennung,passwort) VALUES (?,?)";
     return $help->sql_stmt($SQL,'ssdss',$_POST['kennung'], $_POST['passwort'],time(),time());
     }
     /**
     * Markiert in der Navigationsleiste den aktuellen Task
     * 
     * Dies geschieht über eine entsprechende CSS-Klasse(ui-btn-active),
      * wenn Taskname aus dem Adressaufruf mit Taskname des Buttons übereinstimmt
     * @param float $text Taskname des Buttons
     * @return String class="ui-btn-active"
     */
	public static function showActive($text)
    {
        if($_SERVER['QUERY_STRING']=="task=".$text)
        {
            return ' class="ui-btn-active" ';
			// return ' class="fett" ';
        }
        else
        {
            return "";
        }
    }
     /**
     * Formatiert einen Timestamp in deutsches Datum
      * 
      * In PHP bekommt man mittels time() den Zeitstempel der aktuellen Systemzeit
      * Formell handelt es sich bei einem Timestamp um einen Long-Wert
     * @param long $ts Timestamp
     * @return String im Format z.B. 21.12.2018
     */
    public static function toDate($ts){
        return date('d.m.Y',$ts);    
    }
      /**
     * Formatiert einen Timestamp in deutsches Datum samt Uhrzeit
      * 
      * In PHP bekommt man mittels time() den Zeitstempel der aktuellen Systemzeit
      * Formell handelt es sich bei einem Timestamp um einen Long-Wert
     * @param long $ts Timestamp
     * @return String im Format z.B. 21.12.2018 20:15:37
     */
    public static function toDateTime($ts){
        return date('d.m.Y H:i:s',$ts);    
    }
     /**
     * Leitet serverseitig zu einem anderen Task um
      * 
      * Wichtig ist, dass keinerlei HTML-Output bis zu diesem Zeitpunkt (also auch kein Leerzeichen) erzeugt wurde,
      * da die Umleitung sonst nicht geht.
      * Da die Umleitung serverseitig passiert, wird möglicherweise nicht der korrekte Menüpunkt markiert.
      * Weitere Getparameter können manuell an den Tasknamen angehängt werden (home&id=7)
     * @param String $task Task zu dem umgeleitet wird
     
     */
    public static function redirect($task){
        header("location: ?task=".$task);
    }
     /**
     * Prüft ob Rechtelevel des Benutzers ausreicht.
      * 
      * Ist das Level größer als das Benutzerrecht ($_SESSION['level']) wird zum Task noauth weitergeleitet.
     * @param long $level Mindestlevel. Dies sollte in jedem Task im Controller zu beginn aktiv festgelegt werden.
     
     */
	
		public static function checkRight($level){
		if($level>$_SESSION['level']){
			HelperClass::redirect("noauth");
		}
		
	}
     /**
     * Wandelt Währung in kaufmännischer Form in MySQL-vertägliches Format um.
      * 
      * Kommt in Aktualisierungsformularen bei Währungsfeldern zum Einsatz.
      * Dabei wird der Post-Wert vor dem Eintrag in die Datenbank nocheinmal modifiziert.
     * @param String $value kaufmännisch formatierter Zahlenwert (z.b. 1.234,45 €
     * @return Float z.B. 1234.45
     */	
	public static function currencyToMySQL($value)
    {
        $tmp=str_replace("€","",$value);
        $tmp=str_replace(".","",$tmp);
        $tmp=str_replace(" ","",$tmp);
        $tmp=str_replace(",",".",$tmp);
        return $tmp;
    }
    
     /**
     * Erzeugt eine mit dynamischen Daten gefüllte HTML-Klappbox/Menü (<select>)
      * 
      * Es ist dafür eine Liste(Array) mit Objekten einer Tabelle nötig(z.B: $help->sql_queryList), die über ein entsprechendes model bereitgestellt werden muss.
      * Das Ganze ist  für eine Janus-Enumeration automatisch voreingerstellt, kann aber über Parameter konfiguriert werden. 
      * Standardmäßig wird ein Menü mit dem Namen SELECT erzeugt
      * @param Object $obj Objektarray der Datenbanktabelle/Abfrage
      * @param array $arr folgende Arrayfelder sind zulässig:
      * $name="select";
      * $key="rno";
      * $text="myval";
      * $default="";
      * $class="";
 *
     * @return String HTML-Ausgabe, samt Daten
     */	
	public static function htmlSelect($obj,$arr=array())    {
        $name="select";
        $key="rno";
        $text="myval";
        $default="";
        $class="";
        if(isset($arr['name'])){$name=$arr['name'];}
        if(isset($arr['default'])){$default=$arr['default'];}
        if(isset($arr['key'])){$key=$arr['key'];}
        if(isset($arr['text'])){$text=$arr['text'];}
        if(isset($arr['class'])){$class=$arr['class'];}
        ?><select name="<?=$name?>" id="<?=$name?>" class="<?=$class?>">
         <?php
        foreach($obj as $item){
        ?>
  <option value="<?=$item->$key?>"
    <?php
  if($default!=""){
      if($item->$key==$default){echo ' selected="selected"';}
      }
  ?>><?=$item->$text?></option>
      <?php } ?>
</select>
        <?php        
    }
	

    
	
     /**
     * fügt einen neuen Debug Eintrag hinzu, der automatisch am Ende ausgegeben wird
      * 
     * Verwendet die globale Variable $debug
     * @param String $text Die gewünschte Ausgabe
     * @param String $extra Zweiter, optionaler Wert, z.B. die Zeilennummer (__LINE)
    
     */

	
	public static function debug($text,$extra=""){
        global $debug;
        if(is_string($text)|| is_numeric($text)){
            if($extra!=""){
                $text=$text."[".$extra."]";
            }
        }else if (is_array($text)){
            array_push($text,$extra);
        }else if (is_object($text)){
            $text->extra=$extra;
        }
       
        array_push($debug,$text);
    }


   /**
     * Liefert das  Ergebnis einer SQL-Abfrage als einzelnes Objekt zurück
      * 
      * Verwendet die Standardverbindung <b>$mysql</b>
    * Sorgen Sie dafür, dass die Abfrage auch wirklich nur <b>einen</b> Datensatz zurückgibt. Ansonsten wird der erste zurückgegegebn.
    * Es muss ein model mit einer entsprechenden Klasse wie die Tabelle in  "SELECT ..... FROM <b>Tabelle</b>" geben. 
     * @param String $SQL Gültige SQL-Abfrage
     * @return Object Die resultierenden Feldnamen entsprechen 1:1 dem Datenbankmodell. 
    * Ist das Ergebnis der Abfrage leer, wird <b>false</b> zurückgegeben
     */    
    
    
    
public static function sql_queryItem($SQL){
        // liefert bei einem Datensatz ein Objekt bei mehreren ein  Objekt Array und false, wenn kein ergebnis
    global $mysql;
    global $debug;
    global $help;
   
        // Objektname extrahieren
        $pos=stripos($SQL,"From ")+5;
        $pos2=stripos($SQL," ",$pos);
        if(!$pos2){ //es folgt nach Klassennamen keine Anweisung mehr
            $classname=substr($SQL,$pos);
        }else{
            $classname=substr($SQL,$pos,$pos2-$pos);
        }
        //    $classname="user";   
        $rs=mysqli_query($mysql,$SQL);
        if(!$rs){ $help->debug("Fehler in SQL-String: "); $help->debug($mysql->error);}
        if(mysqli_num_rows($rs)==0){
            $help->debug("Keine Daten zum Anzeigen vorhanden: ".$SQL);
            return false;
        }else if(mysqli_num_rows($rs)==1){ // einzelner Datensatz
            $obj=mysqli_fetch_object($rs,$classname);
            return $obj;
        }else{
            $obj=mysqli_fetch_object($rs,$classname);
            $help->debug("Mehr als ein Datensatz vorhanden, nur der erste wurde zurückgegeben: ".$SQL);
            return $obj;       
        }   
    }
       /**
     * Liefert das  Ergebnis einer SQL-Abfrage als  <b>Objektliste(Array)</b> zurück
      * 
      * Verwendet die Standardverbindung <b>$mysql</b>
    * Es muss ein model mit einer entsprechenden Klasse wie die Tabelle in  "SELECT ..... FROM <b>Tabelle</b>" geben. 
     * Am leichtesten lassen sich die Datenüber eine foreach-Schleife abrufen.
     * @param String $SQL Gültige SQL-Abfrage
     * @return Object[] Die resultierenden Feldnamen entsprechen 1:1 dem Datenbankmodell. 
    * Ist das Ergebnis der Abfrage leer, wird <b>false</b> zurückgegeben
     */   
    
    
    
    
    public static function sql_queryList($SQL){
        //liefert ein  Objekt Array und false, wenn kein Ergebnis
    global $mysql;
    global $debug;
    global $help;
        // Objektname extrahieren
        $pos=stripos($SQL,"From ")+5;
        $pos2=stripos($SQL," ",$pos);
        if(!$pos2){ //es folgt nach Klassennamen keine Anweisung mehr
            $classname=substr($SQL,$pos);
        }else{
            $classname=substr($SQL,$pos,$pos2-$pos);
        }
        //    $classname="user";
        $rs=mysqli_query($mysql,$SQL);
        if(!$rs){ $help->debug("Fehler in SQL-String: "); $help->debug($mysql->error);}
        if(mysqli_num_rows($rs)==0){
            $help->debug("Keine Daten zum Anzeigen vorhanden: ".$SQL);
            return false;   
        }else{
            $klasse=array();
            while($obj=mysqli_fetch_object($rs,$classname)){
               
                array_push($klasse,$obj);           
            }
            return $klasse;   
        }
    }
    // ***************************************************************************************
        /**
     * Führt eine SQL-Abfrage zur Datenmanipulation (INSERT, UPDATE, DELETE) aus
      * 
      * Verwendet die Standardverbindung <b>$mysql</b>
    *  Es werden dabei <b>Prepared Statements</b> zur Vorbeugung gegen eingeschleusten Schadcode verwendet.
         * Die Syntax der Methode orientiert sich an den mysqli_statement... Methoden und bündelt alles in einem Aufruf.
         * Auftretende Fehler werden automatisch über die Debug-Variable angezeigt
         * Beispiel:
         * 
         * $SQL="INSERT INTO artikel(bezeichnung, Preis) VALUES (?,?)";
         * 
         * HelperClass::sql_stmt($SQL,"sd",$_POST['bezeichnung'], $_POST['Preis']);
     * @param String $SQL Parametrisierte SQL-Abfrage. Alle Parameter werden mit <b>?</b> ohne Anführungszeichen angegeben
     * @param String $types Datentypen der Paramenter (<b>i</b> für Ganzahl, <b>s</b> für String, <b>d</b> für Double
     * @param Mixed $param Kommagetrennte Liste der Variablen
     * @return short liefert bei Fehler <b>false</b> zurück. Bei einem INSERT wird bei Gelingen der erzeugte Primärschlüssel (m_oid) zurückgegeben, bei Update/DELETE true(-1)
    * Ist das Ergebnis der Abfrage leer, wird <b>false</b> zurückgegeben
     */ 
    public static function sql_stmt($SQL, $types,...$param){    // gibt bei Insert lasiinsert ID zurück, bei update,delete -1 und 0 wenn nicht geklappt
    global $mysql;
    global $debug;
    global $help;
    // $SQL="INSERT INTO user (kennung,gruppe,vorname, nachname, c_ts, m_ts,ldap) VALUES (?,?,?,?,?,?,?)";
     
     $stmt=mysqli_prepare($mysql,$SQL);
    if(!$stmt){ $help->debug("Fehler in SQL-String: (Feldnamen, Anzahl Felder/Parameter)"); $help->debug($mysql->error,__LINE__);}
   
    $bind=mysqli_stmt_bind_param( $stmt,$types,...$param);//
     
    if(!$bind){$help->debug("Fehler beim Befüllen der Parameter: Anzahl und Art der Parameter prüfen!!");
    $help->debug(error_get_last());
    $help->debug($stmt->error,__LINE__);}
     
    $rs= mysqli_stmt_execute($stmt);
    if(!$rs){
         $help->debug($stmt->error,__LINE__);
		 return 0;
    }
	else{
		if (mysqli_stmt_insert_id($stmt)==0){
			return -1;
		}else{
     	return mysqli_stmt_insert_id($stmt);   }
	}
  }
	
    
    
    
    
    
    
    
    
    
}
?>