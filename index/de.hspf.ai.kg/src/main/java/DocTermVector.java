import org.apache.lucene.analysis.de.GermanAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.*;
import org.apache.lucene.queryparser.classic.ParseException;
import org.apache.lucene.search.DocIdSetIterator;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.ScoreDoc;
import org.apache.lucene.search.TopDocs;
import org.apache.lucene.store.Directory;
import org.apache.lucene.store.FSDirectory;
import org.apache.lucene.util.BytesRef;

import java.io.IOException;
import java.nio.file.Paths;
import java.util.*;
import java.util.stream.Collectors;

/**
 * A hack to get the term vector of a document in the search results
 */
public class DocTermVector {
    // FIXME Get path from a properties file or pass as argument
    final static String path = "C:\\temp\\nudel\\data\\index";
    static String fieldName = "content";
    static String suchtext = "XML";
    /**
     * Run Searcher with command java -jar de.hspf.ai.kg-<version>.jar <Your search terms> on the console (CMD or PowerShell in Windows)
     *
     * @param args Terms to search for, if left out standard search term is Investition
     */
    public static void main(String args[]) {
        // Use command line arguments instead of preset value as search text
        if (args.length > 0) {
            // Concatenate arguments into one string
            suchtext = null;
            for (String arg : args) suchtext = (suchtext == null) ? arg : suchtext + " " + arg;
        }
        print("Hello, you let me search for '" + suchtext + "'");

        // Try to open Moodle Directory at path
        java.nio.file.Path p = Paths.get(path);
        try {
            // Setup access to the index files
            Directory dir = FSDirectory.open(p);
            DirectoryReader ir = DirectoryReader.open(dir);
            DirectoryReader ir2 = DirectoryReader.open(dir);
            IndexSearcher searcher = new IndexSearcher(ir);
            GermanAnalyzer analyzer = new GermanAnalyzer();

            // Start Searching the Index
            long time = System.currentTimeMillis(); // Remember when the search started
            org.apache.lucene.search.Query query = new org.apache.lucene.queryparser.classic.QueryParser(fieldName, analyzer).parse(suchtext);
            // Search for suchtext and accept 1 million results
            TopDocs docs = searcher.search(query, 100000);
            ScoreDoc[] hits = docs.scoreDocs;
            time = (System.currentTimeMillis() - time); // Measure time needed

            // Display search results and identify top courses
            if (hits.length > 0) {
                print("... I find " + hits.length + " results for search term '" + suchtext + "' (" + time + " ms)");
                // Iterate over results while collecting course ids and aggregating their counts
                time = System.currentTimeMillis(); // Reset time measurement
                HashMap<String, Integer> courses = new HashMap<String, Integer>();
                for (int i = 0; i < hits.length; i++) {
                    int docId = hits[i].doc;
                    Document d = ir.document(docId);
                    print("-------------------------");
                    print("d" + docId);
                    List<IndexableField> felder2 = d.getFields();
                    for( IndexableField feld : felder2) {
                        print(feld.name() + " : " + feld.fieldType() + " :" + feld.stringValue());
                    }
                    // get terms vectors for one document and one field
                    // FIXME klappt nicht... ab hier für...

                    final Fields fields = ir2.getTermVectors(docId);
                    if(fields == null) print("Fields is null");
                    if (fields != null) {
                        Iterator it = fields.iterator();
                        while (it.hasNext()) {
                            Field field = (Field) it.next();
                            Terms terms = ir2.getTermVector(docId, field.name());
                            if (terms != null && terms.size() > 0) {
                                // access the terms for this field
                                TermsEnum termsEnum = terms.iterator();
                                BytesRef term = null;

                                // explore the terms for this field
                                while ((term = termsEnum.next()) != null) {
                                    // enumerate through documents, in this case only one
                                    DocsEnum docsEnum = termsEnum.docs(null, null);
                                    int docIdEnum;
                                    while ((docIdEnum = docsEnum.nextDoc()) != DocIdSetIterator.NO_MORE_DOCS) {
                                        // get the term frequency in the document
                                        System.out.println(term.utf8ToString() + " " + docIdEnum + " " + docsEnum.freq());
                                    }
                                }
                            }
                        }
                    }
                }
//                if (termVector != null) {
//                    System.out.println(docId + " # " + termVector.size());
//                    TermsEnum i = termVector.iterator();
//                    BytesRef term = i.next();
//                    while (term != null) {
//                        print(BytesRefUtils.decode(term));
//                        term = i.next();
//                    }
//                } else {
//                    print("Cannot get Term Vector");
//                }
            } else {
                print("... Nothing found for search term '" + suchtext + "'");
            }
            // Finish up, need to close accessed files explicitly to avoid index corruption
            ir.close();
            ir2.close();
            dir.close();
        } catch (
                IOException e) {
            System.err.println("Path " + path + " contains no valid Lucene Search Index");
        } catch (
                ParseException e2) {
            System.err.println("Query " + suchtext + " cannot be parsed");
        }

    }

