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
import org.apache.lucene.search.Query;
import org.apache.lucene.queryparser.classic.QueryParser;

import java.io.IOException;
import java.math.BigInteger;
import java.nio.file.Paths;
import java.util.*;
import java.util.stream.Collectors;

/**
 * A hack to identify the top k Terms in an index and find the courses that match those terms best.
 *
 * @author Raphael Volz
 */
public class TermCounter {
    static String path = "C:\\temp\\nudel\\data\\index";

    static double min_tfidf =  50000.0; // Filter items with term length adjusted TF IDF larger this value
    static int min_df = 10; // Minimum Document Frequency
    static String term, course;
    static int tf, df;
    static String field = "_text_";

    /**
     * Run TermCounter
     *
     * @param args 0 Path
     * @param args 1 Minimum Length-adjusted TFIDF
     * @param args 2 Minimum document frequency
     * @param args 3 Field to search for, default "_text_"
     */
    public static void main(String args[]) {
        // Use command line arguments instead of preset value as search text
        if (args.length > 0) {
            // First argument is the path of the lucene index
            path = args[0];
        }
        if (args.length > 1) {
            // Second argument is the minimum value for the length adjusted tf idf
            try {
                min_tfidf = Double.parseDouble(args[1]);
            } catch (NumberFormatException nfe) {
                System.err.println("Second argument, Minimum value for L_TFIDF is not a number, defaulting to " + min_tfidf);
            }
        }
        if (args.length > 2) {
            // Third argument is the minimum value for document frequency
            try {
                min_df = Integer.parseInt(args[2]);
            } catch (NumberFormatException nfe) {
                System.err.println("Third argument, Minimum value for document frequency is not a integer, defauling to " + min_df);
            }
        }
        if (args.length > 3) {
            // Third argument is the minimum value for document frequency
                field = args[3];
        }
        // Connecting to Lucene index at given path
        java.nio.file.Path p = Paths.get(path);
        try {
            // Setup access to the index files
            Directory dir = FSDirectory.open(p);
            DirectoryReader ir = DirectoryReader.open(dir);
            int N = ir.getDocCount(field); // Number of Documents in the Index
            IndexSearcher searcher = new IndexSearcher(ir);

            // Find k Top Terms in the index
            long time = System.currentTimeMillis(); // Remember when the search started
            List<MyTermStats> terms = getTopTerms(ir, field, 100000000); // Max 100 Mio. Top Terms
            time = System.currentTimeMillis() - time;
            //print("Collecting top " + k + " terms took " + time + " ms:");

            // Create Analyzer for querying the index
            GermanAnalyzer analyzer = new org.apache.lucene.analysis.de.GermanAnalyzer();

            // Write Header for CSV File
            print("Nr,Term,Kurs,TF,DF,TFIDF,TermLength,LEN_TFIDF");
            // Write rows of CSV file
            long i = 1L; // Count processed document-term combinations
            // Iterate over all top terms
            for (MyTermStats t : terms) {
                term = t.getDecodedTermText();
                df = t.getDocFreq();
                // Filter terms that
                // do not meet minimum document frequency
                // or make no sense, because they contain contain characters such as  .,_:
                if ( df >= min_df && !term.contains(",")  && !term.contains(":") && !term.contains("_") && !term.contains(".") ){
                    // Check that Term is not a floating point number
                    boolean isNumber = true;
                    try {
                        Float.parseFloat(term);
                    } catch (NumberFormatException nfe) {
                        isNumber = false;
                    }
                    if(!isNumber) {
                        // Check that Term is not a hexadecimal integer
                        isNumber = true;
                        try {
                            BigInteger bi = new BigInteger(term, 16);
                        } catch (NumberFormatException nfe) {
                            isNumber = false;
                        }
                    }
                    // If not a number, search all documents with this term
                    if (!isNumber) {

                        // Start Searching the Index
                        long time2 = System.currentTimeMillis(); // Remember when the search started
                        TopDocs docs = null;

                        // Search for term and accept 1 million results
                        try {
                            Query query = new QueryParser(field, analyzer).parse(t.toString());
                            docs = searcher.search(query, 1000000);
                        } catch (org.apache.lucene.queryparser.classic.ParseException pe) {
                            // TODO Handle ParseException, something went wrong while searching
                        }
                        // Found documents for the search term
                        if (docs != null) {
                            ScoreDoc[] hits = docs.scoreDocs;
                            if (hits.length > 0) {
                                time = System.currentTimeMillis(); // Reset time measurement
                                // Aggregate term frequencies by courses in map courses
                                HashMap<String, Integer> courses = new HashMap<String, Integer>();
                                // Aggregate term frequencies by course id
                                // TODO Could make this more effective and push to Lucene using faceted search instead of plain search
                                for (int j = 0; j < hits.length; j++) {
                                    int docId = hits[j].doc;
                                    Document d = searcher.doc(docId);
                                    // Count Occurences of search results per course
                                    String course_id = d.get("courseid");
                                    Integer count = courses.containsKey(course_id) ? courses.get(course_id) + 1 : 1;
                                    courses.put(course_id, count);
                                    // Print each search Result
                                    // print((j + 1) + ": '" + d.get("title") + "' course #" + d.get("courseid"));
                                }
                                /*
                                // Sort Courses by Counts Descending, long live the Java 8 Stream API
                                Map<String, Integer> sortedCourses =
                                        courses.entrySet().stream()
                                                .sorted(Map.Entry.comparingByValue(Comparator.reverseOrder()))
                                                .collect(Collectors.toMap(Map.Entry::getKey, Map.Entry::getValue,
                                                        (e1, e2) -> e1, LinkedHashMap::new));
                                // Print Top k courses per Search term
                                // int top = 500000;
                                time = (System.currentTimeMillis() - time); // Measure time needed
                                // print("... I identify the following " + top + " courses, which contain '" + suchtext + "' most often (" + time + " ms)");
                                */
                                // Iterate over all courses relevant to search term
                                for (String course_id : courses.keySet()) {
                                    i++;
                                    course = course_id;
                                    tf = courses.get(course_id);
                                    double idf = Math.log( (double) N / ( 1 + df ));
                                    double tfidf = (double) tf * idf;
                                    int term_length = term.length();
                                    double tl_tfidf = tfidf * term_length;
                                    // If the minimum value for the metric is met, produce a line for the CSV file
                                    if (tl_tfidf > min_tfidf) {
                                        print(i + "," + term + "," + course + "," + tf + "," + df + "," + tfidf + "," + term_length + "," + tl_tfidf);

                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Finish up, need to close accessed Lucene index files explicitly to avoid index corruption
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
