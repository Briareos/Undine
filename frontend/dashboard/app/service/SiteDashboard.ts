class SiteDashboard {
    constructor(private _site:Site, private _state:SiteState, private _modules:Array<Module>, private _themes:Array<Theme>, private _coreUpdates:Array<CoreUpdate>, private _moduleUpdates:Array<ModuleUpdate>, private _themeUpdates:Array<ThemeUpdate>) {
    }

    get site():Site {
        return this._site;
    }

    get state():SiteState {
        return this._state;
    }

    get modules():Array<Module> {
        return this._modules;
    }

    get themes():Array<Theme> {
        return this._themes;
    }

    get coreUpdateCount():number {
        return this._coreUpdates.length;
    }

    get moduleUpdateCount():number {
        return this._moduleUpdates.length;
    }

    get themeUpdateCount():number {
        return this._themeUpdates.length;
    }

    get coreUpdates():Array<CoreUpdate> {
        return this._coreUpdates;
    }

    get moduleUpdates():Array<ModuleUpdate> {
        return this._moduleUpdates;
    }

    get themeUpdates():Array<ThemeUpdate> {
        return this._themeUpdates;
    }
}

interface SiteDashboardFactory {
    create(site:Site):SiteDashboard
}

angular.module('undine.dashboard')
    .factory('SiteDashboardFactory', function () {
    return {
        create: function (site:Site):SiteDashboard {
            return new SiteDashboard(site, site.state, site.modules, site.themes, site.coreUpdates, site.moduleUpdates, site.themeUpdates);
        }
    };
});
