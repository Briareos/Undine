interface ISite {
    uid: string;
    url: string;
    state: ISiteState;
    modules: IModule[];
    themes: ITheme[];
    coreUpdates: ICoreUpdate[];
    moduleUpdates: IModuleUpdate[];
    themeUpdates: IThemeUpdate[];
}
