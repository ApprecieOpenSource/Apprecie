System.register(['angular2/core', '../../services/authentication', 'rxjs/Rx', "../../services/portalinit", "angular2/router", "../../models/recovery"], function(exports_1) {
    var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
        var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
        if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
        else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
        return c > 3 && r && Object.defineProperty(target, key, r), r;
    };
    var __metadata = (this && this.__metadata) || function (k, v) {
        if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
    };
    var core_1, authentication_1, portalinit_1, router_1, recovery_1;
    var RecoveryComponent;
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
            function (router_1_1) {
                router_1 = router_1_1;
            },
            function (recovery_1_1) {
                recovery_1 = recovery_1_1;
            }],
        execute: function() {
            RecoveryComponent = (function () {
                function RecoveryComponent(_auth, _router, _portalInit) {
                    this._auth = _auth;
                    this._router = _router;
                    this._portalInit = _portalInit;
                    this.model = new recovery_1.Recovery(null);
                    this.logo = _portalInit.getLogo();
                }
                RecoveryComponent.prototype.goToLogin = function () {
                    this._router.parent.navigate(['Login']);
                };
                RecoveryComponent = __decorate([
                    core_1.Component({
                        selector: 'router-outlet',
                        providers: [authentication_1.Authentication],
                        templateUrl: '/a/app/components/login/recovery.form.html',
                    }), 
                    __metadata('design:paramtypes', [authentication_1.Authentication, router_1.Router, portalinit_1.PortalInit])
                ], RecoveryComponent);
                return RecoveryComponent;
            })();
            exports_1("RecoveryComponent", RecoveryComponent);
        }
    }
});
//# sourceMappingURL=Recovery.Component.js.map