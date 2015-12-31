import {ISite} from "./site";

export interface IUser {
    id: string;
    email: string;
    sites: ISite[];
}
