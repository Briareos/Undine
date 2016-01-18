import {Injectable} from 'angular2/core';
import {ISite} from '../api/model/site';
import {State} from "./state";

@Injectable()
export class SitePicker {
    private _filteredSites: ISite[] = [];

    constructor(state: State) {
        this._filteredSites = state.user.sites.slice();
    }

    public get filteredSites(): ISite[] {
        return this._filteredSites;
    }
}