    public static void print(String s) {
        System.out.println(s);
    }
//    public class BuildTermDocumentMatrix {
//        public BuildTermDocumentMatrix(File index, File corpus) throws IOException{
//            reader = DirectoryReader.open(FSDirectory.open(index));
//            searcher = new IndexSearcher(reader);
//            this.corpus = corpus;
//            termIdMap = computeTermIdMap(reader);
//        }
//        /**
//         *  Map term to a fix integer so that we can build document matrix later.
//         *  It's used to assign term to specific row in Term-Document matrix
//         */
//        private Map<String, Integer> computeTermIdMap(IndexReader reader) throws IOException {
//            Map<String,Integer> termIdMap = new HashMap<String,Integer>();
//            int id = 0;
//            Fields fields = MultiFields.getFields(reader);
//            Terms terms = fields.terms("contents");
//            TermsEnum itr = terms.iterator(null);
//            BytesRef term = null;
//            while ((term = itr.next()) != null) {
//                String termText = term.utf8ToString();
//                if (termIdMap.containsKey(termText))
//                    continue;
//                //System.out.println(termText);
//                termIdMap.put(termText, id++);
//            }
//
//            return termIdMap;
//        }
//
//        /**
//         *  build term-document matrix for the given directory
//         */
//        public RealMatrix buildTermDocumentMatrix () throws IOException {
//            //iterate through directory to work with each doc
//            int col = 0;
//            int numDocs = countDocs(corpus);            //get the number of documents here
//            int numTerms = termIdMap.size();    //total number of terms
//            RealMatrix tdMatrix = new Array2DRowRealMatrix(numTerms, numDocs);
//
//            for (File f : corpus.listFiles()) {
//                if (!f.isHidden() && f.canRead()) {
//                    //I build term document matrix for a subset of corpus so
//                    //I need to lookup document by path name.
//                    //If you build for the whole corpus, just iterate through all documents
//                    String path = f.getPath();
//                    BooleanQuery pathQuery = new BooleanQuery();
//                    pathQuery.add(new TermQuery(new Term("path", path)), BooleanClause.Occur.SHOULD);
//                    TopDocs hits = searcher.search(pathQuery, 1);
//
//                    //get term vector
//                    Terms termVector = reader.getTermVector(hits.scoreDocs[0].doc, "contents");
//                    TermsEnum itr = termVector.iterator(null);
//                    BytesRef term = null;
//
//                    //compute term weight
//                    while ((term = itr.next()) != null) {
//                        String termText = term.utf8ToString();
//                        int row = termIdMap.get(termText);
//                        long termFreq = itr.totalTermFreq();
//                        long docCount = itr.docFreq();
//                        double weight = computeTfIdfWeight(termFreq, docCount, numDocs);
//                        tdMatrix.setEntry(row, col, weight);
//                    }
//                    col++;
//                }
//            }
//            return tdMatrix;
//        }
//    }
}
