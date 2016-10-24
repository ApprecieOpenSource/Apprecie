System.register(['angular2/core', 'angular2/router', "../../services/userservice", "../../services/authentication"], function(exports_1) {
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1, router_1, userservice_1, authentication_1;
    var SystemAdministratorMenu;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            },
            function (router_1_1) {
                router_1 = router_1_1;
            },
            function (userservice_1_1) {
                userservice_1 = userservice_1_1;
            },
            function (authentication_1_1) {
                authentication_1 = authentication_1_1;
            }],
        execute: function() {
            SystemAdministratorMenu = (function () {
                function SystemAdministratorMenu(_router, _userService, _authentication) {
                    this._router = _router;
                    this._userService = _userService;
                    this._authentication = _authentication;
                    this.firstName = _userService.getFirstName();
                    this.lastName = _userService.getLastName();
                }
                SystemAdministratorMenu.prototype.logout = function () {
                    if (this._authentication.logout()) {
                        this._router.parent.navigate(['LoginContainer']);
                    }
                };
                SystemAdministratorMenu = __decorate([
                    core_1.Component({
                        selector: 'menu-items',
                        templateUrl: '/a/app/components/menu/systemAdministrator.html',
                        directives: [router_1.ROUTER_DIRECTIVES]
                    }), 
                    __metadata('design:paramtypes', [router_1.Router, userservice_1.UserService, authentication_1.Authentication])
                ], SystemAdministratorMenu);
                return SystemAdministratorMenu;
            })();
            exports_1("SystemAdministratorMenu", SystemAdministratorMenu);
        }
    }
});
//# sourceMappingURL=systemadministrator.component.js.map