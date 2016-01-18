import {Component, ViewContainerRef} from 'angular2/core';
import {RouterLink} from 'angular2/router';

import {SitePickerSiteList} from './sitePickerSiteListDirective';
import {ISite} from '../../api/model/site';
import {Api} from "../../service/Api";
import {State} from "../../dashboard/state";

@Component({
    selector: 'site-picker',
    directives: [SitePickerSiteList, RouterLink],
    styles: [`

    `],
    template: `
<div class="site-picker">
    <div class="ui fluid large icon input">
        <input type="text" placeholder="Search sites">
        <i class="search icon"></i>
    </div>
    <a [routerLink]="['/ConnectSite']" class="ui fluid large button">
        <i class="plus icon"></i>
        Connect Website
    </a>
    <a (click)="refresh()" class="ui fluid large button">
        <i class="plus icon"></i>
        Refresh
    </a>
    <site-picker-site-list></site-picker-site-list>
</div>
`
})
export class SitePickerDirective {
    private _api: Api;
    private _state: State;

    constructor(private _viewContainer: ViewContainerRef, state: State, api: Api) {
        let element = <HTMLElement>_viewContainer.element.nativeElement;
        element.style.display = 'block';
        this._state = state;
        this._api = api;
    }

    public refresh() {
        if (this._state.user.sites.length === 0) {
            return;
        }
        this._api.bulk(()=> {
            this._state.user.sites.forEach((s) => {
                this._api.sitePing(s.id)
                    .result.subscribe(
                    (result) => {
                        this._state.updateSiteState(s, result.siteState);
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
