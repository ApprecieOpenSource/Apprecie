import {Component,OnInit,View} from 'angular2/core';
import {Authentication} from '../../services/authentication';
import 'rxjs/Rx';

@Component({
    selector: 'my-app',
    providers: [Authentication],
    templateUrl: 'app/components/dashboard/index.html'
})
export class DashboardComponent{
    constructor(private _authentication:Authentication){
        //_authentication.hasSessionOrRedirect();
    }
}