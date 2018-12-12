# Anleitung

1. IP-Adresse "xxxxxxxxxxxxxxx"in die Adressleiste des Browsers eingeben

2. Benutzername und Passwort eingeben und auf "Anmelden" klicken.

ACHTUNG: Logikfehler im Aufbau der Datenbank vorhanden
--> Themen werden mehrfach angezeigt

Ursache: In der Datenbank wird das selbe Topic mehfach als Knoten angelegt und zwar je Prof, der es beherrscht.
Beispiel: Fünf Profs seien mit dem Topic "HTML" vertraut. In der vorliegenden Datenbank wird nun ein Knoten "HTML" erzeugt je Prof, der das Topic beherrscht. 
Richtig wäre: Ein einziger Knoten mit fünf Verbindungen zu den einzelnen Profs.

Zu unserer Entlastung: Unter IP-Adresse "xxxxxxxxxxx" findet sich die Applikation mit dem EXAKT SELBEN Code, allerdings
mit Zugriff auf die - richtig aufgebaute - Testdatenbank.
