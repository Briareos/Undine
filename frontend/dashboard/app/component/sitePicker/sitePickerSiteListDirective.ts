import {Component, Injectable} from 'angular2/core';
import {CORE_DIRECTIVES} from 'angular2/common';
import {RouterLink} from 'angular2/router';
import {ISite} from '../../api/model/site';
import {StripUrlPipe} from "../../filters/strip_url";
import {State} from "../../dashboard/state";

@Component({
    selector: 'site-picker-site-list',
    directives: [CORE_DIRECTIVES, RouterLink],
    pipes: [StripUrlPipe],
    template: `
<section class="site-picker-site-list">
    <div class="ui site list">
        <div class="item site-item" *ngFor="#site of sites">
            <a class="site-name" [routerLink]="['/SiteDashboard', {id: site.id}]">
                <span class="status">
                    <span class="site status connected"></span>
                </span>

                <span class="name">
                    <i [class]="site.state.connected ? 'check icon' : 'warning icon'"></i>
                    {{ site.url | stripUrl }}
                </span>
            </a>
        </div>
    </div>
</section>
`
})
@Injectable()
export class SitePickerSiteList {
    private sites: ISite[];

    constructor(private state: State) {
        this.refreshList();
        this.state.onAddSite.subscribe(() => this.refreshList());
        this.state.onRemoveSite.subscribe(() => this.refreshList());
        this.state.onUpdateSiteState.subscribe((site: ISite): void => {
            Object.assign(this.sites.find((localSite: ISite) => localSite.id === site.id).state, site.state);
        });
    }

    private refreshList() {
        this.sites = this.state.user.sites.slice();
    }
}
