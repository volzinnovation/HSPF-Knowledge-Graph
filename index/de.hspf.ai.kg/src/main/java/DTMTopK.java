import org.jetbrains.annotations.NotNull;

import java.io.BufferedReader;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStreamReader;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.*;
import java.util.concurrent.ConcurrentSkipListSet;
import java.nio.file.Path;
/**
 * Filters the Top K Topics per Professor from a DTM CSV File produced by TermCounter
 * Produces a new CSV File with ProfID, Term, Metric for import into the database
 *
 * @author Raphael Volz
 *
 */
public class DTMTopK {


    static String path = "C:\\temp\\20181128dtm_4_100_10.csv";
    static int k = 10; // Top Topics per Prof

    // Filter for DTM File
    final int TF = 4;
    final int TFIDF = 6;
    final int LEN = 7;
    final int LEN_TFIDF = 8;
    final int DF = 5;

    private HashMap<String, ConcurrentSkipListSet> pt = new HashMap<>();

    class TermMetric implements Comparable<TermMetric> {
        String term;
        Long metric;

        TermMetric(String term, Long metric) {
            this.term = term;
            this.metric = metric;
        }

        @Override
        public int compareTo(@NotNull TermMetric o) {
            if(this.term.compareTo(o.term) == 0) return 0;
            return this.metric.compareTo(o.metric);
        }
    }

    private void init(Path path) {
        BufferedReader br = null;
        String line = "";
        String cvsSplitBy = ",";
        try {
            br = new BufferedReader(new InputStreamReader(Files.newInputStream(path)));
            while ((line = br.readLine()) != null) {
                // use comma as separator
                // print("Nr 0,Term 1,Kurs 2,Prof 3,TF 4,DF 5,TFIDF 6,TermLength 7 ,LEN_TFIDF 8");
                String[] cols = line.split(cvsSplitBy);
                String term = cols[1].trim();
                // TODO Push this condition into DTM processing / Stop Words
                if(!term.startsWith("Zusammenfassung") && !term.startsWith("Übung")) {
                    String prof_id = cols[2].trim(); // 3 für Prof 2 für Kurs
                    boolean isNumber = true;
                    Long metric = 0L;
                    try {
                        metric = Long.parseLong(cols[TFIDF]);
                    } catch (NumberFormatException nfe) {
                        // No Problem it's the first line
                        isNumber = false;
                    }
                    if (isNumber) {
                        // Build Prof -> Term Mapping
                        ConcurrentSkipListSet<TermMetric> c = pt.containsKey(prof_id) ? pt.get(prof_id) : new ConcurrentSkipListSet<TermMetric>();
                        TermMetric tm = new TermMetric(term, metric);
                        if(!c.contains(tm)) {
                            c.add(tm);
                            if (c.size() > k) c.pollFirst();
                            pt.put(prof_id, c);
                        }
                    }
                }
            }

        } catch (FileNotFoundException e) {
            e.printStackTrace();
        } catch (IOException e) {
            e.printStackTrace();
        } finally {
            if (br != null) {
                try {
                    br.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            }
        }
    }

    public DTMTopK(Path path) {

        init(path);
    }

    /**
     * Command line arguments
     * args[0]  path to the CSV file to process
     * args[1]  k number of topics to keep
     * @param args
     */
    public static void main(String args[]) {
        if (args.length > 0 ) {
            path = args[0];
        }
        if (args.length > 1 ) {
            try {
                k = Integer.parseInt(args[1]);
            } catch (NumberFormatException nfe) {

            }
        }
        java.nio.file.Path p = Paths.get(path);
        DTMTopK me = new DTMTopK(p);

        // System.out.println("# Professors with Topics: " + me.pt.size());
        System.out.println("Prof_ID,Term,Metric");
        //for(String profid : me.pt.keySet()) {
        String profid = "823"; // Raphael Volz
        Iterator<TermMetric> i = me.pt.get(profid).iterator();
        while (i.hasNext()) {
            TermMetric tm = i.next();
            System.out.println(profid + "," + tm.term + "," + tm.metric);
        }
        //}
    }
}
