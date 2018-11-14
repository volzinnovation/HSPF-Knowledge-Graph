
<form id="loginForm" method="post" action="?task=einkaufsListe&id=<?php echo $_GET['id']; ?>" data-ajax="false">
    <div class="ui-field-contain">
        <label for="Bezeichnung">Thema:</label><br></br>
        <input  id="Bezeichnung" placeholder="Themabezeichnung" name="Bezeichnung" type="text" /><br></br>
        <button type="submit" name="artikelAnlegen" id="artikelAnlegen" value="1">Neu Anlegen</button>
    </div>
</form>
 <script src="views/_artikelAnlegen.js"></script> <!-- Validierung -->


<table width="327" data-role="table" class="ui-responsive" data-mode="columntoggle" id="myTable">
    <caption> Themen:</caption>
    <thead>
        <tr>
            <th width="100" height="10" data-priority="4">Thema:</th>
           <!--<th width="100" height="10" data-priority="3">Menge:</th>-->
            <th width="100" height="10" data-priority="3">Ins Archiv setzen:</th> -->
            <th width="100" height="10" data-priority="3">Löschen:</th> 
        </tr>

    </thead>
    <tbody>
        <?php
        if(is_array($nocheinkaufen) || is_object($nocheinkaufen))      
            {
        
            foreach ($nocheinkaufen as $item) {
                ?>

                <tr>
                    <td><?php echo $item->Bezeichnung; ?></td>
                    <td>
                        <form id="update" method="post" action="?task=einkaufsListe&id=<?php echo $_GET['id']; ?>" data-ajax="false"> 
                            <input type="hidden" id="Artikel_id" name="Artikel_id" value="<?php echo $item->Artikel_id ?>">
                            <input id="Menge" placeholder="0" name="Menge" type="text" value="<?php echo $item->Menge; ?>"/>
                            <button type="submit" name="update" id="update" value="1">Menge ändern</button>
                        </form> 
                    </td>
                    <td> 
                        <form id="eingekauft" method="post" action="?task=einkaufsListe&id=<?php echo $_GET['id']; ?>" data-ajax="false"> 
                            <input type="hidden" id="Artikel_id" name="Artikel_id" value="<?php echo $item->Artikel_id ?>">
                            <input type="hidden" id="Bezeichnung" name="Bezeichnung" value="<?php echo $item->Bezeichnung ?>">
                            <input type="hidden" id="Status" name="Status" value="1">
                            <button type="submit" name="eingekauft" id="eingekauft" value="1" data-icon="check" data-theme="a">

                                </button>
                        </form>
                        </td>
                        <td>
                        <form id="delete" method="post" action="?task=einkaufsListe&id=<?php echo $_GET['id']; ?>" data-ajax="false"> 
                            <input type="hidden" id="Artikel_id" name="Artikel_id" value="<?php echo $item->Artikel_id ?>">
                            <input type="hidden" id="Bezeichnung" name="Bezeichnung" value="<?php echo $item->Bezeichnung ?>">
                           <button type="submit" name="delete" id="delete" value="1" data-icon="delete">
                              </button>
                        </form> 
                    </td>
                </tr>
                

        


        <?php } //foreach
        
            }//if?>
                
                

    </tbody>
</table>
 
    <form method="post" action="?task=einkaufsListe&id=<?php echo $_GET['id']; ?>" data-ajax="false"> 
        <input type="hidden" id="list_id" name="list_id" value="<?php echo $_GET['id']; ?>">
        <input type="hidden" id="Status" name="Status" value="1">
        <button type="submit" name="alle_eingekauft" id="alle_eingekauft" value="1" data-icon="check" data-theme="a"> alle löschen

            </button>
    </form>


    <table width="327" data-role="table" class="ui-responsive" data-mode="columntoggle" id="myTable">
        <caption> Gelöscht:</caption>
        <thead>
             
            <tr>
                <th width="20" height="10" data-priority="4">Thema:</th>
                <!--<th width="20" height="10" data-priority="3">Menge:</th>-->
                <th width="20" height="10" data-priority="3">Zurücksetzen:</th>
                <th width="20" height="10" data-priority="3">Löschen:</th> 

            </tr>
        </thead>
        <tbody>
            <?php
            if(is_array($eingekaufte) || is_object($eingekaufte))      
            {
                foreach ($eingekaufte as $item) {
            ?>
            <tr>
                <td><?php echo $item->Bezeichnung; ?></td>
                <td><?php echo $item->Menge; ?></td>
                <td> 
                    <form id="eingekauft" method="post" action="?task=einkaufsListe&id=<?php echo $_GET['id']; ?>" data-ajax="false"> 
                        <input type="hidden" id="Artikel_id" name="Artikel_id" value="<?php echo $item->Artikel_id ?>">
                        <input type="hidden" id="Bezeichnung" name="Bezeichnung" value="<?php echo $item->Bezeichnung ?>">
                        <input type="hidden" id="Status" name="Status" value="0">
                        <button type="submit" name="zuruecksetzen" id="zuruecksetzen" value="1" data-icon="back"></button>
                    </form>            
                </td>
                 <td>
                    <form id="delete" method="post" action="?task=einkaufsListe&id=<?php echo $_GET['id']; ?>" data-ajax="false"> 
                        <input type="hidden" id="Artikel_id" name="Artikel_id" value="<?php echo $item->Artikel_id ?>">
                        <input type="hidden" id="Bezeichnung" name="Bezeichnung" value="<?php echo $item->Bezeichnung ?>">
                       <button type="submit" name="delete" id="delete" value="1" data-icon="delete">
                          </button>
                    </form> 
                </td>
            </tr>
        </tbody>
        
        <?php } //foreach
            }//if?>

    </table>
 <form method="post" action="?task=einkaufsListe&id=<?php echo $_GET['id']; ?>" data-ajax="false"> 
        <input type="hidden" id="list_id" name="list_id" value="<?php echo $_GET['id']; ?>">
        <input type="hidden" id="Status" name="Status" value="0">
        <button type="submit" name="alle_zueruecksetzen" id="alle_zueruecksetzen" value="1" data-icon="back" data-theme="a"> alle zurücksetzen

            </button>
    </form>
