class Api {
    constructor(private $http:ng.IHttpService, private endpoint:string) {
    }

    private command(command:string, parameters:any):ng.IHttpPromise<ApiResult> {
        return this.$http.post(this.endpoint + command, parameters);
    }

    siteConnect(url:string, checkUrl:boolean=false, httpUsername?:string, httpPassword?:string, adminUsername?:string, adminPassword?:string, ftpMethod?:string, ftpUsername?:string, ftpPassword?:string, ftpHost?:string, ftpPort?:number):ng.IHttpPromise<SiteConnectResult> {
        return this.command('site.connect', {
            url: url,
            checkUrl: checkUrl,
            httpUsername: httpUsername,
            httpPassword: httpPassword,
            adminUsername: adminUsername,
            adminPassword: adminPassword,
            ftpMethod: ftpMethod,
            ftpUsername: ftpUsername,
            ftpPassword: ftpPassword,
            ftpHost: ftpHost,
            ftpPort: ftpPort
        });
    }
}

angular.module('undine.dashboard')
    .factory('Api', function ($http:ng.IHttpService, AppData:AppData) {
    return new Api($http, AppData.apiUrl);
});
