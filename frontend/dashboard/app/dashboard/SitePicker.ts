class SitePicker {
    private _visible:boolean = false;
    private _filteredSites:Array<Site> = [];
    private _initialized:boolean = false;

    constructor(private Dashboard:Dashboard, private $rootScope:ng.IRootScopeService) {
    }

    public subscribeScope(scope:ng.IScope, callback:any) {
        var unsubscribe:any = this.$rootScope.$on('site-picker.change', callback);
        scope.$on('destroy', unsubscribe);

        if (this._initialized) {
            callback();
        }
    }

    public subscribe(callback:any):Function {
        if (this._initialized) {
            callback();
        }

        return this.$rootScope.$on('site-picker.change', callback);
    }

    public get visible():boolean {
        return this._visible;
    }

    public set visible(visible:boolean) {
        this._visible = visible;
        if (visible) {
            this.update();
        }
    }

    public get filteredSites():Array<Site> {
        return this._filteredSites;
    }

    public update() {
        this._initialized = true;
        this._filteredSites = this.Dashboard.sites.filter(function (site:Site) {
            return true;
        });
    }

    private broadcastChange() {
        this.$rootScope.$broadcast('site-picker.change');
    }
}

angular.module('undine.dashboard')
    .service('SitePicker', function (Dashboard:Dashboard, $rootScope:ng.IRootScopeService) {
        return new SitePicker(Dashboard, $rootScope);
    })
    .run(function (SitePicker:SitePicker, Dashboard:Dashboard, $rootScope:ng.IRootScopeService) {
        $rootScope.$on('$stateChangeSuccess', function ($event:ng.IAngularEvent, toState:ng.ui.IState) {
            var stateData = toState.data;

            if (_.isUndefined(stateData) || _.isUndefined(stateData.sitePicker)) {
                SitePicker.visible = false;
            }
            else {
                SitePicker.visible = !!stateData.sitePicker.visible;
            }
        });

        Dashboard.subscribe(() => {
            SitePicker.update();
        });
    });
