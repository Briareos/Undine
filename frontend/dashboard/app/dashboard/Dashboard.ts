interface DashboardInterface {
    sites:Array<Site>;
    user:User;
    load():ng.IPromise<DashboardInterface>;
}

class Dashboard implements DashboardInterface {
    private _user:User;
    private _sites:Array<Site>;
    private _deferred:ng.IDeferred<Dashboard>;
    private _initialPromise:ng.IPromise<Dashboard>;

    constructor(private Api:Api, private $q:ng.IQService) {
        this._deferred = $q.defer();
        this._initialPromise = this._deferred.promise;
    }

    /**
     * Should only be used once the page is loaded.
     *
     * @internal
     */
    initialize(user:User) {
        this._user = user;
        this._sites = user.sites;
    }


    load():ng.IPromise<DashboardInterface> {
        return this._initialPromise;
    }

    get sites() {
        return this._sites;
    }

    get user() {
        return this._user;
    }

    refreshDashboard() {
        // TODO: implement
        this._deferred.resolve(this);
    }
}

angular.module('undine.dashboard')
    .service('Dashboard', function (Api:Api, $q:ng.IQService) {
        return new Dashboard(Api, $q);
    })
    .run(function (Dashboard:Dashboard, AppData:AppData) {
        Dashboard.initialize(AppData.currentUser);
    });