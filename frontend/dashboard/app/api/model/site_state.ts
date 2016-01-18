import {IModule, ITheme} from "./site_extension";
import {ICoreUpdate, IModuleUpdate, IThemeUpdate} from "./site_update";

export interface ISiteState {
    drupalVersion: string;
    connected: boolean;
    modules: IModule[];
    themes: ITheme[];
    coreUpdates: ICoreUpdate[];
    moduleUpdates: IModuleUpdate[];
    themeUpdates: IThemeUpdate[];
}
