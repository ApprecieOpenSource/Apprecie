System.register(['angular2/core', 'angular2/router', "./Login.Component", "./Recovery.Component", "../../services/portalinit"], function(exports_1) {
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1, router_1, Login_Component_1, Recovery_Component_1, portalinit_1;
    var LoginContainer;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            },
            function (router_1_1) {
                router_1 = router_1_1;
            },
            function (Login_Component_1_1) {
                Login_Component_1 = Login_Component_1_1;
            },
            function (Recovery_Component_1_1) {
                Recovery_Component_1 = Recovery_Component_1_1;
            },
            function (portalinit_1_1) {
                portalinit_1 = portalinit_1_1;
            }],
        execute: function() {
            LoginContainer = (function () {
                function LoginContainer(_router, _portalInit) {
                    this._router = _router;
                    this._portalInit = _portalInit;
                }
                LoginContainer.prototype.ngOnInit = function () {
                    this.loginBg = this._portalInit.getLoginBg();
                };
                LoginContainer = __decorate([
                    core_1.Component({
                        selector: 'router-outlet',
                        templateUrl: '/a/app/layouts/login.html',
                        directives: [router_1.ROUTER_DIRECTIVES],
                        providers: [portalinit_1.PortalInit]
                    }),
                    router_1.RouteConfig([
                        { path: '/', name: 'Login', component: Login_Component_1.LoginComponent, useAsDefault: true },
                        { path: '/recovery', name: 'Recovery', component: Recovery_Component_1.RecoveryComponent }
                    ]), 
                    __metadata('design:paramtypes', [router_1.Router, portalinit_1.PortalInit])
                ], LoginContainer);
                return LoginContainer;
            })();
            exports_1("LoginContainer", LoginContainer);
        }
    }
});
//# sourceMappingURL=login.container.js.map