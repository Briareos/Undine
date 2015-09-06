class SitePicker {
    private _visible:boolean = false;

    constructor(Dashboard:DashboardInterface) {

    }

    public get visible():boolean {
        return this._visible;
    }

    public set visible(visible:boolean) {
        this._visible = visible;
    }
}

angular.module('undine.dashboard')
    .service('SitePicker', function (Dashboard:DashboardInterface) {
        return new SitePicker(Dashboard);
    })
    .run(function (SitePicker:SitePicker, $rootScope:ng.IRootScopeService) {
        $rootScope.$on('$stateChangeSuccess', function ($event:ng.IAngularEvent, toState:ng.ui.IState) {
            var stateData = toState.data;

            if (_.isUndefined(stateData) || _.isUndefined(stateData.sitePicker)) {
                SitePicker.visible = false;
            }
            else {
                SitePicker.visible = !!stateData.sitePicker.visible;
            }
        });
    });