class Dashboard {
    private _user:User;
    private _sites:Array<Site>;

    constructor(private Api:Api, private $q:ng.IQService, private $rootScope:ng.IRootScopeService) {
    }

    /**
     * Should only be used once the page is loaded.
     *
     * @internal
     */
    public initialize(user:User) {
        this._user = user;
        this._sites = user.sites;
        this.$rootScope.$broadcast('dashboard.change');
    }

    public subscribeScope(scope:ng.IScope, callback:any) {
        var unsubscribe:any = this.$rootScope.$on('dashboard.change', callback);
        scope.$on('destroy', unsubscribe);
    }

    public subscribe(callback:any):Function {
        return this.$rootScope.$on('dashboard.change', callback);
    }

    public get sites() {
        return this._sites;
    }

    public get user() {
        return this._user;
    }

    private broadcastChange() {
        this.$rootScope.$broadcast('dashboard.change');
    }

    //refreshDashboard() {
    // TODO: implement
    //this._deferred.resolve(this);
    //}
}

angular.module('undine.dashboard')
    .service('Dashboard', function (Api:Api, $q:ng.IQService, $rootScope:ng.IRootScopeService) {
        return new Dashboard(Api, $q, $rootScope);
    })
    .run(function (Dashboard:Dashboard, AppData:AppData) {
        Dashboard.initialize(AppData.currentUser);
    });
