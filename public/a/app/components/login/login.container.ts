import {Component,OnInit,Renderer} from 'angular2/core';
import {Router,Route,RouteConfig, ROUTER_DIRECTIVES,Location} from 'angular2/router';
import {LoginComponent} from "./Login.Component";
import {RecoveryComponent} from "./Recovery.Component";
import {PortalInit} from "../../services/portalinit";
@Component({
    selector: 'router-outlet',
    templateUrl: '/a/app/layouts/login.html',
    directives: [ROUTER_DIRECTIVES],
    providers: [PortalInit]
})
@RouteConfig([
    {path:'/',         name: 'Login', component: LoginComponent, useAsDefault: true},
    {path:'/recovery',         name: 'Recovery', component: RecoveryComponent}
])
export class LoginContainer implements OnInit{
    public loginBg:String;
    constructor(private _router:Router, private _portalInit:PortalInit){

    }

    ngOnInit(){
        this.loginBg=this._portalInit.getLoginBg();
    }
}