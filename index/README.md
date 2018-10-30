# Modul 3: Index

- Ziel: Indexierung von eLearning Inhalten
- Aufgaben:
	- (1) Nutzung von SolR Suchmaschine als Einstiegspunkt in den Graphen (Suche nach Thema führt zu qualifizierten Professoren)
	- (2) Auswertung von SuchIndex zur Erstellung des Themen-Prof Graps auf Basis von IR-Metriken wie TF/IDF, wird genutzt, um von Prof zu Thema zu navigieren und weiter den Graph zu traviersieren.
	- Ausführung auf vorkonfiguriertem Server
	- Eventuell Bearbeitung der Konfiguration für Indizierung (StopWords, Synonyme, etc.)
	
- Erste Aufgabe: Issues erstellen mit Aufgaben

- Bereitgestellte Code Beispiele:
	- HTML - Webseite als Suchmaschine als Frontend zu Solr im Repository (suchmaschine.html)[suchmaschine.html]
	- Java-Projekt zur Identifikation des besten Kurses für eine Suchanfrage (de.hspf.ai.kg)[de.hspf.ai.kg] als Basis für Top Themen Identifikation pro Prof. *Dies benötigt Java JDK und Gradle für die Übersetzung, Texteditor oder IntelliJ Community Edition zur Bearbeitung des Beispiel-Quellcode (Searcher.java)*

- Wichtige Links:
- [Lucene 5.5.0 Klassenbibliothek](http://lucene.apache.org/core/5_5_0/index.html), insbesondere relevant für Aufgabe 2 sind IndexReader und IndexSearcher, um eine Anfrage auszuwerten.
- [Luke 5.5.0](https://github.com/DmitryKey/luke/releases/tag/luke-5.5.0) enthält eine Script Engine, wo IndexReader bereits an die Variable ir gebunden ist, hier kann man schnell ein eigene Programmlogik ausprobieren
