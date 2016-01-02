import {Injectable} from 'angular2/core';
import {Dashboard} from './Dashboard';
import {ISite} from '../api/model/site';

@Injectable()
export class SitePicker {
    private _filteredSites: ISite[] = [];
    private dashboard: Dashboard;

    constructor(dashboard: Dashboard) {
        this.dashboard = dashboard;
        this._filteredSites = dashboard.sites;
    }

    public get filteredSites(): ISite[] {
        return this._filteredSites;
    }
}
