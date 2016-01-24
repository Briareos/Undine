import {EventEmitter} from "angular2/core";
import {IUser} from "../api/model/user";
import {ISite} from "../api/model/site";
import {ISiteState} from "../api/model/site_state";

export class State {
    /**
     * A new website has been added to the account.
     */
    public onAddSite: EventEmitter<ISite> = new EventEmitter();
    /**
     * A website has been removed from the account.
     */
    public onRemoveSite: EventEmitter<ISite> = new EventEmitter();
    /**
     * A new website state has been fetched.
     */
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
