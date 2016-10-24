import {bootstrap}    from 'angular2/platform/browser';
import {App} from './components/app';
import {provide} from 'angular2/core';
import {Http,HTTP_PROVIDERS} from 'angular2/http';
import {RouteConfig, ROUTER_DIRECTIVES,ROUTER_PROVIDERS,HashLocationStrategy,LocationStrategy} from 'angular2/router';

bootstrap(App, [ROUTER_PROVIDERS,Http,HTTP_PROVIDERS,RouteConfig,ROUTER_DIRECTIVES,provide(LocationStrategy, {useClass: HashLocationStrategy})]);
