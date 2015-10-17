interface IConnectWebsiteUrlScope extends ng.IScope {
    urlForm: IConnectWebsiteUrlForm;
    urlFormData: IConnectWebsiteUrlFormData;
    urlFormLoading: boolean;
    httpAuthenticationRequired: boolean;
    connectWebsiteErrors: {
        httpAuthenticationFailed: boolean;
        canNotResolveHost: boolean;
    };
    urlFormSubmit(form: IConnectWebsiteUrlForm): void;
}

interface IConnectWebsiteUrlForm extends ng.IFormController {
    url: ng.INgModelController;
    httpUsername: ng.INgModelController;
    httpPassword: ng.INgModelController;
}

interface IConnectWebsiteUrlFormData {
    url: string;
    httpUsername: string;
    httpPassword: string;
}

interface IReconnectWebsiteScope extends ng.IScope {
    url: string;
    disconnectUrl: string;
    lookedForLoginForm: boolean;
    loginFormFound: boolean;
    connectWebsiteLoading: boolean;
    autoConnectWebsiteLoading: boolean;
    connectWebsiteActive: boolean;
    reconnectFormData: IAdminCredentialsFormData;
    connectWebsiteErrors: {
        stillConnected: boolean;
        invalidCredentials: boolean;
    };
    reconnectFormSubmit(form: IAdminCredentialsForm): void;
    reconnectClick(): void;
}

interface IAdminCredentialsForm extends ng.IFormController {
    username: ng.INgModelController;
    password: ng.INgModelController;
}

interface IAdminCredentialsFormData {
    username: string;
    password: string;
}

interface IFtpCredentialsFormData {
    method: string;
    username: string;
    password: string;
    host: string;
    port: string;
}

interface IConnectWebsiteNewScope extends ng.IScope {
    url: string;
    updatesUrl: string;
    oxygenZipUrl: string;
    lookedForLoginForm: boolean;
    loginFormFound: boolean;
    connectWebsiteLoading: boolean;
    autoConnectWebsiteLoading: boolean;
    connectWebsiteActive: boolean;
    ftpFormFound: boolean;
    newFormData: IAdminCredentialsFormData;
    ftpFormData: IFtpCredentialsFormData;
    connectWebsiteErrors: {
        stillDisabled: boolean;
        invalidCredentials: boolean;
        ftpError: boolean;
        ftpErrorMessage: string;
    };
    newClick(): void;
    newFormSubmit(form: IAdminCredentialsForm): void;
}

