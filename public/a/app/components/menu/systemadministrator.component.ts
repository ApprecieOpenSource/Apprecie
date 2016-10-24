import {Component,OnInit,Renderer} from 'angular2/core';
import {Router,Route,RouteConfig, ROUTER_DIRECTIVES,Location} from 'angular2/router';
import {UserService} from "../../services/userservice";
import {Authentication} from "../../services/authentication";
@Component({
    selector: 'menu-items',
    templateUrl: '/a/app/components/menu/systemAdministrator.html',
    directives: [ROUTER_DIRECTIVES]
})
export class SystemAdministratorMenu{
    public firstName:String;
    public lastName:String;
    constructor(private _router:Router,private _userService:UserService, private _authentication:Authentication){
        this.firstName=_userService.getFirstName();
        this.lastName=_userService.getLastName();
    }

    logout(){
        if(this._authentication.logout()){
            this._router.parent.navigate(['LoginContainer']);
        }
    }
}