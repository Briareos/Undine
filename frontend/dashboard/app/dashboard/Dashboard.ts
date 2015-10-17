class Dashboard {
    private api: Api;
    private q: ng.IQService;
    private rootScope: ng.IRootScopeService;
    private _user: IUser;
    private _sites: ISite[];
    private _initialized: boolean = false;

    constructor(api: Api, $q: ng.IQService, $rootScope: ng.IRootScopeService) {
        this.api = api;
        this.q = $q;
        this.rootScope = $rootScope;
    }

    /**
     * Should only be used once the page is loaded.
     *
     * @internal
     */
    public initialize(user: IUser) {
        this._user = user;
        this._sites = user.sites;
        this._initialized = true;
        this.rootScope.$broadcast('dashboard.change');
    }

    public subscribeScope(scope: ng.IScope, callback: any) {
        let unsubscribe: any = this.rootScope.$on('dashboard.change', callback);
        scope.$on('destroy', unsubscribe);

        if (this._initialized) {
            callback();
        }
    }

    public subscribe(callback: any): void {
        if (this._initialized) {
            callback();
        }

        this.rootScope.$on('dashboard.change', callback);
    }

    public get sites(): ISite[] {
        return this._sites;
    }

    public get user(): IUser {
        return this._user;
    }

    private broadcastChange(): void {
        this.rootScope.$broadcast('dashboard.change');
    }
}

angular.module('undine.dashboard')
    .service('Dashboard', function (Api: Api, $q: ng.IQService, $rootScope: ng.IRootScopeService): Dashboard {
        return new Dashboard(Api, $q, $rootScope);
    })
    .run(function (Dashboard: Dashboard, AppData: AppData): void {
        Dashboard.initialize(AppData.currentUser);
    });
