export class Api {
    constructor(private $http:ng.IHttpService, private endpoint:string) {
    }

    private command(command:string, parameters:any):ng.IHttpPromise<ApiResult> {
        return this.$http.post(this.endpoint + command, parameters);
    }

    siteConnect(url:string):ng.IHttpPromise<SiteConnectResult> {
        return this.command('site.connect', {url: url});
    }
}

angular.module('undine.dashboard')
    .factory('Api', function ($http:ng.IHttpService, AppData:AppData) {
        return new Api($http, AppData.apiUrl);
    });
