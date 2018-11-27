import com.google.common.collect.ImmutableList;
import org.apache.lucene.analysis.de.GermanAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.*;
import org.apache.lucene.misc.HighFreqTerms;
import org.apache.lucene.queryparser.classic.QueryParser;
import org.apache.lucene.search.*;
import org.apache.lucene.store.Directory;
import org.apache.lucene.store.FSDirectory;

import java.io.IOException;
import java.math.BigInteger;
import java.nio.file.Paths;
import java.util.*;
import java.util.stream.Collectors;

/**
 * A hack to update the Solr Index with the correct owner ids (0 at the time being), should be fixed in Moodle Search PlugIn instead
 * <p>
 * Need to manually change the SolR managed-schema to make owneruserid a multi valued field first
 * <p>
 * Produced JSON file to upload in SolR Admin interface to update the field values
 *
 * @author Raphael Volz
 */
public class UpdateOwnerId {
    static String path = "C:\\temp\\nudel\\data\\index";

    static double min_tfidf = 50000.0; // Filter items with term length adjusted TF IDF larger this value
    static int min_df = 10; // Minimum Document Frequency
    static String term, course;
    static int tf, df;
    static String field = "courseid";

    /* Produce Pattern
     { "id" : "mod_folder-activity-873-solrfile1336477",
      "owneruserid" : { "set" : [ "-1" ] }
     }
    */
    public static String toJSON(String docId, List<String> newOwners) {
        if(newOwners == null || newOwners.size()==0 ) return null;
        String ret = "{ \"id\" : \"" + docId + "\",\"owneruserid\" : { \"set\" : [";
        for (String s : newOwners) {
            ret += "\"" + s + "\",";
        }
        ret = ret.substring(0, ret.length() - 1); // remove trailing ","
        ret += "]}}";
        return ret;

    }

    /**
     * Run Update Owner Ids
     *
     * @param args 0 Path
     * @param args 1 Minimum Length-adjusted TFIDF
     */
    public static void main(String args[]) {
        MappingUtils mu = new MappingUtils();
        // Use command line arguments instead of preset value as search text
        if (args.length > 0) {
            // First argument is the path of the lucene index
            path = args[0];
        }
        print("Courses: " + mu.getCourseIds().size());
        // Connecting to Lucene index at given path
        java.nio.file.Path p = Paths.get(path);
        try {
            // Setup access to the index files
            Directory dir = FSDirectory.open(p);
            DirectoryReader ir = DirectoryReader.open(dir);
            for (int j = 0; j < ir.maxDoc(); j++) {
            /*
            IndexSearcher searcher = new IndexSearcher(ir);
            GermanAnalyzer analyzer = new org.apache.lucene.analysis.de.GermanAnalyzer();

            // for (String course : mu.getCourseIds()) {
            print("Course: " + course);
            TopDocs docs = null;
            // Search for term and accept 1 million results

            // Query query = new QueryParser(field, analyzer).parse(course);
            Query query = new FieldValueQuery(field);
            docs = searcher.search(query, 1000000);
            docs = ir.d
            // Found documents for the search term
            if (docs != null) {
                ScoreDoc[] hits = docs.scoreDocs;
                print(" Hits: " + hits.length);
                if (hits.length > 0) {
                    // Aggregate term frequencies by courses in map courses
                    HashMap<String, Integer> courses = new HashMap<String, Integer>();
                    // Aggregate term frequencies by course id
                    // TODO Could make this more effective and push to Lucene using faceted search instead of plain search
                    for (int j = 0; j < hits.length; j++) {
                        int docId = hits[j].doc;
                        */
                //Document d = searcher.doc(docId);
                Document d = ir.document(j);
                // Count Occurences of search results per course
                String id = d.get("id");
                String course = d.get("courseid");
                String json = toJSON(id, mu.getProfessorIds(course));
                if(json != null) print(json + (j < (ir.maxDoc()-2) ? "," : ""));
                // Print each search Result
                // print((j + 1) + ": '" + d.get("title") + "' course #" + d.get("courseid"));


            }
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
}
