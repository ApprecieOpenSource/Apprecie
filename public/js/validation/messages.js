/**
 * Created by hu86 on 18/08/2015.
 */
function validateMessage(subject, body) {
    if (subject == '') {
        errors.push('Please enter a subject');
    }

    if (subject.length > 100) {
        errors.push('Your subject is too long. Please keep it below 100 characters')
    }

    if (body == '') {
        errors.push('Please enter a message');
    }
}