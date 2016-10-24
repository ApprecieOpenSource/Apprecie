System.register(['angular2/core', 'angular2/http'], function(exports_1) {
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1, http_1;
    var PortalInit;
    return {
        setters:[
            function (core_1_1) {
                core_1 = core_1_1;
            },
            function (http_1_1) {
                http_1 = http_1_1;
            }],
        execute: function() {
            PortalInit = (function () {
                function PortalInit(_http) {
                    this._http = _http;
                }
                PortalInit.prototype.getPortal = function () {
                    sessionStorage.removeItem('portalSettings');
                    return this._http.get('/apiex/portalinit').map(function (res) { return res.json(); });
                };
                PortalInit.prototype.isInitialised = function () {
                    if (sessionStorage.getItem('portalInit') == null) {
                        return false;
                    }
                    return true;
                };
                PortalInit.prototype.getLogo = function () {
                    if (this._logo != null) {
                        return this._logo;
                    }
                    this._logo = (JSON.parse(sessionStorage.getItem('portalInit')).logo);
                    return this._logo;
                };
                PortalInit.prototype.getStyles = function () {
                    if (this._styles != null) {
                        return this._styles;
                    }
                    this._styles = (JSON.parse(sessionStorage.getItem('portalInit')).styles);
                    return this._styles;
                };
                PortalInit.prototype.getAssetsDir = function () {
                    if (this._assetsDir != null) {
                        return this._assetsDir;
                    }
                    this._assetsDir = (JSON.parse(sessionStorage.getItem('portalInit')).assetsDir);
                    return this._assetsDir;
                };
                PortalInit.prototype.getCsrf = function () {
                    if (this._csrf != null) {
                        return this._csrf;
                    }
                    this._csrf = (JSON.parse(sessionStorage.getItem('portalInit')).csrf);
                    return this._csrf;
                };
                PortalInit.prototype.getLoginBg = function () {
                    if (this._loginBg != null) {
                        return this._loginBg;
                    }
                    this._loginBg = (JSON.parse(sessionStorage.getItem('portalInit')).loginBg);
                    return this._loginBg;
                };
                PortalInit = __decorate([
                    core_1.Injectable(),
                    core_1.Component({
                        providers: [http_1.Http],
                    }), 
                    __metadata('design:paramtypes', [http_1.Http])
                ], PortalInit);
                return PortalInit;
            })();
            exports_1("PortalInit", PortalInit);
        }
    }
});
//# sourceMappingURL=portalinit.js.map