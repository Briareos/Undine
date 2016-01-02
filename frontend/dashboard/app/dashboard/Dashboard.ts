import {Injectable, Inject} from 'angular2/core';
import {Api} from '../service/Api';
import {IUser} from "../api/model/user";
import {ISite} from "../api/model/site";

@Injectable()
export class Dashboard {
    private api: Api;
    private _user: IUser;
    private _sites: ISite[];

    constructor(api: Api, @Inject('CURRENT_USER') user: IUser) {
        this.api = api;
        this._user = user;
        this._sites = user.sites;
    }

    public get sites(): ISite[] {
        return this._sites;
    }

    public get user(): IUser {
        return this._user;
    }
}
