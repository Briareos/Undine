angular.module('undine.dashboard')
    .directive('logoutUrl', function (AppData) {
        return {
            restrict: 'A',
            link: function (scope:ng.IScope, element:ng.IAugmentedJQuery) {
                element.attr('href', AppData.logoutUrl);

            }
        }
    });
