import org.languagetool.AnalyzedSentence;
import org.languagetool.AnalyzedToken;
import org.languagetool.AnalyzedTokenReadings;
import org.languagetool.JLanguageTool;
import org.languagetool.language.GermanyGerman;
import org.languagetool.rules.RuleMatch;

import java.io.IOException;
import java.util.List;

public class LanguageToolTest {

    public static void main(String args[]) {

        JLanguageTool langTool = new JLanguageTool(new GermanyGerman());
        // Dictiónary in org\languagetool\resource\de\hunspell\de_DE.dict
// comment in to use statistical ngram data:
//langTool.activateLanguageModelRules(new File("/data/google-ngram-data"));
        List<RuleMatch> matches = null;
        try {
            String text = "Ich ist dähmlich.";
            matches = langTool.check(text);
            List<AnalyzedSentence> analyzedSentences = langTool.analyzeText(text);
            for(AnalyzedSentence s : analyzedSentences) {
                AnalyzedTokenReadings[] tokens = s.getTokens();
                for(AnalyzedTokenReadings tr : tokens) {
                    List<AnalyzedToken> readings = tr.getReadings();
                    for(AnalyzedToken t : readings) {
                        if (t != null && t.getPOSTag() != null && t.getPOSTag().startsWith("SUB:NOM")) {
                            System.out.println("POS: " + t.getPOSTag());
                            System.out.println("Lemma: " + t.getLemma());
                            System.out.println("Token: " + t.getToken());
                        }
                    }
                }

            }


            for (RuleMatch match : matches) {
                System.out.println("Potential error at characters " +
                        match.getFromPos() + "-" + match.getToPos() + ": " +
                        match.getMessage());
                System.out.println("Suggested correction(s): " +
                        match.getSuggestedReplacements());
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
