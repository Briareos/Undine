interface ConnectWebsiteUrlScope extends ng.IScope {
    urlForm:ConnectWebsiteUrlForm
    urlFormSubmit(form:ConnectWebsiteUrlForm)
    urlFormData:ConnectWebsiteUrlFormData
    urlFormLoading:boolean
}

interface ConnectWebsiteUrlForm extends ng.IFormController {
    url:ng.INgModelController
}

interface ConnectWebsiteUrlFormData {
    url:string
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

interface FtpCredentialsForm extends ng.IFormController {
    method:ng.INgModelController
    username:ng.INgModelController
    password:ng.INgModelController
    host:ng.INgModelController
    port:ng.INgModelController
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
    }
    newClick()
    newFormSubmit()
}

angular.module('undine.dashboard')
    .controller('ConnectWebsiteUrlController', function (Api:Api, $scope:ConnectWebsiteUrlScope, $state:ng.ui.IStateService) {
    $scope.urlFormData = {
        url: ''
    };

    $scope.urlFormSubmit = function (form:ConnectWebsiteUrlForm) {
        if (!form.$valid) {
            return;
        }
        var siteUrl:string = $scope.urlFormData.url;
        if (!siteUrl.match(/^https?:\/\//)) {
            // Make sure the URL starts with a scheme.
            siteUrl = 'http://' + siteUrl.replace(/^:?\/+/, '');
        }
        $scope.urlFormLoading = true;
        Api.siteConnect(siteUrl, true)
            .success(function (result:SiteConnectResult) {
            $state.go('siteDashboard', {uid: result.site.uid});
        })
            .error(function (constraint:Constraint) {
            if (constraint instanceof AlreadyConnectedConstraint) {
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
    .controller('ReconnectWebsiteController', function ($scope:ReconnectWebsiteScope, Api:Api, $state:ng.ui.IStateService, url:string, lookedForLoginForm:boolean, loginFormFound:boolean) {
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

    $scope.reconnectClick = function () {
        $scope.connectWebsiteErrors.stillConnected = false;
        $scope.connectWebsiteActive = true;
        $scope.connectWebsiteLoading = true;
        Api.siteConnect(url)
            .success(function (result:SiteConnectResult) {
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
        Api.siteConnect(url, true, null, null, $scope.reconnectFormData.username, $scope.reconnectFormData.password)
            .success(function (result:SiteConnectResult) {
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
    .controller('ConnectWebsiteNewController', function ($scope:ConnectWebsiteNewScope, $state:ng.ui.IStateService, AppData:AppData, Api:Api, url:string, lookedForLoginForm:boolean, loginFormFound:boolean) {
    $scope.url = url;
    $scope.updatesUrl = url.replace(/\/?$/, '/?q=admin/modules/install');
    $scope.oxygenZipUrl = AppData.oxygenZipUrl;
    $scope.lookedForLoginForm = lookedForLoginForm;
    $scope.loginFormFound = loginFormFound;
    $scope.connectWebsiteLoading = false;
    $scope.autoConnectWebsiteLoading = false;
    $scope.connectWebsiteActive = false;
    $scope.ftpFormFound = false;
    $scope.connectWebsiteErrors = {
        stillDisabled: false,
        invalidCredentials: false
    };
    $scope.newFormData = {
        username: '',
        password: ''
    }

    $scope.newClick = function () {
        $scope.connectWebsiteErrors.stillDisabled = false;
        $scope.connectWebsiteActive = true;
        $scope.connectWebsiteLoading = true;
        Api.siteConnect(url)
            .success(function (result:SiteConnectResult) {
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
        $scope.connectWebsiteErrors.invalidCredentials = false;
        $scope.connectWebsiteActive = true;
        $scope.autoConnectWebsiteLoading = true;
        Api.siteConnect(url, true, null, null, $scope.newFormData.username, $scope.newFormData.password)
            .success(function (result:SiteConnectResult) {
            $state.go('siteDashboard', {uid: result.site.uid});
        })
            .error(function (constraint:Constraint) {
            if (constraint instanceof InvalidCredentialsConstraint) {
                $scope.connectWebsiteErrors.invalidCredentials = true;
                return;
            } else if (constraint instanceof FtpCredentialsRequiredConstraint) {
                $scope.ftpFormFound = true;
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
