import {Component,View,Renderer} from 'angular2/core';
import {Authentication} from '../../services/authentication';
import 'rxjs/Rx';
import {PortalInit} from "../../services/portalinit";
import {NgForm}    from 'angular2/common';
import {Login} from "../../models/login";
import {Router} from "angular2/router";
@Component({
    selector: 'router-outlet',
    providers: [Authentication],
    templateUrl: '/a/app/components/login/login.form.html',
})

export class LoginComponent{
    public logo:String;
    public error:Boolean;
    public errorMessage:String;

    constructor(private _auth:Authentication, private _router:Router,private _portalInit:PortalInit){
        this.logo=_portalInit.getLogo();
        this.error=false;
    }

    model = new Login(null,null);
    submitted = false;
    isProcessing=false;
    onSubmit() {
        this.isProcessing=true;
        this._auth.emailAddress=this.model.emailAddress;
        this._auth.password=this.model.password;
        this._auth.loginUser().subscribe(response  => {
            if(response.status=='success'){
                this.error=false;
                sessionStorage.setItem('userRecord',JSON.stringify(response));
                this._router.parent.parent.navigate(['ApplicationContainer']);
            }
            else{
                this.errorMessage=response.message;
                this.error=true;
            }
            this.isProcessing=false;
        });
        this.submitted = true;
    }

    public goToRecovery(){
        this._router.parent.navigate(['Recovery']);
    }
}