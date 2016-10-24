System.register(['angular2/core', 'angular2/router', "../../services/portalinit", "../../services/userservice", "../dashboard/dashboard.component", "../../services/roleservice", "../menu/menu.container"], function(exports_1) {
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1, router_1, portalinit_1, userservice_1, dashboard_component_1, roleservice_1, menu_container_1;
    var ApplicationContainer;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            },
            function (router_1_1) {
                router_1 = router_1_1;
            },
            function (portalinit_1_1) {
                portalinit_1 = portalinit_1_1;
            },
            function (userservice_1_1) {
                userservice_1 = userservice_1_1;
            },
            function (dashboard_component_1_1) {
                dashboard_component_1 = dashboard_component_1_1;
            },
            function (roleservice_1_1) {
                roleservice_1 = roleservice_1_1;
            },
            function (menu_container_1_1) {
                menu_container_1 = menu_container_1_1;
            }],
        execute: function() {
            ApplicationContainer = (function () {
                function ApplicationContainer(_portalInit, roleService) {
                    this._portalInit = _portalInit;
                    this.roleService = roleService;
                    this.logo = _portalInit.getLogo();
                    this.activeRoleDescription = roleService.getActiveRoleDescription();
                }
                ApplicationContainer = __decorate([
                    core_1.Component({
                        selector: 'router-outlet',
                        templateUrl: '/a/app/layouts/application.html',
                        directives: [router_1.ROUTER_DIRECTIVES, menu_container_1.MenuContainer],
                        providers: [portalinit_1.PortalInit, userservice_1.UserService, roleservice_1.RoleService]
                    }),
                    router_1.RouteConfig([
                        { path: '/dashboard', name: 'Dashboard', component: dashboard_component_1.DashboardComponent, useAsDefault: true }
                    ]), 
                    __metadata('design:paramtypes', [portalinit_1.PortalInit, roleservice_1.RoleService])
                ], ApplicationContainer);
                return ApplicationContainer;
            })();
            exports_1("ApplicationContainer", ApplicationContainer);
        }
    }
});
//# sourceMappingURL=app.container.js.map