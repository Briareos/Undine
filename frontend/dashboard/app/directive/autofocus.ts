angular.module('undine.dashboard')
    .directive('autofocus', function (): ng.IDirective {
        return {
            restrict: 'A',
            link: function (scope: ng.IScope, element: ng.IAugmentedJQuery): void {
                // Don't use $timeout, don't trigger digest.
                setTimeout(function (): void {
                    element.select();
                });
            }
        };
    });
