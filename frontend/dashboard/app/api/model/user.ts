import {ISite} from "./site";

export interface IUser {
    uid: string;
    email: string;
    sites: ISite[];
}
