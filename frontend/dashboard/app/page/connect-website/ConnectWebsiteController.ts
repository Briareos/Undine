interface ConnectWebsiteNewScope extends ng.IScope {
    newForm:ConnectWebsiteNewForm
    newFormSubmit(form:ConnectWebsiteNewForm)
    newFormData:{
        url:string
    }
}

interface ConnectWebsiteInstructionsScope extends ng.IScope {
    instructionsData:{
        url:string
    }
}

interface ConnectWebsiteNewForm extends ng.IFormController {
    url:ng.IFormController
}

angular.module('undine.dashboard')
    .controller('ConnectWebsiteNewController', function ($scope:ConnectWebsiteNewScope, $state:ng.ui.IStateService) {
    $scope.newFormData = {
        url: ''
    };

    $scope.newFormSubmit = function (form:ConnectWebsiteNewForm) {
        if (!form.$valid) {
            return;
        }
        $state.go('^.instructions', {url: $scope.newFormData.url});
    };
})
    .controller('ConnectWebsiteInstructionsController', function ($scope:ConnectWebsiteInstructionsScope, url:string) {
    $scope.instructionsData = {
        url: url
    };
});
