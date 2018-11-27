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

     static String term, course;

    /* Produce Pattern
     { "id" : "mod_folder-activity-873-solrfile1336477",
      "owneruserid" : { "set" : [ "-1" ] }
     }
    */
    private static String toJSON(String docId, List<String> newOwners) {
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
     * Run Update Owner Ids to produce JSON to upload manually into SolR admin interface
     * or use curl, e.g. for your indexname after saving the output of this script into a file called output.json
     * curl 'http://localhost:8983/solr/indexname/update?commit=true' --data-binary @output.json -H 'Content-type:application/json'
     * @param args 0 Path
     */
    public static void main(String args[]) {
        MappingUtils mu = new MappingUtils();
        // Use command line arguments instead of preset value as search text
        if (args.length > 0) {
            // First argument is the path of the lucene index
            path = args[0];
        }
        // Connecting to Lucene index at given path
        java.nio.file.Path p = Paths.get(path);
        try {
            // Setup access to the index files
            Directory dir = FSDirectory.open(p);
            DirectoryReader ir = DirectoryReader.open(dir);
            print("[");
            for (int j = 0; j < ir.maxDoc(); j++) {
                Document d = ir.document(j);
                String id = d.get("id");
                String course = d.get("courseid");
                String json = toJSON(id, mu.getProfessorIds(course));
                if(json != null) print(json + (j < (ir.maxDoc()-2) ? "," : ""));
            }
            print("]");
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
