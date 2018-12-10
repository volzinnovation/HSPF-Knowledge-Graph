<?php 
header("Content-Type: text/html; charset=utf-8");  
require_once 'vendor/autoload.php';
use GraphAware\Neo4j\Client\ClientBuilder;

$client = ClientBuilder::create()
// ->addConnection('default', 'http://stud:stud@volz.hs-pforzheim.de:7474') // Example for HTTP connection configuration (port is optional)
//->addConnection('bolt', 'bolt://stud:stud@volz.hs-pforzheim.de:7687') // Example for BOLT connection configuration (port is optional)
->addConnection('bolt', 'bolt://Config:1234@141.47.5.51:7687') // Example for BOLT connection configuration (port is optional)
->build();

?>


<table width="200" data-role="table" class="ui-responsive" data-mode="columntoggle" id="myTable">
    <thead>
        <tr>
            <th width="100" height="5" data-priority="4">EIGENE THEMEN:</th>
            <th width="100" height="5" data-priority="3">löschen:</th> 
        </tr>
    </thead>
    
    
    <tbody>   
        <?php

        //$uebergebener_user = $_POST['username'];// Abfrage bei Seitenaufruf -> Übergabe der Mail-Adresse (als ID) aus AD.
        $uebergebener_user = $_SESSION['alias'];
        //$uebergebener_user = "soenke.otten";

        // Anzeige der Ergebnisse
        $query = 'MATCH (:Prof{alias:"'.$uebergebener_user.'"})-[:knows{deleted:"false"}]->(p:Topic) RETURN p.title';
        $result = $client->run($query);

        foreach ($result->getRecords() as $record) {
          ?>
            <tr>
                <td>
                    <?php
                        echo $record->value('p.title');
                    ?>

                </td>
                 <td> 
                    <form id="delete" method="post" action="?task=Themen&delete=1&delmovie=<?php echo urlencode($record->value('p.title'));?>&username=<?php echo $uebergebener_user; ?>" data-ajax="true">                       
                    <button type="submit" name="delete" id="deletemoviebutton" value="1" data-icon="delete">
                    </form>                                 
                </td>
            </tr>   
            <?php  
        } ?>                
    </tbody>    
</table>
 


<form id="inputForm" method="post" action="?task=Themen&username=<?php echo $uebergebener_user; ?>" data-ajax="false">
    <input id="movietitle" placeholder="Thema eingeben" name="movietitle" />		
    <button type="submit" name="addmovie" id="addmovie" value="1">hinzufügen</button>
</form>


<?php
header("Content-Type: text/html; charset=utf-8");  
?>


<table width="200" data-role="table" class="ui-responsive" data-mode="columntoggle" id="myTable">
    <thead>
        <tr>
            <th width="100" height="5" data-priority="4">GELÖSCHTE THEMEN:</th>
            <th width="100" height="5" data-priority="3"> zurücksetzen:</th> 
        </tr>
    </thead>
    
    
    <tbody>   
        <?php

        $uebergebener_user =  $_SESSION['alias'];// Abfrage bei Seitenaufruf -> Übergabe der Mail-Adresse (als ID) aus AD.
        //$uebergebener_user =  "soenke.otten";// Abfrage bei Seitenaufruf -> Übergabe der Mail-Adresse (als ID) aus AD.

        // Anzeige der Ergebnisse
        $query = 'MATCH (:Prof{alias:"'.$uebergebener_user.'"})-[:knows{deleted:"true"}]->(p:Topic) RETURN p.title';
        $result = $client->run($query);

        foreach ($result->getRecords() as $record) {
          ?>
            <tr>
                <td>
                    <?php
                        echo $record->value('p.title');
                    ?>

                </td>
                 <td> 
                     <form id="reactivate" method="post" action="?task=Themen&addmovie=<?php echo urlencode($record->value('p.title'));?>&username=<?php echo $uebergebener_user; ?>" data-ajax="true">                       
                     <input type="hidden" id="movietitle"  name="movietitle" value=<?php echo urlencode($record->value('p.title'));?> />		
                    <button type="submit" name="reactivate" id="reactivatebutton" value="1" data-icon="back">
                    </form>                                 
                </td>
            </tr>   
            <?php  
        } ?>        
              
    </tbody>    
    
</table>