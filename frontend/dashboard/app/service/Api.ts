class Api {
    constructor(private $http:ng.IHttpService, private $q:ng.IQService, private endpoint:string) {
    }

    private buildConfig(stream):ng.IRequestShortcutConfig {
        var config = <ng.IRequestShortcutConfig>{};
        if (stream) {
            // The following code is a major AngularJS hack (second part is in the XhrFactory and DeferredStack).
            // It relies on the fact that the last transformation callback is the last "injectable" code during
            // an HTTP request, so we can hack in the "deferred" variable and attach a progress to it in the
            // XhrFactory. The DeferredStack is a global array because fuck AngularJS providers.
            config.transformRequest = Array.prototype.concat(this.$http.defaults.transformRequest, (data:any) => {
                var deferred = this.$q.defer();
                DeferredStack.push(deferred);
                var originalDefer = this.$q.defer;
                this.$q.defer = () => {
                    this.$q.defer = originalDefer;
                    return deferred;
                };
                // This is default AngularJS behavior.
                return data;
            });
            config.transformResponse = Array.prototype.concat((data:string)=> {
                var lastNewLine;
                if ((lastNewLine = data.lastIndexOf("\n")) === -1) {
                    return data;
                }
                return data.substring(lastNewLine + 1);
            }, this.$http.defaults.transformResponse);
            config.headers = {
                'X-Stream': 1
            };
        }

        return config;
    }

    private command(command:string, parameters:any, stream:boolean = true):ng.IHttpPromise<ApiResult> {
        return this.$http.post(this.endpoint + command, parameters, this.buildConfig(stream));
    }

    siteConnect(url:string, checkUrl:boolean = false, httpUsername?:string, httpPassword?:string, adminUsername?:string, adminPassword?:string, ftpMethod?:string, ftpUsername?:string, ftpPassword?:string, ftpHost?:string, ftpPort?:number):ng.IHttpPromise<SiteConnectResult> {
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
    .factory('Api', function ($http:ng.IHttpService, $q:ng.IQService, AppData:AppData) {
        return new Api($http, $q, AppData.apiUrl);
    });
