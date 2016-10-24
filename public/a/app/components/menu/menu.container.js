System.register(['angular2/core', "../../services/portalinit", "./systemadministrator.component", "../../services/userservice"], function(exports_1) {
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1, portalinit_1, systemadministrator_component_1, userservice_1;
    var MenuContainer;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            },
            function (portalinit_1_1) {
                portalinit_1 = portalinit_1_1;
            },
            function (systemadministrator_component_1_1) {
                systemadministrator_component_1 = systemadministrator_component_1_1;
            },
            function (userservice_1_1) {
                userservice_1 = userservice_1_1;
            }],
        execute: function() {
            MenuContainer = (function () {
                function MenuContainer(dcl, elementRef, _userService) {
                    this._userService = _userService;
                    switch (_userService.getActiveRole()) {
                        case 'SystemAdministrator':
                            dcl.loadIntoLocation(systemadministrator_component_1.SystemAdministratorMenu, elementRef, 'menuitems');
                            break;
                    }
                }
                MenuContainer.prototype.ngOnInit = function () {
                };
                MenuContainer = __decorate([
                    core_1.Component({
                        selector: 'app-menu',
                        templateUrl: '/a/app/layouts/menu.html',
                        providers: [portalinit_1.PortalInit]
                    }), 
                    __metadata('design:paramtypes', [core_1.DynamicComponentLoader, core_1.ElementRef, userservice_1.UserService])
                ], MenuContainer);
                return MenuContainer;
            })();
            exports_1("MenuContainer", MenuContainer);
        }
    }
});
//# sourceMappingURL=menu.container.js.map