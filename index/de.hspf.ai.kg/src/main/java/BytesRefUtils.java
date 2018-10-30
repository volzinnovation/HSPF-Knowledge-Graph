import org.apache.lucene.util.BytesRef;

/**
 * An utility class for handling {@link BytesRef} objects.
 */
public final class BytesRefUtils {

    public static String decode(BytesRef ref) {
        try {
            return ref.utf8ToString();
        } catch (Exception e) {
            return ref.toString();
        }
    }

    private BytesRefUtils() {
    }
}