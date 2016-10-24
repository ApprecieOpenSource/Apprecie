import {Injectable,Component} from 'angular2/core';
import {Observable} from "rxjs/Observable";

@Injectable()
@Component({

})
export class RoleService {
    private _activeRoleDescription: string;
    private _activeRoleId: number;
    constructor(){

    }
    getActiveRoleDescription(){
        if(this._activeRoleDescription!=null){
            return this._activeRoleDescription;
        }
        console.log(JSON.parse(sessionStorage.getItem('userRecord')));
        this._activeRoleDescription=(JSON.parse(sessionStorage.getItem('userRecord')).activeRole.roleDescription);
        return this._activeRoleDescription;
    }

    getActiveRoleId(){
        if(this._activeRoleId!=null){
            return this._activeRoleId;
        }
        this._activeRoleId=(JSON.parse(sessionStorage.getItem('userRecord')).activeRole.roleId);
        return this._activeRoleId;
    }
}