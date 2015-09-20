interface ConnectWebsiteNewScope extends ng.IScope {
    newForm:ConnectWebsiteNewForm
    newFormSubmit(form:ConnectWebsiteNewForm)
    newFormData:{
        url:string
    }
    newFormLoading:boolean
}

interface ConnectWebsiteInstructionsScope extends ng.IScope {
    url:string
    lookedForLoginForm:boolean
    loginFormFound:boolean
    alreadyConnected:boolean
}

interface ConnectWebsiteNewForm extends ng.IFormController {
    url:ng.IFormController
}

interface ConnectWebsiteAdminCredentialsScope extends ng.IScope {
}

angular.module('undine.dashboard')
    .controller('ConnectWebsiteNewController', function (Api:Api, $scope:ConnectWebsiteNewScope, $state:ng.ui.IStateService) {
    $scope.newFormData = {
        url: ''
    };

    $scope.newFormSubmit = function (form:ConnectWebsiteNewForm) {
        if (!form.$valid) {
            return;
        }
        var siteUrl:string = $scope.newFormData.url;
        if (!siteUrl.match(/^https?:\/\//)) {
            // Make sure the URL starts with a scheme.
            siteUrl = 'http://' + siteUrl.replace(/^:?\/+/, '');
        }
        $scope.newFormLoading = true;
        Api.siteConnect(siteUrl, true)
            .success(function (result:SiteConnectResult) {
            $state.go('siteDashboard', {uid: result.site.uid});
        })
            .error(function (constraint:Constraint) {
            if (constraint instanceof AlreadyConnectedConstraint) {
                $state.go('^.instructions', {
                    url: siteUrl,
                    lookedForLoginForm: constraint.lookedForLoginForm,
                    loginFormFound: constraint.loginFormFound,
                    alreadyConnected: true
                });
                return;
            }
            $state.go('^.instructions', {url: $scope.newFormData.url});
        })
            .finally(function () {
            $scope.newFormLoading = false;
        })
    };
})
    .controller('ConnectWebsiteInstructionsController', function ($scope:ConnectWebsiteInstructionsScope, url:string, lookedForLoginForm:boolean, loginFormFound:boolean, alreadyConnected:boolean) {
    $scope.url = url;
    $scope.lookedForLoginForm = lookedForLoginForm;
    $scope.loginFormFound = loginFormFound;
    $scope.alreadyConnected = alreadyConnected;
})
    .controller('ConnectWebsiteAdminCredentialsController', function ($scope:ConnectWebsiteAdminCredentialsScope) {

});
