<?php

class to_thema_user{}
class themen {

    public static function create($user_id,$list_id) { 
        global $help;
        global $mysql;

        $SQL = "INSERT INTO themen(Bezeichnung,Listen_id,user_id,Menge,Status) VALUES (?,?,?,?,?)";
        $rs = $help->sql_stmt($SQL, 'siiii', $_POST['Bezeichnung'],$list_id,$user_id,$_POST['menge'],"0");

        return $rs;
    }
    public static function find($user_id) {
        global $help;
        global $mysql;
        if (empty($arr) || $arr['m_oid'] == "") {
            $SQL = "SELECT * FROM to_artikelEinkaufsliste WHERE Status= 0 AND user_id='$user_id'";
            return $help->sql_queryList($SQL); // liefert einen Array von Objekten undgibt diesen gleich zurück
        }
    }
    
    public static function find_article_by_list($Listen_id, $Status) {
        global $help;
        global $mysql;
        
            $SQL = "SELECT * FROM to_artikelEinkaufsliste WHERE Status=". $Status. " AND Listen_id='$Listen_id'";
            return $help->sql_queryList($SQL); // liefert einen Array von Objekten undgibt diesen gleich zurück
        
    }
    public static function update_menge($Listen_id, $user_id, $Artikel_id, $Menge ){
        global $help;
        global$mysql;
        
        $SQL="UPDATE to_artikelEinkaufsliste SET Menge = ?  WHERE Listen_id=? AND user_id=? AND artikel_id = ?";
         $rs = $help->sql_stmt($SQL, 'iiii',$Menge, $Listen_id, $user_id, $Artikel_id);

        return $rs;
    }        
    public static function update_status($Listen_id, $user_id, $Artikel_id, $Status){
        global $help;
        global$mysql;
        
        $SQL="UPDATE to_artikelEinkaufsliste SET status = ? WHERE Listen_id=? AND user_id=? AND artikel_id = ?";
         $rs = $help->sql_stmt($SQL, 'iiii',$Status, $Listen_id, $user_id, $Artikel_id);

        return $rs;
    }   
    
    public static function delete_from_list($Listen_id, $user_id, $Artikel_id){
        global $help;
        global$mysql;
        
        $SQL="DELETE FROM to_artikelEinkaufsliste WHERE Listen_id=? AND user_id=? AND artikel_id = ?";
        $rs = $help->sql_stmt($SQL, 'iii', $Listen_id, $user_id, $Artikel_id);

        return $rs;
    } 
                
                
    }

?>