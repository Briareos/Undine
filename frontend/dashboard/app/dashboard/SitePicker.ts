class SitePicker {
    private _visible: boolean = false;
    private _filteredSites: ISite[] = [];
    private _initialized: boolean = false;
    private dashboard: Dashboard;
    private rootScope: ng.IRootScopeService;

    constructor(Dashboard: Dashboard, $rootScope: ng.IRootScopeService) {
        this.dashboard = Dashboard;
        this.rootScope = $rootScope;
    }

    public subscribeScope(scope: ng.IScope, callback: any): void {
        let unSubscribe: any = this.rootScope.$on('site-picker.change', callback);
        scope.$on('$destroy', unSubscribe);

        if (this._initialized) {
            callback();
        }
    }

    public subscribe(callback: any): Function {
        if (this._initialized) {
            callback();
        }

        return this.rootScope.$on('site-picker.change', callback);
    }

    public get visible(): boolean {
        return this._visible;
    }

    public set visible(visible: boolean) {
        this._visible = visible;
        if (visible) {
            this.update();
        }
    }

    public get filteredSites(): ISite[] {
        return this._filteredSites;
    }

    public update(): void {
        this._initialized = true;
        this._filteredSites = this.dashboard.sites.filter(function (site: ISite): boolean {
            return true;
        });
    }

    private broadcastChange(): void {
        this.rootScope.$broadcast('site-picker.change');
    }
}

angular.module('undine.dashboard')
    .service('SitePicker', function (Dashboard: Dashboard, $rootScope: ng.IRootScopeService): SitePicker {
        return new SitePicker(Dashboard, $rootScope);
    })
    .run(function (SitePicker: SitePicker, Dashboard: Dashboard, $rootScope: ng.IRootScopeService): void {
        $rootScope.$on('$stateChangeSuccess', function ($event: ng.IAngularEvent, toState: ng.ui.IState): void {
            let stateData: any = toState.data;

            if (_.isUndefined(stateData) || _.isUndefined(stateData.sitePicker)) {
                SitePicker.visible = false;
            } else {
                SitePicker.visible = !!stateData.sitePicker.visible;
            }
        });

        Dashboard.subscribe(() => {
            SitePicker.update();
        });
    });
