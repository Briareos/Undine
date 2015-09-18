interface Site {
    uid: string;
    url: string;
    state: SiteState;
    modules: Array<Module>;
    themes: Array<Theme>;
    coreUpdates:Array<CoreUpdate>;
    moduleUpdates:Array<ModuleUpdate>;
    themeUpdates:Array<ThemeUpdate>;
}
