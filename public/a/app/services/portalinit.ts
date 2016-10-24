import {Injectable,Component} from 'angular2/core';
import {Http} from 'angular2/http';
import {Observable} from "rxjs/Observable";

@Injectable()
@Component({
    providers: [Http],
})
export class PortalInit {
    private _logo: string;
    private _styles: string;
    private _assetsDir: string;
    private _csrf: string;
    private _loginBg: string;
    constructor(private _http:Http){

    }

    getPortal(){
        sessionStorage.removeItem('portalSettings');
        return this._http.get('/apiex/portalinit').map(res => res.json());
    }

    isInitialised(){
        if(sessionStorage.getItem('portalInit')==null){
            return false
        }
        return true;

    }
    getLogo(){
        if(this._logo!=null){
            return this._logo;
        }
        this._logo=(JSON.parse(sessionStorage.getItem('portalInit')).logo);
        return this._logo;
    }
    getStyles(){
        if(this._styles!=null){
            return this._styles;
        }
        this._styles=(JSON.parse(sessionStorage.getItem('portalInit')).styles);
        return this._styles;
    }
    getAssetsDir(){
        if(this._assetsDir!=null){
            return this._assetsDir;
        }
        this._assetsDir=(JSON.parse(sessionStorage.getItem('portalInit')).assetsDir);
        return this._assetsDir;
    }
    getCsrf(){
        if(this._csrf!=null){
            return this._csrf;
        }
        this._csrf=(JSON.parse(sessionStorage.getItem('portalInit')).csrf);
        return this._csrf;
    }
    getLoginBg(){
        if(this._loginBg!=null){
            return this._loginBg;
        }
        this._loginBg=(JSON.parse(sessionStorage.getItem('portalInit')).loginBg);
        return this._loginBg;
    }

}