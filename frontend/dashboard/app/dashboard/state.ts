import {EventEmitter} from "angular2/core";
import {IUser} from "../api/model/user";
import {ISite} from "../api/model/site";
import {ISiteState} from "../api/model/site_state";

export class State {
    public onAddSite: EventEmitter<ISite> = new EventEmitter();
    public onRemoveSite: EventEmitter<ISite> = new EventEmitter();
    public onUpdateSiteState: EventEmitter<ISite> = new EventEmitter();

    public constructor(public user: IUser) {
        this.user = user;
    }

    public addSite(site: ISite) {
        this.user.sites.push(site);
        this.onAddSite.emit(site);
    }

    public removeSite(site: ISite) {
        this.user.sites.splice(this.user.sites.indexOf(site), 1);
        this.onRemoveSite.emit(site);
    }

    public updateSiteState(site: ISite, siteState: ISiteState) {
        Object.assign(site.state, siteState);
        this.onUpdateSiteState.emit(site);
    }
}
