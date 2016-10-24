import {Injectable} from 'angular2/core';
import {Http,Headers} from 'angular2/http';
import {Component} from 'angular2/core';
import {Observable} from "rxjs/Observable";
import {Router,Location} from 'angular2/router';
import {PortalInit} from "./portalinit";

@Injectable()
@Component({
    providers: [Http]
})
export class Authentication {
    public emailAddress:String;
    public password:String;
    public remember:String;
    constructor(private _http:Http, private _router:Router, private _portalInit:PortalInit){

    }

    initialise(){
        this.getToken().subscribe(response  => {
            if(response.loggedIn!=false || response.loggedIn==true){
                sessionStorage.setItem('userRecord',JSON.stringify(response));
            }
            this.hasSessionOrRedirect();
        });
    }

    getToken(){
        sessionStorage.removeItem('userRecord');
        return this._http.get('/login/getAuthenticatedUser').map(res => res.json());
    }

    loginUser(){
        sessionStorage.removeItem('userRecord');
        return this._http.post('/apiex/login',
            JSON.stringify({'emailAddress':this.emailAddress,'password':this.password,'CSRF_SESSION_TOKEN':this._portalInit.getCsrf(),'remember':this.remember}))
            .map((res => res.json()));
    }

    hasSessionOrRedirect(){
        if(sessionStorage.getItem('userRecord')==null) {
            this._router.navigate(['LoginContainer']);
        }
        else{
            this._router.navigate(['ApplicationContainer']);
        }
    }

    logout(){
        sessionStorage.removeItem('userRecord');
        return true;
    }
}

