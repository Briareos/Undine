/**
 * Toggles the "active" class to an element after a successful change of state.
 * The element must have the "ui-sref" attribute for it to work.
 */
angular.module('undine.dashboard')
    .directive('uiSrefActive', function () {
        return {
            restrict: 'A',
            scope: {
                uiSref: '@'
            },
            link: function (scope, element, attr) {
                scope.$on('$stateChangeSuccess', function (event, state) {
                    if (state.name === attr.uiSref) {
                        element.addClass('active');
                    } else {
                        element.removeClass('active');
                    }
                });
            }
        };
    });
