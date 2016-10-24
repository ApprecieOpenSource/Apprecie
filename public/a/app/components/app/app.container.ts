import {Component,Renderer} from 'angular2/core';
import {Router,Route,RouteConfig, ROUTER_DIRECTIVES} from 'angular2/router';
import {PortalInit} from "../../services/portalinit";
import {UserService} from "../../services/userservice";
import {DashboardComponent} from "../dashboard/dashboard.component";
import {RoleService} from "../../services/roleservice";
import {MenuContainer} from "../menu/menu.container";
@Component({
    selector: 'router-outlet',
    templateUrl: '/a/app/layouts/application.html',
    directives: [ROUTER_DIRECTIVES,MenuContainer],
    providers: [PortalInit,UserService,RoleService]
})
@RouteConfig([
    {path:'/dashboard', name: 'Dashboard', component: DashboardComponent, useAsDefault: true}
])
export class ApplicationContainer{
    public logo:String;
    public activeRoleDescription:String;
    constructor(private _portalInit:PortalInit,public roleService:RoleService){
        this.logo=_portalInit.getLogo();
        this.activeRoleDescription=roleService.getActiveRoleDescription();
    }
}