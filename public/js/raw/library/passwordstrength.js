/**
 * Created by huwang on 15/10/2015.
 */
function PasswordStrength(element) {
    this.strengthOptions = {};
    this.strengthOptions.common = {
        minChar: 8,
        debug: false,
        onLoad: function() {
            element.pwstrength("forceUpdate");
        }
    };
    this.strengthOptions.rules = {
        scores: {
            wordLength: -100,
            wordUpperLowerCombo: 20,
            wordLetterNumberCombo: 50,
            wordLetterNumberCharCombo: 20
        },
        activated: {
            wordNotEmail: false,
            wordLength: true,
            wordSimilarToUsername: false,
            wordSequences: false,
            wordTwoCharacterClasses: false,
            wordRepetitions: false,
            wordLowercase: false,
            wordUppercase: false,
            wordOneNumber: false,
            wordThreeNumbers: false,
            wordOneSpecialChar: false,
            wordTwoSpecialChar: false,
            wordUpperLowerCombo: true,
            wordLetterNumberCombo: true,
            wordLetterNumberCharCombo: true
        },
        raisePower: 0
    };
    this.strengthOptions.ui = {
        bootstrap2: false,
        colorClasses: ["danger", "warning", "success"],
        showErrors: false,
        scores: [1, 51, 71, 91],
        showVerdictsInsideProgressBar: true,
        verdicts: ["Weak", "Weak", "Medium", "Strong", "Very Strong"]
    };
    element.pwstrength(this.strengthOptions);
}