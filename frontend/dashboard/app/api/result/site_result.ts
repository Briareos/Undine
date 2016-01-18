import {IResult} from "./result";
import {ISite} from "../model/site";
import {ISiteState} from "../model/site_state";

export interface ISiteConnect extends IResult {
    site: ISite;
}

export interface ISitePing extends IResult {
    siteState: ISiteState;
}
