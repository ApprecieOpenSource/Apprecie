System.register(['angular2/core', 'angular2/http', 'angular2/router', "./portalinit"], function(exports_1) {
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1, http_1, core_2, router_1, portalinit_1;
    var Authentication;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
                core_2 = core_1_1;
            },
            function (http_1_1) {
                http_1 = http_1_1;
            },
            function (router_1_1) {
                router_1 = router_1_1;
            },
            function (portalinit_1_1) {
                portalinit_1 = portalinit_1_1;
            }],
        execute: function() {
            Authentication = (function () {
                function Authentication(_http, _router, _portalInit) {
                    this._http = _http;
                    this._router = _router;
                    this._portalInit = _portalInit;
                }
                Authentication.prototype.initialise = function () {
                    var _this = this;
                    this.getToken().subscribe(function (response) {
                        if (response.loggedIn != false || response.loggedIn == true) {
                            sessionStorage.setItem('userRecord', JSON.stringify(response));
                        }
                        _this.hasSessionOrRedirect();
                    });
                };
                Authentication.prototype.getToken = function () {
                    sessionStorage.removeItem('userRecord');
                    return this._http.get('/login/getAuthenticatedUser').map(function (res) { return res.json(); });
                };
                Authentication.prototype.loginUser = function () {
                    sessionStorage.removeItem('userRecord');
                    return this._http.post('/apiex/login', JSON.stringify({ 'emailAddress': this.emailAddress, 'password': this.password, 'CSRF_SESSION_TOKEN': this._portalInit.getCsrf(), 'remember': this.remember }))
                        .map((function (res) { return res.json(); }));
                };
                Authentication.prototype.hasSessionOrRedirect = function () {
                    if (sessionStorage.getItem('userRecord') == null) {
                        this._router.navigate(['LoginContainer']);
                    }
                    else {
                        this._router.navigate(['ApplicationContainer']);
                    }
                };
                Authentication.prototype.logout = function () {
                    sessionStorage.removeItem('userRecord');
                    return true;
                };
                Authentication = __decorate([
                    core_1.Injectable(),
                    core_2.Component({
                        providers: [http_1.Http]
                    }), 
                    __metadata('design:paramtypes', [http_1.Http, router_1.Router, portalinit_1.PortalInit])
                ], Authentication);
                return Authentication;
            })();
            exports_1("Authentication", Authentication);
        }
    }
});
//# sourceMappingURL=authentication.js.map