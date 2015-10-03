angular.module('undine.dashboard')
    .directive('ngPlaceholder', function (AppData) {
        return {
            restrict: 'A',
            scope: {
                ngPlaceholder: '='
            },
            link: function (scope:ng.IScope, element:ng.IAugmentedJQuery) {
                scope.$watch('ngPlaceholder', function (newValue:string) {
                    var inputElement = <HTMLInputElement>element[0];
                    inputElement.placeholder = newValue;
                });
            }
        };
    });
