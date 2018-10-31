import com.google.common.collect.ImmutableList;
import org.apache.lucene.index.*;
    import org.apache.lucene.misc.HighFreqTerms;
import org.apache.lucene.search.IndexSearcher;
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
    // FIXME Get path from a properties file or pass as argument
    final static String path = "C:\\temp\\nudel\\data\\index";

    /**
     * Run TermCounter
     *
     * @param args[0] field to collect term statistics for, no argument uses _text_ field
     */
    public static void main(String args[]) {
        int k = 100;
        String field = "_text_";
        // Use command line arguments instead of preset value as search text
        if (args.length > 0) {
            // Concatenate arguments into one string
            field = args[1];
        }
        print("Hello, collecting document frequency for terms in field '" + field + "'");

        // Try to open Moodle Directory at path
        java.nio.file.Path p = Paths.get(path);
        try {
            // Setup access to the index files
            Directory dir = FSDirectory.open(p);
            DirectoryReader ir = DirectoryReader.open(dir);
            IndexSearcher searcher = new IndexSearcher(ir);

            // Start Searching the Index
            long time = System.currentTimeMillis(); // Remember when the search started
            HashSet<String> fields = new HashSet<String>();
            List<MyTermStats> terms = getTopTerms(ir, field, k);
            time = System.currentTimeMillis() - time ;

            print("Collecting top " + k + " terms took " + time + " ms:");

            for( MyTermStats t : terms) {
                print(t.toString());
            }
/*
            Map<String, Long> terms = countTerms(ir, fields);
            // overviewModel.getTopTerms(field, numTerms);
            // Sort HashMap, long live the Java 8 Stream API
            Map<String, Long> topTerms =
                    terms.entrySet().stream()
                            .sorted(Map.Entry.comparingByValue(Comparator.reverseOrder()))
                            .collect(Collectors.toMap(Map.Entry::getKey, Map.Entry::getValue,
                                    (e1, e2) -> e1, LinkedHashMap::new));
            // Print Top k courses per Search term
            // int top = 5;

            time = (System.currentTimeMillis() - time); // Measure time needed
           // print("... I identify the following " + top + " courses, which contain '" + suchtext + "' most often (" + time + " ms)");
            for (String t : topTerms.keySet()) {
                // if (top == 0) break;
                Long count = topTerms.get(t);
                print( t + " : " + count);
                // top--;
            }
            */
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
     * @param field - the field name
     * @param numTerms - the max number of terms to be returned
     * @throws Exception - if an error occurs when collecting term statistics
     * @return Terms ordered ny highest document frequency descending.
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
