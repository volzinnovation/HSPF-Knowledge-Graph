Hallo lieber Anwender hier ist eine kurze Beschreibung wie die CSV-Datei, für die Datenbank erstellt werden kann.

1. Legen sie die Ausgangsdatei aus dem TermCounter mit dem Namen "dtm.csv" unter folgendem Pfad ab :"C:\dtm.csv"

2. Zunächst können in der Visual Basic Datei die Pfade angepasst werden (ohne Anpassungen werden sie direkt in :C\Users gespeichert):
	Zeile 12  ->  Pfad Ausgangsdatei	
	Zeile 319 ->  Pfad Datei für die Knoten 
	Zeile 350 ->  Pfad Datei für die Kanten

3. Für Testzwecke ist die Funktion Stopwords herausfiltern auskommentiert (aufgrund von langen Berechnungszeiten)
	für Berücksichtigung der Stopwords bitte Zeile 71 bis 86 aktivieren.


Für die Erstellung müssen sie die Makros aktivieren/vertrauen und auf den Butten CSV erstellen drücken.
Die zwei CSV Dateien für den Upload liegen nun Unter dem oben genannten Pfad ab.
