angular.module('undine.dashboard')
    .directive('sitePicker', function (SitePicker:SitePicker, Dashboard:DashboardInterface) {
        return {
            scope: true,
            replace: true,
            templateUrl: '/component/sitePicker/sitePicker.html',
            link: function (scope:ng.IScope, $element:ng.IAugmentedJQuery) {
                scope['sites'] = Dashboard.sites;
                scope.$watch(function () {
                    return SitePicker.visible;
                }, function () {
                    if (SitePicker.visible) {
                        $element.show();
                    } else {
                        $element.hide();
                    }
                });
            }
        };
    });
