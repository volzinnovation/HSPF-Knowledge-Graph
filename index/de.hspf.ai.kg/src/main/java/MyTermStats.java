import org.apache.lucene.misc.TermStats;

/**
 * Holder for statistics for a term in a specific field.
 */
public final class MyTermStats {

    private final String decodedTermText;

    private final String field;

    private final int docFreq;


    /**
     * Returns a TermStats instance representing the specified {@link org.apache.lucene.misc.TermStats} value.
     */
    static MyTermStats of(org.apache.lucene.misc.TermStats stats) {
        String termText = BytesRefUtils.decode(stats.termtext);
        return new MyTermStats(termText, stats.field, stats.docFreq);
    }

    private MyTermStats(String decodedTermText, String field, int docFreq) {
        this.decodedTermText = decodedTermText;
        this.field = field;
        this.docFreq = docFreq;
    }

    /**
     * Returns the string representation for this term.
     */
    public String getDecodedTermText() {
        return decodedTermText;
    }

    /**
     * Returns the field name.
     */
    public String getField() {
        return field;
    }

    /**
     * Returns the document frequency of this term.
     */
    public int getDocFreq() {
        return docFreq;
    }

    @Override
    public String toString() {
        return  decodedTermText + "\t" + docFreq ;
    }
}
