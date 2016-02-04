import {Component, ViewContainerRef} from 'angular2/core';
import {RouterLink} from 'angular2/router';

import {SitePickerSiteList} from './sitePickerSiteListDirective';
import {Api} from "../../service/Api";
import {State} from "../../dashboard/state";
import {IError} from "../../api/error/abstract_error";
import * as Result from "../../api/result";
import {ISite} from "../../api/model/site";

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
        let element: HTMLElement = <HTMLElement>_viewContainer.element.nativeElement;
        element.style.display = 'block';
        this._state = state;
        this._api = api;
    }

    public refresh(): void {
        if (this._state.user.sites.length === 0) {
            return;
        }
        this._api.bulk((): void => {
            this._state.user.sites.forEach((s: ISite) => {
                this._api.sitePing(s.id)
                    .result.subscribe(
                    (result: Result.ISitePing) => {
                        this._state.updateSiteState(s, result.siteState);
                    },
                    (error: IError) => {
                        console.log(error);
                    }
                );
            });
        }).subscribe(null, null, (): void => {
            console.log('bulk call completed');
        });
    }
}
