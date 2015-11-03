import {bootstrap, provide, Component, FORM_PROVIDERS} from 'angular2/angular2';
import {RouteConfig, ROUTER_PROVIDERS, ROUTER_DIRECTIVES} from 'angular2/router';

import {DashboardController} from './page/dashboard/controller';
import {Sidebar} from './dashboard/Sidebar';
import {SitePickerDirective} from './component/sitePicker/sitePickerDirective';
import {Dashboard} from './dashboard/Dashboard';
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

@Component({
    selector: 'dashboard-app',
    directives: [ROUTER_DIRECTIVES, Sidebar, SitePickerDirective],
    template: `
<div class="stretched dashboard container">
    <sidebar></sidebar>
    <site-picker></site-picker>
    <div class="main content">
        <div class="content header">
            <div class="ui huge breadcrumb">
            </div>

            <div class="ui secondary icon action menu">
                <a class="item" [router-link]="['/Account']">
                    <i class="setting icon"></i>
                </a>
                <a class="item" [router-link]="['/Logout']">
                    <i class="sign out icon"></i>
                </a>
            </div>
        </div>
        <div class="state content">
            <router-outlet></router-outlet>
        </div>
    </div>
</div>
`
})
@RouteConfig([
    {path: '/', as: 'Dashboard', component: DashboardController},
    {path: '/account', as: 'Account', component: AccountController},
    {path: '/logout', as: 'Logout', component: LogoutController},
    {path: '/site/:uid', as: 'SiteDashboard', component: SiteDashboardController},
    {path: '/connect/...', as: 'ConnectSite', component: ConnectWebsiteController}
])
class DashboardComponent {
}

class AppData {
    constructor(private data:any){
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
    Dashboard,
    Api,
    provide('API_URL', {useValue: appData.apiUrl}),
    provide('CURRENT_USER', {useValue: appData.currentUser}),
    provide('LOGOUT_URL', {useValue: appData.logoutUrl}),
    provide('OXYGEN_ZIP_URL', {useValue: appData.oxygenZipUrl}),
    provide('BRAND', {useValue: appData.brand}),
    SitePicker,
    StripUrlPipe,
    ConnectWebsiteSession
]);
