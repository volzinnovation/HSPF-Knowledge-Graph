import com.google.common.collect.ImmutableList;
import org.apache.lucene.analysis.de.GermanAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.*;
import org.apache.lucene.misc.HighFreqTerms;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.ScoreDoc;
import org.apache.lucene.search.TopDocs;
import org.apache.lucene.store.Directory;
import org.apache.lucene.store.FSDirectory;

import java.io.IOException;
import java.nio.file.Paths;
import java.util.*;
import java.util.stream.Collectors;

/**
 * A hack to identify the top k Terms in an index
 */
public class TermCounter {
    static String path = "C:\\temp\\nudel\\data\\index";

    static String x, y;
    static int z, d;

    /**
     * Run TermCounter
     *
     * @param args[0] Source location of Moodle Index
     */
    public static void main(String args[]) {
        int k = 100000000;
        String field = "_text_";
        // Use command line arguments instead of preset value as search text
        if (args.length > 0) {
            // Concatenate arguments into one string
            path = args[0];
        }
        // print("Hello, collecting document frequency for terms in field '" + field + "'");

        // Try to open Moodle Directory at path
        java.nio.file.Path p = Paths.get(path);
        try {
            // Setup access to the index files
            Directory dir = FSDirectory.open(p);
            DirectoryReader ir = DirectoryReader.open(dir);
            IndexSearcher searcher = new IndexSearcher(ir);
            /*
            // Iterate over Documents
            for (int i=0; i<ir.maxDoc(); i++) {
               //print("D# : " + i);
                Document d = ir.document(i);
                String[] terms = ((Document) d).getValues("content");
                for (String t : terms) {
                    print (t);
                    print (",");
                }
            }
            */
            // Start Searching the Index
            long time = System.currentTimeMillis(); // Remember when the search started
            HashSet<String> fields = new HashSet<String>();
            List<MyTermStats> terms = getTopTerms(ir, field, k);
            time = System.currentTimeMillis() - time;

            //print("Collecting top " + k + " terms took " + time + " ms:");
            print("Term,Kurs,TF,DF,TFIDF");
            int i = 1;
            for (MyTermStats t : terms) {
                //  print("#" + i + "\t" + t.toString());
                x = t.getDecodedTermText();
                d = t.getDocFreq();
                // Anfrage Starten
                // Frage nach t mit Facet Field Course ID
                GermanAnalyzer analyzer = new org.apache.lucene.analysis.de.GermanAnalyzer();

                // Start Searching the Index
                long time2 = System.currentTimeMillis(); // Remember when the search started
                TopDocs docs = null;
                try {
                    org.apache.lucene.search.Query query = new org.apache.lucene.queryparser.classic.QueryParser("_text_", analyzer).parse(t.toString());
                    // Search for suchtext and accept 1 million results
                    docs = searcher.search(query, 1000000);
                } catch (org.apache.lucene.queryparser.classic.ParseException pe) {
                    // TODO Handle ParseException
                }
                // Found Something
                if (docs != null) {
                    ScoreDoc[] hits = docs.scoreDocs;
                    i++;

                    if (hits.length > 0) {
                        // Iterate over results while collecting course ids and aggregating their counts
                        time = System.currentTimeMillis(); // Reset time measurement
                        HashMap<String, Integer> courses = new HashMap<String, Integer>();
                        for (int j = 0; j < hits.length; j++) {
                            int docId = hits[j].doc;

                            Document d = searcher.doc(docId);

                            // Count Occurences of search results per course
                            String course_id = d.get("courseid");
                            Integer count = courses.containsKey(course_id) ? courses.get(course_id) + 1 : 1;
                            courses.put(course_id, count);
                            // Print each search Result

                            // print((j + 1) + ": '" + d.get("title") + "' in Moodle course #" + d.get("courseid"));

                        }

                        // Sort HashMap by Counts Descending, long live the Java 8 Stream API
                        Map<String, Integer> sortedCourses =
                                courses.entrySet().stream()
                                        .sorted(Map.Entry.comparingByValue(Comparator.reverseOrder()))
                                        .collect(Collectors.toMap(Map.Entry::getKey, Map.Entry::getValue,
                                                (e1, e2) -> e1, LinkedHashMap::new));
                        // Print Top k courses per Search term
                        // int top = 500000;

                        time = (System.currentTimeMillis() - time); // Measure time needed
                        // print("... I identify the following " + top + " courses, which contain '" + suchtext + "' most often (" + time + " ms)");
                        for (String course_id : sortedCourses.keySet()) {
                            // if (top == 0) break;
                            y = course_id;
                            Integer count = courses.get(course_id);
                            z = count;
                            print(x + "," + y + "," + z + "," + d + "," + ((float) z / (float) d));
                            // print(" Course " + course_id + " : " + count);
                            // top--;
                        }
                    }
                }
            }

            // Finish up, need to close accessed files explicitly to avoid index corruption
            ir.close();
            dir.close();
        } catch (IOException e) {
            System.err.println("Path " + path + " contains no valid Lucene Search Index");
        } catch (Exception e2) {
            System.err.println(e2.getMessage());
            e2.printStackTrace();
        }

    }


    public static void print(String s) {
        System.out.println(s);
    }

    /**
     * Collect all terms and their counts in the specified fields.
     * Method Stolen from https://github.com/DmitryKey/luke/blob/master/src/main/java/org/apache/lucene/luke/util/IndexUtils.java
     *
     * @param reader - index reader
     * @param fields - field names
     * @return a map contains terms and their occurrence frequencies
     * @throws IOException
     */
    public static Map<String, Long> countTerms(IndexReader reader, Collection<String> fields) throws IOException {
        Map<String, Long> res = new HashMap<>();
        for (String field : fields) {
            if (!res.containsKey(field)) {
                res.put(field, 0L);
            }
            Terms terms = MultiFields.getTerms(reader, field);
            if (terms != null) {
                TermsEnum te = terms.iterator();
                while (te.next() != null) {
                    res.put(field, res.get(field) + 1);
                }
            }
        }
        return res;
    }


    /**
     * Returns the top indexed terms with their statistics for the specified field.
     * Stolen from https://github.com/DmitryKey/luke/blob/master/src/main/java/org/apache/lucene/luke/models/overview/TopTerms.java
     *
     * @param field    - the field name
     * @param numTerms - the max number of terms to be returned
     * @return Terms ordered ny highest document frequency descending.
     * @throws Exception - if an error occurs when collecting term statistics
     */
    public static List<MyTermStats> getTopTerms(IndexReader reader, String field, int numTerms) throws Exception {
        Map<String, List<MyTermStats>> topTermsCache = new WeakHashMap<>();
        if (!topTermsCache.containsKey(field) || topTermsCache.get(field).size() < numTerms) {
            org.apache.lucene.misc.TermStats[] stats =
                    HighFreqTerms.getHighFreqTerms(reader, numTerms, field, new HighFreqTerms.DocFreqComparator());

            List<MyTermStats> topTerms = Arrays.stream(stats)
                    .map(MyTermStats::of)
                    .collect(Collectors.toList());

            // cache computed statistics for later uses
            topTermsCache.put(field, topTerms);
        }

        return ImmutableList.copyOf(topTermsCache.get(field));
    }


}
