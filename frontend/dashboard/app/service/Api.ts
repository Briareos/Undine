class ApiTransaction {
    private _action: string;
    private _stack: any[] = [];

    get action(): string {
        return this._action;
    }

    get stack(): any[] {
        return this._stack;
    }

    public push(action: string, params: any): void {
        if (this._action) {
            if (this._action !== action) {
                throw Error('Transaction of type "' + this._action + '" cannot accept actions of type "' + action + '".');
            }
        } else {
            this._action = action;
        }
        this._stack.push(params);
    }
}

class Api {
    private http: ng.IHttpService;
    private q: ng.IQService;
    private endpoint: string;
    private transaction: ApiTransaction;

    constructor($http: ng.IHttpService, $q: ng.IQService, endpoint: string) {
        this.http = $http;
        this.q = $q;
        this.endpoint = endpoint;
    }

    public beginTransaction(): void {
        this.transaction = new ApiTransaction();
    }

    public commit(): void {
        // @todo: Implement!
        this.transaction = null;
    }

    public rollback(): void {
        if (!this.transaction) {
            throw Error('There is no active transaction.');
        }
        this.transaction = null;
    }

    public siteConnect(url: string, checkUrl: boolean = false, httpUsername?: string, httpPassword?: string, adminUsername?: string, adminPassword?: string, ftpMethod?: string, ftpUsername?: string, ftpPassword?: string, ftpHost?: string, ftpPort?: number, stream: boolean = false): ng.IPromise<ng.IHttpPromiseCallbackArg<IApiResult>> {
        return this.command(
            'site.connect',
            {
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
            },
            stream
        );
    }

    private command(command: string, parameters: any, stream: boolean = false): ng.IPromise<ng.IHttpPromiseCallbackArg<IApiResult>> {
        if (this.transaction) {
            let deferred: ng.IDeferred<ng.IHttpPromiseCallbackArg<IApiResult>> = this.q.defer();

            // @todo: Save this deferred object!

            return deferred.promise;
        }
        return this.http.post(this.endpoint + command, parameters, this.buildConfig(stream));
    }

    private buildConfig(stream: boolean): ng.IRequestShortcutConfig {
        let config: ng.IRequestShortcutConfig = <ng.IRequestShortcutConfig>{};
        if (stream) {
            // The following code is a major AngularJS hack (second part is in the XhrFactory and DeferredStack).
            // It relies on the fact that the last transformation callback is the last "injectable" code during
            // an HTTP request, so we can hack in the "deferred" variable and attach a progress to it in the
            // XhrFactory. The DeferredStack is a global array because fuck AngularJS providers.
            config.transformRequest = Array.prototype.concat(this.http.defaults.transformRequest, (data: any) => {
                let deferred: ng.IDeferred<ng.IHttpPromiseCallbackArg<IApiResult>> = this.q.defer();
                DeferredStack.push(deferred);
                let originalDefer: any = this.q.defer;
                this.q.defer = () => {
                    this.q.defer = originalDefer;
                    return deferred;
                };
                return data;
            });
            config.transformResponse = Array.prototype.concat(
                (data: string): string => {
                    /* tslint:disable:quotemark */
                    let lastNewLine: number = data.lastIndexOf("\n");
                    /* tslint:enable */
                    if (lastNewLine === -1) {
                        return data;
                    }
                    return data.substring(lastNewLine + 1);
                },
                this.http.defaults.transformResponse
            );
            config.headers = {
                'X-Stream': '1'
            };
        }

        return config;
    }
}

angular.module('undine.dashboard')
    .factory('Api', function ($http: ng.IHttpService, $q: ng.IQService, AppData: AppData): Api {
        return new Api($http, $q, AppData.apiUrl);
    });
