import {IModule, ITheme} from "./site_extension";
import {ICoreUpdate, IModuleUpdate, IThemeUpdate} from "./site_update";

export interface ISiteState {
    drupalVersion: string;
    modules: IModule[];
    themes: ITheme[];
    coreUpdates: ICoreUpdate[];
    moduleUpdates: IModuleUpdate[];
    themeUpdates: IThemeUpdate[];
}
