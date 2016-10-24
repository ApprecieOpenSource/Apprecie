import {Component,View,Input} from 'angular2/core';
import {Router,RouteConfig, ROUTER_DIRECTIVES} from 'angular2/router';
import {Authentication} from '../services/authentication';
import {LoginContainer} from './login/login.container';
import {ApplicationContainer} from './app/app.container';
import 'rxjs/Rx';
import {PortalInit} from "../services/portalinit";
import {NoPage} from './error/nopage.component';
@Component({
    selector: 'my-app',
    directives: [ROUTER_DIRECTIVES],
    providers: [Authentication,PortalInit],
    templateUrl: '/a/app/layouts/blank.html'
})
@RouteConfig([
    {path:'/login/...', name: 'LoginContainer', component: LoginContainer},
    {path:'/portal/...', name: 'ApplicationContainer', component: ApplicationContainer},
    {path:'/error/nopage', name: 'NoPage', component: NoPage},
    {path: '/**', redirectTo: ['NoPage'] }

])
export class App{
    constructor(public authentication:Authentication, public portalinit:PortalInit, private _router:Router){
        if(!this.portalinit.isInitialised()){
            this.portalinit.getPortal().subscribe(response  => {
                sessionStorage.setItem('portalInit', JSON.stringify(response));
                this._router.navigate(['LoginContainer']);
            });
        }
        else{
            this.authentication.hasSessionOrRedirect();
        }
    }
}