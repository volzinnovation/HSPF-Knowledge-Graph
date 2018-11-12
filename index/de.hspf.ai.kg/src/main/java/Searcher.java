import org.apache.lucene.analysis.de.GermanAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.DirectoryReader;
import org.apache.lucene.queryparser.classic.ParseException;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.ScoreDoc;
import org.apache.lucene.search.TopDocs;
import org.apache.lucene.store.Directory;
import org.apache.lucene.store.FSDirectory;

import java.io.IOException;
import java.nio.file.Paths;
import java.util.Comparator;
import java.util.HashMap;
import java.util.LinkedHashMap;
import java.util.Map;
import java.util.stream.Collectors;

/**
 * A hack to identify the top k Moodle Courses for a given search term
 * TODO Link to Professor via Database Query based on course id
 */
public class Searcher {
    // FIXME Get path from a properties file or pass as argument
    final static String path = "C:\\Nudel\\nudel\\data\\index";
    static String suchtext = "Orthonormalsystem";

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
            IndexSearcher searcher = new org.apache.lucene.search.IndexSearcher(ir);
            GermanAnalyzer analyzer = new org.apache.lucene.analysis.de.GermanAnalyzer();

            // Start Searching the Index
            long time = System.currentTimeMillis(); // Remember when the search started
            org.apache.lucene.search.Query query = new org.apache.lucene.queryparser.classic.QueryParser("_text_", analyzer).parse(suchtext);
            // Search for suchtext and accept 1 million results
            TopDocs docs = searcher.search(query, 1000000);
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
                    Document d = searcher.doc(docId);
                    // Count Occurences of search results per course
                    String course_id = d.get("courseid");
                    Integer count = courses.containsKey(course_id) ? courses.get(course_id) + 1 : 1;
                    courses.put(course_id, count);
                    // Print each search Result
                    // print((i+1) + ": '"  + d.get("title") + "' in Moodle course #" + d.get("courseid"));
                }

                // Sort HashMap by Counts Descending, long live the Java 8 Stream API
                Map<String, Integer> sortedCourses =
                        courses.entrySet().stream()
                                .sorted(Map.Entry.comparingByValue(Comparator.reverseOrder()))
                                .collect(Collectors.toMap(Map.Entry::getKey, Map.Entry::getValue,
                                        (e1, e2) -> e1, LinkedHashMap::new));
                // Print Top k courses per Search term

                // todo. abfrage an datenbank, input: kurs_ID, output: prof_id
                int top = 10;

                time = (System.currentTimeMillis() - time); // Measure time needed
                print("... I identify the following " + top + " courses, which contain '" + suchtext + "' most often (" + time + " ms)");
                for (String course_id : sortedCourses.keySet()) {
                    if (top == 0) break;
                    Integer count = courses.get(course_id);
                    print(" Course " + course_id + " : " + count);
                    top--;
                }

            } else {
                print("... Nothing found for search term '" + suchtext + "'");
            }
            // Finish up, need to close accessed files explicitly to avoid index corruption
            ir.close();
            dir.close();
        } catch (IOException e) {
            System.err.println("Path " + path + " contains no valid Lucene Search Index");
        } catch (ParseException e2) {
            System.err.println("Query " + suchtext + " cannot be parsed");
        }
    }

        public static void print (String s){
            System.out.println(s);
        }

    }
