angular.module('undine.dashboard')
    .directive('sitePicker', function () {
        return {
            scope: true,
            replace: true,
            templateUrl: '/component/sitePicker/sitePicker.html',
            link: function (scope:ng.IScope, $element:ng.IAugmentedJQuery) {

            }
        };
    });