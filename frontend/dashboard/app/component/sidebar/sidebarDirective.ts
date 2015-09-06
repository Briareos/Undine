angular.module('undine.dashboard')
    .directive('sidebar', function (Sidebar:Sidebar) {
        return {
            scope: true,
            replace: true,
            templateUrl: '/component/sidebar/sidebar.html',
            link: function (scope:ng.IScope, $element:ng.IAugmentedJQuery) {

            }
        };
    });