import {ISiteState} from "./site_state";

export interface ISite {
    id: string;
    url: string;
    state: ISiteState;
}
