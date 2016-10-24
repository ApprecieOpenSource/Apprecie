import {Injectable,Component} from 'angular2/core';
import {Observable} from "rxjs/Observable";

@Injectable()
@Component({

})
export class UserService {
    private _firstName: string;
    private _lastname: string;
    private _activeRole: string;
    constructor(){

    }
    getFirstName(){
        if(this._firstName!=null){
            return this._firstName;
        }
        this._firstName=(JSON.parse(sessionStorage.getItem('userRecord')).profile.firstname);
        return this._firstName;
    }

    getActiveRole(){
        if(this._activeRole!=null){
            return this._activeRole;
        }
        this._activeRole=(JSON.parse(sessionStorage.getItem('userRecord')).activeRole.roleName);
        return this._activeRole;
    }

    getLastName(){
        if(this._lastname!=null){
            return this._lastname;
        }
        this._lastname=(JSON.parse(sessionStorage.getItem('userRecord')).profile.lastname);
        return this._lastname;
    }
}