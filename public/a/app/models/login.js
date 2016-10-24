System.register([], function(exports_1) {
    var Login;
    return {
        setters:[],
        execute: function() {
            Login = (function () {
                function Login(emailAddress, password, remember) {
                    this.emailAddress = emailAddress;
                    this.password = password;
                    this.remember = remember;
                }
                return Login;
            })();
            exports_1("Login", Login);
        }
    }
});
//# sourceMappingURL=login.js.map