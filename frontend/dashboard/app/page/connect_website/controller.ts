import {Component} from 'angular2/core';
import {Router, RouterLink, ROUTER_PROVIDERS, RouterOutlet, RouteConfig} from 'angular2/router';
import {ConnectWebsiteUrlController} from "./url/controller";
import {ConnectWebsiteNewController} from "./new/controller";
import {ConnectWebsiteReconnectController} from "./reconnect/controller";

@Component({
    selector: 'connect-website-controller',
    directives: [RouterOutlet],
    template: `
        <div class="ui fluid container">
            <div class="ui padded grid">
                <div class="sixteen wide tablet thirteen wide computer ten wide large screen column">
                        <router-outlet></router-outlet>
                </div>
            </div>
        </div>
        `
})
@RouteConfig([
    {path: '/url', name: 'ConnectSiteUrl', component: ConnectWebsiteUrlController, useAsDefault: true},
    {path: '/new', name: 'ConnectSiteNew', component: ConnectWebsiteNewController},
    {path: '/reconnect', name: 'ConnectSiteReconnect', component: ConnectWebsiteReconnectController}
])
export class ConnectWebsiteController {
}
