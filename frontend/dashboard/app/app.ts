import {bootstrap} from 'angular2/platform/browser';
import {provide, Component} from 'angular2/core';
import {FORM_PROVIDERS} from 'angular2/common';
import {RouteConfig, ROUTER_PROVIDERS, ROUTER_DIRECTIVES} from 'angular2/router';

import {DashboardController} from './page/dashboard/controller';
import {Navigation} from './dashboard/navigation';
import {SitePickerDirective} from './component/sitePicker/sitePickerDirective';
import {Api} from './service/Api';
import {SitePicker} from './dashboard/SitePicker';
import {StripUrlPipe} from './filters/strip_url';
import {AccountController} from './page/account/controller';
import {SiteDashboardController} from './page/site/SiteDashboardController';
import {LogoutController} from './page/logout/controller';
import {ConnectWebsiteUrlController} from './page/connect_website/url/controller';
import {ConnectWebsiteSession} from './service/ConnectWebsiteSession';
import {ConnectWebsiteNewController} from "./page/connect_website/new/controller";
import {ConnectWebsiteReconnectController} from "./page/connect_website/reconnect/controller";
import {IUser} from "./api/model/user";
import {ConnectWebsiteController} from "./page/connect_website/controller";
import {State} from "./dashboard/state";

@Component({
    selector: 'dashboard-app',
    directives: [ROUTER_DIRECTIVES, Navigation, SitePickerDirective],
    template: `
<div class="page-wrapper">
    <navigation class="navigation"></navigation>
    <div class="content-wrapper">
        <site-picker class="site-picker"></site-picker>
        <div class="content">
            <div>
                <div class="ui huge breadcrumb">
                breadcrumb
                </div>

                <div class="ui secondary icon action menu">
                    <a class="item" [routerLink]="['/Account']">
                        <i class="setting icon"></i>
                    </a>
                    <a class="item" [routerLink]="['/Logout']">
                        <i class="sign out icon"></i>
                    </a>
                </div>
            </div>
            <div>
                <router-outlet></router-outlet>
            </div>
        </div>
    </div>
</div>
`
})
@RouteConfig([
    {path: '/', name: 'Dashboard', component: DashboardController, useAsDefault: true},
    {path: '/account', name: 'Account', component: AccountController},
    {path: '/logout', name: 'Logout', component: LogoutController},
    {path: '/site/:id', name: 'SiteDashboard', component: SiteDashboardController},
    {path: '/connect/...', name: 'ConnectSite', component: ConnectWebsiteController}
])
class DashboardComponent {
}

class AppData {
    constructor(private data: any) {
    }

    get apiUrl(): string {
        return this.data['apiUrl'];
    }

    get currentUser(): IUser {
        return this.data['currentUser'];
    }

    get logoutUrl(): string {
        return this.data['logoutUrl'];
    }

    get oxygenZipUrl(): string {
        return this.data['oxygenZipUrl'];
    }

    get brand(): string {
        return this.data['brand'];
    }
}

let appData = new AppData(window['appData']);

bootstrap(DashboardComponent, [
    FORM_PROVIDERS,
    ROUTER_PROVIDERS,
    Api,
    provide(State, {useValue: new State(appData.currentUser)}),
    provide('API_URL', {useValue: appData.apiUrl}),
    provide('LOGOUT_URL', {useValue: appData.logoutUrl}),
    provide('OXYGEN_ZIP_URL', {useValue: appData.oxygenZipUrl}),
    provide('BRAND', {useValue: appData.brand}),
    SitePicker,
    StripUrlPipe,
    ConnectWebsiteSession
]);
