angular.module('undine.dashboard')
    .directive('autofocus', [function () {
    return {
        restrict: 'A',
        link: function (scope:ng.IScope, element:ng.IAugmentedJQuery) {
            setTimeout(function () {
                element.select();
            });
        }
    };
}]);
