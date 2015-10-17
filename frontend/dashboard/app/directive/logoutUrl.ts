angular.module('undine.dashboard')
    .directive('logoutUrl', function (AppData: AppData): ng.IDirective {
        return {
            restrict: 'A',
            link: function (scope: ng.IScope, element: ng.IAugmentedJQuery): void {
                element.attr('href', AppData.logoutUrl);
            }
        };
    });
