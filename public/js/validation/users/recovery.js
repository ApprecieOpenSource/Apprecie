/**
 * Created by huwang on 15/10/2015.
 */
function validatePassword(password,confirmPassword){
    if(password=='' || password==null || confirmPassword=='' || confirmPassword==null){
        errors.push('You must provide a password');
    }
    else if(password!=confirmPassword){
        errors.push('Password and confirm password do not match');
    }
    else if(password.length<8 || password.length>25){
        errors.push('Password must be between 8 and 25 characters');
    }
    else if(!password.match(/([a-zA-Z])/) || !password.match(/([0-9])/)) {
        errors.push('Password must include at least one number and one letter');
    }
}