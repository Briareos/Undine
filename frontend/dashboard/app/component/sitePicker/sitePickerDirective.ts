import {Component, ViewContainerRef} from 'angular2/core';
import {RouterLink} from 'angular2/router';

import {SitePickerSiteList} from './sitePickerSiteListDirective';
import {SitePicker} from '../../dashboard/SitePicker';
import {Dashboard} from '../../dashboard/Dashboard';
import {ISite} from '../../api/model/site';
import {Api} from "../../service/Api";

@Component({
    selector: 'site-picker',
    directives: [SitePickerSiteList, RouterLink],
    template: `
<div class="site-picker">
    <div class="ui fluid dark large icon input margin-bottom-20">
        <input type="text" placeholder="Search sites">
        <i class="search icon"></i>
    </div>
    <a [routerLink]="['/ConnectSite']" class="fluid large ui light gray button margin-bottom-20">
        <i class="plus icon"></i>
        Connect Website
    </a>
    <a (click)="refresh()" class="fluid large ui light gray button margin-bottom-20">
        <i class="plus icon"></i>
        Refresh
    </a>
    <site-picker-site-list></site-picker-site-list>
</div>
`
})
export class SitePickerDirective {
    private _sites: ISite[] = [];
    private _api: Api;

    constructor(private _viewContainer: ViewContainerRef, dashboard: Dashboard, api: Api) {
        let element = <HTMLElement>_viewContainer.element.nativeElement;
        element.style.display = 'block';
        this._sites = dashboard.sites;
        this._api = api;
    }

    refresh() {
        if (this._sites.length === 0) {
            return;
        }
        this._api.bulk(()=> {
            this._sites.forEach((s) => {
                this._api.sitePing(s.id)
                    .result.subscribe(
                    (result) => {
                        console.log(result);
                    },
                    (constraint) => {
                        console.log(constraint);
                    }
                );
            });
        }).subscribe(null, null, ()=> {
            console.log('bulk call completed')
        });
    }
}
