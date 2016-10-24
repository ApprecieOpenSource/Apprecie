import {Component,OnInit,View,Renderer} from 'angular2/core';
import {Authentication} from '../../services/authentication';
import 'rxjs/Rx';
import {PortalInit} from "../../services/portalinit";
import {NgForm}    from 'angular2/common';
import {Router} from "angular2/router";
import {Recovery} from "../../models/recovery";
@Component({
    selector: 'router-outlet',
    providers: [Authentication],
    templateUrl: '/a/app/components/login/recovery.form.html',
})

export class RecoveryComponent{
    public logo:String;
    constructor(private _auth:Authentication, private _router:Router, private _portalInit:PortalInit){
        this.logo=_portalInit.getLogo();
    }
    model = new Recovery(null);
    public goToLogin(){
        this._router.parent.navigate(['Login']);
    }
}