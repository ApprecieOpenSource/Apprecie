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
    var UserService;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            }],
        execute: function() {
            UserService = (function () {
                function UserService() {
                }
                UserService.prototype.getFirstName = function () {
                    if (this._firstName != null) {
                        return this._firstName;
                    }
                    this._firstName = (JSON.parse(sessionStorage.getItem('userRecord')).profile.firstname);
                    return this._firstName;
                };
                UserService.prototype.getActiveRole = function () {
                    if (this._activeRole != null) {
                        return this._activeRole;
                    }
                    this._activeRole = (JSON.parse(sessionStorage.getItem('userRecord')).activeRole.roleName);
                    return this._activeRole;
                };
                UserService.prototype.getLastName = function () {
                    if (this._lastname != null) {
                        return this._lastname;
                    }
                    this._lastname = (JSON.parse(sessionStorage.getItem('userRecord')).profile.lastname);
                    return this._lastname;
                };
                UserService = __decorate([
                    core_1.Injectable(),
                    core_1.Component({}), 
                    __metadata('design:paramtypes', [])
                ], UserService);
                return UserService;
            })();
            exports_1("UserService", UserService);
        }
    }
});
//# sourceMappingURL=userservice.js.map