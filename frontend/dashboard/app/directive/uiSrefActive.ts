/**
 * Toggles the "active" class to an element after a successful change of state.
 * The element must have the "ui-sref" attribute for it to work.
 */
angular.module('undine.dashboard')
    .directive('uiSrefActive', function (): ng.IDirective {
        return {
            restrict: 'A',
            scope: {
                uiSref: '@'
            },
            link: function (scope: ng.IScope, element: ng.IAugmentedJQuery, attr: any): void {
                scope.$on('$stateChangeSuccess', function (event: any, state: ng.ui.IState): void {
                    if (state.name === attr.uiSref) {
                        element.addClass('active');
                    } else {
                        element.removeClass('active');
                    }
                });
            }
        };
    });
