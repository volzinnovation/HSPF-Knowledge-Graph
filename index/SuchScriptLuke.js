// Beispiel Suchprogramm (in JavaScript)
// Nutzt Solr / Lucene Bibliotheken
// Kann in Luke unter Plugins > Scripting Luke ausgeführt werden
// für ein Java Programm fehlt noch Laden des Suchindex von der Festplatte (in Luke bereits gebunden an Variable ir) und die jeweiligen Datentypen für die Variablen (JavaScript ist typlos)

suchtext = "Investition";
searcher = new org.apache.lucene.search.IndexSearcher(ir);
analyzer = new org.apache.lucene.analysis.de.GermanAnalyzer();
query = new org.apache.lucene.queryparser.classic.QueryParser("_text_",analyzer).parse(suchtext);
docs = searcher.search(query, 10);
hits = docs.scoreDocs;
for (i=0; i < hits.length; i++) {
  docId = hits[i].doc;
  d = searcher.doc(docId);
  print(d.get("title") + " im Kurs # " + d.get("courseid"));
}
