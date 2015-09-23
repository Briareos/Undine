interface ConnectWebsiteUrlScope extends ng.IScope {
    urlForm:ConnectWebsiteUrlForm
    urlFormSubmit(form:ConnectWebsiteUrlForm)
    urlFormData:ConnectWebsiteUrlFormData
    urlFormLoading:boolean
    httpAuthenticationRequired:boolean
    connectWebsiteErrors:{
        httpAuthenticationFailed:boolean
    }
}

interface ConnectWebsiteUrlForm extends ng.IFormController {
    url:ng.INgModelController
    httpUsername:ng.INgModelController
    httpPassword:ng.INgModelController
}

interface ConnectWebsiteUrlFormData {
    url:string
    httpUsername:string
    httpPassword:string
}

interface ReconnectWebsiteScope extends ng.IScope {
    url:string
    disconnectUrl:string
    lookedForLoginForm:boolean
    loginFormFound:boolean
    connectWebsiteLoading:boolean
    autoConnectWebsiteLoading:boolean
    connectWebsiteActive:boolean
    reconnectFormData:AdminCredentialsFormData
    connectWebsiteErrors:{
        stillConnected:boolean
        invalidCredentials:boolean
    }
    reconnectFormSubmit(form:AdminCredentialsForm)
    reconnectClick()
}

interface AdminCredentialsForm extends ng.IFormController {
    username:ng.INgModelController
    password:ng.INgModelController
}

interface AdminCredentialsFormData {
    username:string
    password:string
}

interface FtpCredentialsFormData {
    method:string
    username:string
    password:string
    host:string
    port:number
}

interface ConnectWebsiteNewScope extends ng.IScope {
    url:string
    updatesUrl:string
    oxygenZipUrl:string
    lookedForLoginForm:string
    loginFormFound:string
    connectWebsiteLoading:boolean
    autoConnectWebsiteLoading:boolean
    connectWebsiteActive:boolean
    ftpFormFound:boolean
    newFormData:AdminCredentialsFormData
    ftpFormData:FtpCredentialsFormData
    connectWebsiteErrors:{
        stillDisabled:boolean
        invalidCredentials:boolean
        ftpError:boolean
        ftpErrorMessage:string
    }
    newClick()
    newFormSubmit()
}

