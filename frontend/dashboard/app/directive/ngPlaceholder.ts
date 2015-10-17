angular.module('undine.dashboard')
    .directive('ngPlaceholder', function (): ng.IDirective {
        return {
            restrict: 'A',
            scope: {
                ngPlaceholder: '='
            },
            link: function (scope: ng.IScope, element: ng.IAugmentedJQuery): void {
                scope.$watch('ngPlaceholder', function (newValue: string): void {
                    let inputElement: HTMLInputElement = <HTMLInputElement>element[0];
                    inputElement.placeholder = newValue;
                });
            }
        };
    });
