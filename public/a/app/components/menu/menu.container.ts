import {Component,Renderer,DynamicComponentLoader,Injector,OnInit,ElementRef} from 'angular2/core';
import {Router,Route,RouteConfig, ROUTER_DIRECTIVES,Location} from 'angular2/router';
import {PortalInit} from "../../services/portalinit";
import {SystemAdministratorMenu} from "./systemadministrator.component";
import {UserService} from "../../services/userservice";
import {Authentication} from "../../services/authentication";
@Component({
    selector: 'app-menu',
    templateUrl: '/a/app/layouts/menu.html',
    providers: [PortalInit]
})
export class MenuContainer implements OnInit{
    constructor( dcl: DynamicComponentLoader, elementRef: ElementRef, private _userService:UserService){
        switch(_userService.getActiveRole()){
            case 'SystemAdministrator':
                dcl.loadIntoLocation(SystemAdministratorMenu, elementRef,'menuitems');
                break;
        }
    }

    ngOnInit(){

    }
}