angular.module('undine.dashboard')
    .controller('ConnectWebsiteUrlController', function (Api:Api, ConnectWebsiteSession:ConnectWebsiteSession, $scope:ConnectWebsiteUrlScope, $state:ng.ui.IStateService) {
        $scope.urlFormData = {};
        $scope.urlFormSubmit = function (form:ConnectWebsiteUrlForm) {
            ConnectWebsiteSession.httpUsername = $scope.urlFormData.httpUsername;
            ConnectWebsiteSession.httpPassword = $scope.urlFormData.httpPassword;
            if (!form.$valid) {
                return;
            }
            $scope.connectWebsiteErrors = {};
            var siteUrl:string = $scope.urlFormData.url;
            if (!siteUrl.match(/^https?:\/\//)) {
                // Make sure the URL starts with a scheme.
                siteUrl = 'http://' + siteUrl.replace(/^:?\/+/, '');
            }
            $scope.urlFormLoading = true;
            Api.siteConnect(siteUrl, true, $scope.urlFormData.httpUsername, $scope.urlFormData.httpPassword)
                .success(function (result:SiteConnectResult) {
                    // @todo: Reset form data if it's in state!
                    $state.go('siteDashboard', {uid: result.site.uid});
                })
                .error(function (constraint:Constraint) {
                    if (constraint instanceof HttpAuthenticationRequiredConstraint) {
                        $scope.httpAuthenticationRequired = true;
                    } else if (constraint instanceof HttpAuthenticationFailedConstraint) {
                        ConnectWebsiteSession.httpUsername = null;
                        ConnectWebsiteSession.httpPassword = null;
                        $scope.connectWebsiteErrors.httpAuthenticationFailed = true;
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
                    }
                })
                .finally(function () {
                    $scope.urlFormLoading = false;
                })
        };
    })
    .controller('ReconnectWebsiteController', function ($scope:ReconnectWebsiteScope, ConnectWebsiteSession:ConnectWebsiteSession, Api:Api, $state:ng.ui.IStateService, url:string, lookedForLoginForm:boolean, loginFormFound:boolean) {
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
        $scope.connectWebsiteErrors = {};

        $scope.reconnectClick = function () {
            $scope.connectWebsiteErrors.stillConnected = false;
            $scope.connectWebsiteActive = true;
            $scope.connectWebsiteLoading = true;
            Api.siteConnect(url, false, ConnectWebsiteSession.httpUsername, ConnectWebsiteSession.httpPassword)
                .success(function (result:SiteConnectResult) {
                    // @todo: Reset form data if it's in state!
                    $state.go('siteDashboard', {uid: result.site.uid});
                })
                .error(function (constraint:Constraint) {
                    if (constraint instanceof AlreadyConnectedConstraint) {
                        $scope.connectWebsiteErrors.stillConnected = true;
                        return;
                    }
                })
                .finally(function () {
                    $scope.connectWebsiteActive = false;
                    $scope.connectWebsiteLoading = false;
                });
        };

        $scope.reconnectFormSubmit = function (form:AdminCredentialsForm) {
            $scope.connectWebsiteErrors.invalidCredentials = false;
            if (!form.$valid) {
                return;
            }
            $scope.connectWebsiteActive = true;
            $scope.autoConnectWebsiteLoading = true;
            Api.siteConnect(url, true, ConnectWebsiteSession.httpUsername, ConnectWebsiteSession.httpPassword, $scope.reconnectFormData.username, $scope.reconnectFormData.password)
                .success(function (result:SiteConnectResult) {
                    // @todo: Reset form data if it's in state!
                    $state.go('siteDashboard', {uid: result.site.uid});
                })
                .error(function (constraint:Constraint) {
                    if (constraint instanceof InvalidCredentialsConstraint) {
                        $scope.connectWebsiteErrors.invalidCredentials = true;
                        return;
                    }
                })
                .finally(function () {
                    $scope.connectWebsiteActive = false;
                    $scope.autoConnectWebsiteLoading = false;
                    form.$setPristine();
                });
        }
    })
    .controller('ConnectWebsiteNewController', function ($scope:ConnectWebsiteNewScope, ConnectWebsiteSession:ConnectWebsiteSession, $state:ng.ui.IStateService, AppData:AppData, Api:Api, url:string, lookedForLoginForm:boolean, loginFormFound:boolean) {
        $scope.url = url;
        $scope.updatesUrl = url.replace(/\/?$/, '/?q=admin/modules/install');
        $scope.oxygenZipUrl = AppData.oxygenZipUrl;
        $scope.lookedForLoginForm = lookedForLoginForm;
        $scope.loginFormFound = loginFormFound;
        $scope.connectWebsiteLoading = false;
        $scope.autoConnectWebsiteLoading = false;
        $scope.connectWebsiteActive = false;
        $scope.ftpFormFound = false;
        $scope.connectWebsiteErrors = {};
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

        $scope.newClick = function () {
            $scope.connectWebsiteErrors = {};
            $scope.connectWebsiteActive = true;
            $scope.connectWebsiteLoading = true;
            Api.siteConnect(url, false, ConnectWebsiteSession.httpUsername, ConnectWebsiteSession.httpPassword)
                .success(function (result:SiteConnectResult) {
                    // @todo: Reset form data if it's in state!
                    $state.go('siteDashboard', {uid: result.site.uid});
                })
                .error(function (constraint:Constraint) {
                    if (constraint instanceof OxygenNotEnabledConstraint) {
                        $scope.connectWebsiteErrors.stillDisabled = true;
                        return;
                    } else if (constraint instanceof AlreadyConnectedConstraint) {
                        // Site got connected to another account in the meantime? It's possible...
                        $state.go('^.reconnect', {
                            url: url,
                            lookedForLoginForm: lookedForLoginForm,
                            loginFormFound: loginFormFound
                        });
                        return;
                    }
                })
                .finally(function () {
                    $scope.connectWebsiteActive = false;
                    $scope.connectWebsiteLoading = false;
                });
        };
        $scope.newFormSubmit = function (form:AdminCredentialsForm) {
            $scope.connectWebsiteErrors = {};
            $scope.connectWebsiteActive = true;
            $scope.autoConnectWebsiteLoading = true;
            Api.siteConnect(url, true, ConnectWebsiteSession.httpUsername, ConnectWebsiteSession.httpPassword, $scope.newFormData.username, $scope.newFormData.password, $scope.ftpFormData.method, $scope.ftpFormData.username, $scope.ftpFormData.password, $scope.ftpFormData.host, $scope.ftpFormData.port)
                .success(function (result:SiteConnectResult) {
                    // @todo: Reset form data if it's in state!
                    $state.go('siteDashboard', {uid: result.site.uid});
                })
                .error(function (constraint:Constraint) {
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
                })
                .finally(function () {
                    $scope.connectWebsiteActive = false;
                    $scope.autoConnectWebsiteLoading = false;
                    form.$setPristine();
                });
        };
    });
