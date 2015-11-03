import {Component} from 'angular2/angular2';
import {Router, RouterLink, ROUTER_PROVIDERS, RouterOutlet, RouteConfig} from 'angular2/router';
import {ConnectWebsiteUrlController} from "./url/controller";
import {ConnectWebsiteNewController} from "./new/controller";
import {ConnectWebsiteReconnectController} from "./reconnect/controller";

@Component({
    selector: 'connect-website-controller',
    directives: [RouterOutlet],
    template: `
        <div class="ui grid">
            <div class="doubling two column row">
                <div class="column">
                    <router-outlet></router-outlet>
                </div>
            </div>
        </div>
        `
})
@RouteConfig([
    {path: '/', redirectTo: '/url'},
    {path: '/url', as: 'ConnectSiteUrl', component: ConnectWebsiteUrlController},
    {path: '/new', as: 'ConnectSiteNew', component: ConnectWebsiteNewController},
    {path: '/reconnect', as: 'ConnectSiteReconnect', component: ConnectWebsiteReconnectController}
])
export class ConnectWebsiteController {
}
