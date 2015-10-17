class ISiteDashboard {
    private _site: ISite;
    private _state: ISiteState;
    private _modules: IModule[];
    private _themes: ITheme[];
    private _coreUpdates: ICoreUpdate[];
    private _moduleUpdates: IModuleUpdate[];
    private _themeUpdates: IThemeUpdate[];

    constructor(site: ISite, state: ISiteState, modules: IModule[], themes: ITheme[], coreUpdates: ICoreUpdate[], moduleUpdates: IModuleUpdate[], themeUpdates: IThemeUpdate[]) {
        this._site = site;
        this._state = state;
        this._modules = modules;
        this._themes = themes;
        this._coreUpdates = coreUpdates;
        this._moduleUpdates = moduleUpdates;
        this._themeUpdates = themeUpdates;
    }

    get site(): ISite {
        return this._site;
    }

    get state(): ISiteState {
        return this._state;
    }

    get modules(): Array<IModule> {
        return this._modules;
    }

    get themes(): ITheme[] {
        return this._themes;
    }

    get coreUpdateCount(): number {
        return this._coreUpdates.length;
    }

    get moduleUpdateCount(): number {
        return this._moduleUpdates.length;
    }

    get themeUpdateCount(): number {
        return this._themeUpdates.length;
    }

    get coreUpdates(): ICoreUpdate[] {
        return this._coreUpdates;
    }

    get moduleUpdates(): IModuleUpdate[] {
        return this._moduleUpdates;
    }

    get themeUpdates(): IThemeUpdate[] {
        return this._themeUpdates;
    }
}

interface ISiteDashboardFactory {
    create(site: ISite): ISiteDashboard;
}

angular.module('undine.dashboard')
    .factory('SiteDashboardFactory', function (): ISiteDashboardFactory {
        return {
            create: function (site: ISite): ISiteDashboard {
                return new ISiteDashboard(site, site.state, site.modules, site.themes, site.coreUpdates, site.moduleUpdates, site.themeUpdates);
            }
        };
    });
