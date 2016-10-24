System.register(['angular2/core', 'angular2/router', '../services/authentication', './login/login.container', './app/app.container', 'rxjs/Rx', "../services/portalinit", './error/nopage.component'], function(exports_1) {
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1, router_1, authentication_1, login_container_1, app_container_1, portalinit_1, nopage_component_1;
    var App;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            },
            function (router_1_1) {
                router_1 = router_1_1;
            },
            function (authentication_1_1) {
                authentication_1 = authentication_1_1;
            },
            function (login_container_1_1) {
                login_container_1 = login_container_1_1;
            },
            function (app_container_1_1) {
                app_container_1 = app_container_1_1;
            },
            function (_1) {},
            function (portalinit_1_1) {
                portalinit_1 = portalinit_1_1;
            },
            function (nopage_component_1_1) {
                nopage_component_1 = nopage_component_1_1;
            }],
        execute: function() {
            App = (function () {
                function App(authentication, portalinit, _router) {
                    var _this = this;
                    this.authentication = authentication;
                    this.portalinit = portalinit;
                    this._router = _router;
                    if (!this.portalinit.isInitialised()) {
                        this.portalinit.getPortal().subscribe(function (response) {
                            sessionStorage.setItem('portalInit', JSON.stringify(response));
                            _this._router.navigate(['LoginContainer']);
                        });
                    }
                    else {
                        this.authentication.hasSessionOrRedirect();
                    }
                }
                App = __decorate([
                    core_1.Component({
                        selector: 'my-app',
                        directives: [router_1.ROUTER_DIRECTIVES],
                        providers: [authentication_1.Authentication, portalinit_1.PortalInit],
                        templateUrl: '/a/app/layouts/blank.html'
                    }),
                    router_1.RouteConfig([
                        { path: '/login/...', name: 'LoginContainer', component: login_container_1.LoginContainer },
                        { path: '/portal/...', name: 'ApplicationContainer', component: app_container_1.ApplicationContainer },
                        { path: '/error/nopage', name: 'NoPage', component: nopage_component_1.NoPage },
                        { path: '/**', redirectTo: ['NoPage'] }
                    ]), 
                    __metadata('design:paramtypes', [authentication_1.Authentication, portalinit_1.PortalInit, router_1.Router])
                ], App);
                return App;
            })();
            exports_1("App", App);
        }
    }
});
//# sourceMappingURL=app.js.map