System.register(['angular2/core', '../../services/authentication', 'rxjs/Rx', "../../services/portalinit", "../../models/login", "angular2/router"], function(exports_1) {
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1, authentication_1, portalinit_1, login_1, router_1;
    var LoginComponent;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            },
            function (authentication_1_1) {
                authentication_1 = authentication_1_1;
            },
            function (_1) {},
            function (portalinit_1_1) {
                portalinit_1 = portalinit_1_1;
            },
            function (login_1_1) {
                login_1 = login_1_1;
            },
            function (router_1_1) {
                router_1 = router_1_1;
            }],
        execute: function() {
            LoginComponent = (function () {
                function LoginComponent(_auth, _router, _portalInit) {
                    this._auth = _auth;
                    this._router = _router;
                    this._portalInit = _portalInit;
                    this.model = new login_1.Login(null, null);
                    this.submitted = false;
                    this.isProcessing = false;
                    this.logo = _portalInit.getLogo();
                    this.error = false;
                }
                LoginComponent.prototype.onSubmit = function () {
                    var _this = this;
                    this.isProcessing = true;
                    this._auth.emailAddress = this.model.emailAddress;
                    this._auth.password = this.model.password;
                    this._auth.loginUser().subscribe(function (response) {
                        if (response.status == 'success') {
                            _this.error = false;
                            sessionStorage.setItem('userRecord', JSON.stringify(response));
                            _this._router.parent.parent.navigate(['ApplicationContainer']);
                        }
                        else {
                            _this.errorMessage = response.message;
                            _this.error = true;
                        }
                        _this.isProcessing = false;
                    });
                    this.submitted = true;
                };
                LoginComponent.prototype.goToRecovery = function () {
                    this._router.parent.navigate(['Recovery']);
                };
                LoginComponent = __decorate([
                    core_1.Component({
                        selector: 'router-outlet',
                        providers: [authentication_1.Authentication],
                        templateUrl: '/a/app/components/login/login.form.html',
                    }), 
                    __metadata('design:paramtypes', [authentication_1.Authentication, router_1.Router, portalinit_1.PortalInit])
                ], LoginComponent);
                return LoginComponent;
            })();
            exports_1("LoginComponent", LoginComponent);
        }
    }
});
//# sourceMappingURL=Login.Component.js.map