# Installationsanleitung

1. Den Ordner "" 
IP-Adresse "xxxxxxxxxxxxxxx"in die Adressleiste des Browsers eingeben

2. Benutzername und Passwort eingeben und auf "Anmelden" klicken.

3. Sollten keine Themen sichtbar sein, bitte folgendes durchführen:

    a) In die Adressleiste des Browsers "xxxxxxxxxxxxxx" eingeben
    b) Folgenden Befehl ausführen: 
          MATCH (a:Prof)-[r]-(b:Topic)
          SET r.deleted="false"
        Dies setzt bei allen Verbindungen "knows" zwischen Prof und Topic den Wert des Attributs "Deleted" auf "false"
 
4. Bei 1. beginnen
