import java.io.BufferedReader;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Set;

/*
    Small utility to map between Moodle professor ids (aka. people in Trainer role) and course ids
    @author Raphael Volz
 */
public class MappingUtils {
        // Maps between courses-> professors
        private HashMap<String, List> cp = new HashMap<>();
        // Maps between professors -> courses
        private HashMap<String, List> pc = new HashMap<>();

        private void init() {
            // Mapping Datei einlesen
            String csvFile = "user_course.csv";
            BufferedReader br = null;
            String line = "";
            String cvsSplitBy = ",";
            try {
                br = new BufferedReader(new InputStreamReader(this.getClass().getResourceAsStream(csvFile)));
                while ((line = br.readLine()) != null) {
                    // use comma as separator
                    String[] cols = line.split(cvsSplitBy);
                    String course_id = cols[1].trim();
                    String prof_id = cols[0].trim();
                    // Build Course -> Prof Mapping
                    List<String> p = cp.containsKey(course_id) ? cp.get(course_id) : new LinkedList<String>();
                    p.add(prof_id);
                    cp.put(course_id, p);
                    // Build Prof -> Course Mapping
                    List<String> c = pc.containsKey(prof_id) ? pc.get(prof_id) : new LinkedList<String>();
                    c.add(course_id);
                    pc.put(prof_id, c);
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

        public MappingUtils() {
            init();
        }

    /**
     * Get the list of professor ids for a given course_id
     * @param course_id
     * @return List of professor id Strings
     */
    public List<String> getProfessorIds(String course_id) {
        return cp.get(course_id.trim());
    }
    /**
     * Get the list of professor ids for a given course_id
     * @param prof_id
     * @return List of course id Strings
     */
    public List<String> getCourseIds(String prof_id) {
        return pc.get(prof_id.trim());
    }

    public Set<String> getProfessorIds() {
        return pc.keySet();
    }
    public Set<String> getCourseIds() {
        return cp.keySet();
    }

    public static void main(String args[]) {
        MappingUtils me = new MappingUtils();
        System.out.println("# Professors: " + me.getProfessorIds().size());
        System.out.println("# Courses: " + me.getCourseIds().size());
        System.out.println("# Kurs 410 sollte sein 67 ? " + me.getProfessorIds("410"));
    }
}
