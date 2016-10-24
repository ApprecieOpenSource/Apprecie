System.register(['angular2/core'], function(exports_1) {
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1;
    var RoleService;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            }],
        execute: function() {
            RoleService = (function () {
                function RoleService() {
                }
                RoleService.prototype.getActiveRoleDescription = function () {
                    if (this._activeRoleDescription != null) {
                        return this._activeRoleDescription;
                    }
                    console.log(JSON.parse(sessionStorage.getItem('userRecord')));
                    this._activeRoleDescription = (JSON.parse(sessionStorage.getItem('userRecord')).activeRole.roleDescription);
                    return this._activeRoleDescription;
                };
                RoleService.prototype.getActiveRoleId = function () {
                    if (this._activeRoleId != null) {
                        return this._activeRoleId;
                    }
                    this._activeRoleId = (JSON.parse(sessionStorage.getItem('userRecord')).activeRole.roleId);
                    return this._activeRoleId;
                };
                RoleService = __decorate([
                    core_1.Injectable(),
                    core_1.Component({}), 
                    __metadata('design:paramtypes', [])
                ], RoleService);
                return RoleService;
            })();
            exports_1("RoleService", RoleService);
        }
    }
});
//# sourceMappingURL=roleservice.js.map