angular.module('undine.dashboard')
    .controller('ConnectWebsiteUrlController', function (Api: Api, ConnectWebsiteSession: ConnectWebsiteSession, $scope: IConnectWebsiteUrlScope, $state: ng.ui.IStateService): void {
        $scope.urlFormData = {
            url: '',
            httpUsername: '',
            httpPassword: ''
        };
        $scope.urlFormSubmit = (form: IConnectWebsiteUrlForm): void => {
            ConnectWebsiteSession.httpUsername = $scope.urlFormData.httpUsername;
            ConnectWebsiteSession.httpPassword = $scope.urlFormData.httpPassword;
            if (!form.$valid) {
                return;
            }
            $scope.connectWebsiteErrors = {
                httpAuthenticationFailed: false,
                canNotResolveHost: false
            };
            let siteUrl: string = $scope.urlFormData.url;
            if (!siteUrl.match(/^https?:\/\//)) {
                // Make sure the URL starts with a scheme.
                siteUrl = 'http://' + siteUrl.replace(/^:?\/+/, '');
            }
            $scope.urlFormLoading = true;
            Api.siteConnect(siteUrl, true, $scope.urlFormData.httpUsername, $scope.urlFormData.httpPassword)
                .then(
                    function (result: ISiteConnectResult): void {
                        ConnectWebsiteSession.clearAll();
                        $state.go('siteDashboard', {uid: result.site.uid});
                    },
                    function (response: ng.IHttpPromiseCallbackArg<Constraint>): void {
                        let constraint: Constraint = response.data;
                        if (constraint instanceof HttpAuthenticationRequiredConstraint) {
                            $scope.httpAuthenticationRequired = true;
                            return;
                        } else if (constraint instanceof HttpAuthenticationFailedConstraint) {
                            ConnectWebsiteSession.clearHttp();
                            $scope.connectWebsiteErrors.httpAuthenticationFailed = true;
                            return;
                        } else if (constraint instanceof AlreadyConnectedConstraint) {
                            $state.go('^.reconnect', {
                                url: siteUrl,
                                lookedForLoginForm: constraint.lookedForLoginForm,
                                loginFormFound: constraint.loginFormFound
                            });
                            return;
                        } else if (constraint instanceof OxygenNotEnabledConstraint) {
                            $state.go('^.new', {
                                url: siteUrl,
                                lookedForLoginForm: constraint.lookedForLoginForm,
                                loginFormFound: constraint.loginFormFound
                            });
                            return;
                        } else if (constraint instanceof CanNotResolveHost) {
                            $scope.connectWebsiteErrors.canNotResolveHost = true;
                        }
                    }
                )
                .finally(function (): void {
                    $scope.urlFormLoading = false;
                });
        };
    })
    .controller('ReconnectWebsiteController', function ($scope: IReconnectWebsiteScope, ConnectWebsiteSession: ConnectWebsiteSession, Api: Api, $state: ng.ui.IStateService, url: string, lookedForLoginForm: boolean, loginFormFound: boolean): void {
        $scope.url = url;
        $scope.disconnectUrl = url.replace(/\/?$/, '/?q=admin/config/oxygen/disconnect');
        $scope.lookedForLoginForm = lookedForLoginForm;
        $scope.loginFormFound = loginFormFound;
        $scope.connectWebsiteLoading = false;
        $scope.autoConnectWebsiteLoading = false;
        $scope.connectWebsiteActive = false;
        $scope.reconnectFormData = {
            username: '',
            password: ''
        };
        $scope.connectWebsiteErrors = {
            stillConnected: false,
            invalidCredentials: false
        };

        $scope.reconnectClick = (): void => {
            $scope.connectWebsiteErrors.stillConnected = false;
            $scope.connectWebsiteActive = true;
            $scope.connectWebsiteLoading = true;
            Api.siteConnect(url, false, ConnectWebsiteSession.httpUsername, ConnectWebsiteSession.httpPassword)
                .then(
                    function (response: ng.IHttpPromiseCallbackArg<ISiteConnectResult>): void {
                        let result: ISiteConnectResult = response.data;
                        // @todo: Reset form data if it's in state!
                        $state.go('siteDashboard', {uid: result.site.uid});
                    },
                    function (response: ng.IHttpPromiseCallbackArg<Constraint>): void {
                        let constraint: Constraint = response.data;
                        if (constraint instanceof AlreadyConnectedConstraint) {
                            $scope.connectWebsiteErrors.stillConnected = true;
                            return;
                        }
                    }
                )
                .finally(function (): void {
                    $scope.connectWebsiteActive = false;
                    $scope.connectWebsiteLoading = false;
                });
        };

        $scope.reconnectFormSubmit = function (form: IAdminCredentialsForm): void {
            $scope.connectWebsiteErrors.invalidCredentials = false;
            if (!form.$valid) {
                return;
            }
            $scope.connectWebsiteActive = true;
            $scope.autoConnectWebsiteLoading = true;
            Api.siteConnect(url, true, ConnectWebsiteSession.httpUsername, ConnectWebsiteSession.httpPassword, $scope.reconnectFormData.username, $scope.reconnectFormData.password)
                .then(
                    function (response: ng.IHttpPromiseCallbackArg<ISiteConnectResult>): void {
                        let result: ISiteConnectResult = response.data;
                        ConnectWebsiteSession.clearAll();
                        $state.go('siteDashboard', {uid: result.site.uid});
                    },
                    function (response: ng.IHttpPromiseCallbackArg<Constraint>): void {
                        let constraint: Constraint = response.data;
                        if (constraint instanceof InvalidCredentialsConstraint) {
                            $scope.connectWebsiteErrors.invalidCredentials = true;
                            return;
                        }
                    }
                )
                .finally(function (): void {
                    $scope.connectWebsiteActive = false;
                    $scope.autoConnectWebsiteLoading = false;
                    form.$setPristine();
                });
        };
    })
    .controller('ConnectWebsiteNewController', function ($scope: IConnectWebsiteNewScope, ConnectWebsiteSession: ConnectWebsiteSession, $state: ng.ui.IStateService, AppData: AppData, Api: Api, url: string, lookedForLoginForm: boolean, loginFormFound: boolean): void {
        $scope.url = url;
        $scope.updatesUrl = url.replace(/\/?$/, '/?q=admin/modules/install');
        $scope.oxygenZipUrl = AppData.oxygenZipUrl;
        $scope.lookedForLoginForm = lookedForLoginForm;
        $scope.loginFormFound = loginFormFound;
        $scope.connectWebsiteLoading = false;
        $scope.autoConnectWebsiteLoading = false;
        $scope.connectWebsiteActive = false;
        $scope.ftpFormFound = false;
        $scope.newFormData = {
            username: '',
            password: ''
        };
        $scope.ftpFormData = {
            method: 'ftp',
            username: '',
            password: '',
            host: '',
            port: ''
        };

        function resetErrors(): void {
            $scope.connectWebsiteErrors = {
                stillDisabled: false,
                invalidCredentials: false,
                ftpError: false,
                ftpErrorMessage: ''
            };
        }

        resetErrors();

        $scope.newClick = function (): void {
            resetErrors();
            $scope.connectWebsiteActive = true;
            $scope.connectWebsiteLoading = true;
            Api.siteConnect(url, false, ConnectWebsiteSession.httpUsername, ConnectWebsiteSession.httpPassword)
                .then(
                    function (response: ng.IHttpPromiseCallbackArg<ISiteConnectResult>): void {
                        let result: ISiteConnectResult = response.data;
                        ConnectWebsiteSession.clearAll();
                        $state.go('siteDashboard', {uid: result.site.uid});
                    },
                    function (response: ng.IHttpPromiseCallbackArg<Constraint>): void {
                        let constraint: Constraint = response.data;
                        if (constraint instanceof OxygenNotEnabledConstraint) {
                            $scope.connectWebsiteErrors.stillDisabled = true;
                            return;
                        } else if (constraint instanceof AlreadyConnectedConstraint) {
                            // ISite got connected to another account in the meantime? It's possible...
                            $state.go('^.reconnect', {
                                url: url,
                                lookedForLoginForm: lookedForLoginForm,
                                loginFormFound: loginFormFound
                            });
                            return;
                        }
                    }
                )
                .finally(function (): void {
                    $scope.connectWebsiteActive = false;
                    $scope.connectWebsiteLoading = false;
                });
        };
        $scope.newFormSubmit = function (form: IAdminCredentialsForm): void {
            resetErrors();
            $scope.connectWebsiteActive = true;
            $scope.autoConnectWebsiteLoading = true;
            Api.siteConnect(url, true, ConnectWebsiteSession.httpUsername, ConnectWebsiteSession.httpPassword, $scope.newFormData.username, $scope.newFormData.password, $scope.ftpFormData.method, $scope.ftpFormData.username, $scope.ftpFormData.password, $scope.ftpFormData.host, parseInt($scope.ftpFormData.port, 10))
                .then(
                    function (response: ng.IHttpPromiseCallbackArg<ISiteConnectResult>): void {
                        let result: ISiteConnectResult = response.data;
                        ConnectWebsiteSession.clearAll();
                        $state.go('siteDashboard', {uid: result.site.uid});
                    },
                    function (response: ng.IHttpPromiseCallbackArg<Constraint>): void {
                        let constraint: Constraint = response.data;
                        if (constraint instanceof InvalidCredentialsConstraint) {
                            $scope.connectWebsiteErrors.invalidCredentials = true;
                            return;
                        } else if (constraint instanceof FtpCredentialsRequiredConstraint) {
                            $scope.ftpFormFound = true;
                            return;
                        } else if (constraint instanceof FtpCredentialsErrorConstraint) {
                            $scope.connectWebsiteErrors.ftpError = true;
                            $scope.connectWebsiteErrors.ftpErrorMessage = constraint.ftpError;
                            return;
                        }
                    }
                )
                .finally(function (): void {
                    $scope.connectWebsiteActive = false;
                    $scope.autoConnectWebsiteLoading = false;
                    form.$setPristine();
                });
        };
    });
