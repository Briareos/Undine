import {IResult} from "./result";
import {ISite} from "../model/site";

export interface ISiteConnect extends IResult {
    site: ISite;
}
