import {ISiteState} from "./site_state";
import {IModule, ITheme} from "./site_extension";
import {ICoreUpdate, IModuleUpdate, IThemeUpdate} from "./site_update";

export interface ISite {
    uid: string;
    url: string;
    state: ISiteState;
    modules: IModule[];
    themes: ITheme[];
    coreUpdates: ICoreUpdate[];
    moduleUpdates: IModuleUpdate[];
    themeUpdates: IThemeUpdate[];
}
